<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\ItemType;
use App\Enums\PaymentStatus;
use App\Enums\QuantityUnit;
use App\Models\Item;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(
        private StockService $stock,
        private PaymentService $payments,
        private DocumentNumberService $numbers,
    ) {}

    /**
     * @param  array{sale_date:string, buyer_id:int, items:list<array{item_id:int, input_quantity:float, input_unit:string|QuantityUnit, price_per_kg:float}>,
     *     discount?:float, shipping_cost?:float, pay_now?:float, payment_method?:string, due_date?:?string, notes?:?string}  $data
     */
    public function create(array $data, User $user): Sale
    {
        $date = Carbon::parse($data['sale_date']);
        $discount = round((float) ($data['discount'] ?? 0), 2);
        $shipping = round((float) ($data['shipping_cost'] ?? 0), 2);
        $payNow = round((float) ($data['pay_now'] ?? 0), 2);
        $lines = array_values($data['items'] ?? []);

        if ($date->isAfter(today())) {
            throw ValidationException::withMessages(['sale_date' => 'Tanggal penjualan tidak boleh di masa depan.']);
        }
        if ($lines === []) {
            throw ValidationException::withMessages(['items' => 'Minimal satu barang.']);
        }
        if (count(array_unique(array_column($lines, 'item_id'))) !== count($lines)) {
            throw ValidationException::withMessages(['items' => 'Barang tidak boleh dobel dalam satu nota.']);
        }
        if ($discount < 0 || $shipping < 0) {
            throw ValidationException::withMessages(['discount' => 'Diskon dan ongkir tidak boleh negatif.']);
        }

        return DB::transaction(function () use ($data, $user, $date, $discount, $shipping, $payNow, $lines) {
            // Hitung ulang kg & total baris di server.
            $prepared = [];
            foreach ($lines as $line) {
                $item = Item::findOrFail($line['item_id']);
                if ($item->type === ItemType::Raw) {
                    throw ValidationException::withMessages(['items' => "{$item->name} (bahan baku) tidak bisa dijual lewat penjualan."]);
                }

                $unit = QuantityUnit::from($line['input_unit'] instanceof QuantityUnit ? $line['input_unit']->value : $line['input_unit']);
                $quantity = round((float) $line['input_quantity'], 2);
                $price = round((float) $line['price_per_kg'], 2);
                if ($quantity <= 0 || $price <= 0) {
                    throw ValidationException::withMessages(['items' => "Jumlah dan harga {$item->name} harus lebih dari 0."]);
                }

                $kg = $item->toKg($quantity, $unit->value);
                $prepared[] = compact('item', 'unit', 'quantity', 'price', 'kg') + ['line_total' => round($kg * $price, 2)];
            }

            $subtotal = round(array_sum(array_column($prepared, 'line_total')), 2);
            if ($discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Diskon tidak boleh melebihi subtotal.']);
            }
            $total = round($subtotal - $discount + $shipping, 2);

            if ($payNow < 0 || $payNow > $total) {
                throw ValidationException::withMessages(['pay_now' => 'Bayar sekarang harus antara 0 dan total.']);
            }
            if ($payNow < $total && empty($data['due_date'])) {
                throw ValidationException::withMessages(['due_date' => 'Jatuh tempo wajib diisi bila belum lunas.']);
            }

            $sale = Sale::create([
                'number' => $this->numbers->next('PJ', $date),
                'sale_date' => $date,
                'buyer_id' => $data['buyer_id'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shipping,
                'total' => $total,
                'cogs_total' => 0,
                'paid_amount' => 0,
                'payment_status' => PaymentStatus::Unpaid,
                'due_date' => $payNow < $total ? $data['due_date'] : null,
                'notes' => $data['notes'] ?? null,
                'status' => DocumentStatus::Posted,
                'user_id' => $user->id,
            ]);

            $cogsTotal = 0;
            foreach ($prepared as $line) {
                $movement = $this->stock->decrease($line['item'], $line['kg'], $sale, $date, 'Penjualan '.$sale->number);
                // HPP per kg = biaya rata-rata barang saat keluar.
                $cogsPerKg = (float) $movement->unit_cost;
                $cogsTotal += round($line['kg'] * $cogsPerKg, 2);

                $sale->items()->create([
                    'item_id' => $line['item']->id,
                    'input_quantity' => $line['quantity'],
                    'input_unit' => $line['unit'],
                    'quantity_kg' => $line['kg'],
                    'price_per_kg' => $line['price'],
                    'line_total' => $line['line_total'],
                    'cogs_per_kg' => $cogsPerKg,
                ]);

                // Harga mengikuti pasar: simpan harga terakhir untuk isi awal form.
                $line['item']->update(['default_price_per_kg' => $line['price']]);
            }

            $sale->update(['cogs_total' => round($cogsTotal, 2)]);

            if ($payNow > 0) {
                $this->payments->record($sale, [
                    'amount' => $payNow,
                    'payment_date' => $date->toDateString(),
                    'method' => $data['payment_method'] ?? null,
                ], $user);
            }

            return $sale->refresh()->load('items');
        });
    }

    public function void(Sale $sale, string $reason, User $user): void
    {
        DB::transaction(function () use ($sale, $reason, $user) {
            $sale = $sale->lockForVoid($reason);

            $this->stock->reverse($sale, $reason);
            $this->payments->voidAllFor($sale, 'Penjualan dibatalkan: '.$reason, $user);
            $sale->markVoid($reason, $user);
        });
    }
}
