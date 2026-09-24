<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\ItemType;
use App\Enums\PaymentStatus;
use App\Enums\QuantityUnit;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(
        private StockService $stock,
        private PaymentService $payments,
        private DocumentNumberService $numbers,
    ) {}

    /**
     * @param  array{supplier_id:int, item_id:int, purchase_date:string, input_quantity:float, input_unit:string|QuantityUnit,
     *     price_per_kg:float, other_cost?:float, moisture_percent?:?float, pay_now?:float, payment_method?:string, due_date?:?string, notes?:?string}  $data
     */
    public function create(array $data, User $user): Purchase
    {
        $date = Carbon::parse($data['purchase_date']);
        $unit = QuantityUnit::from($data['input_unit'] instanceof QuantityUnit ? $data['input_unit']->value : $data['input_unit']);
        $quantity = round((float) $data['input_quantity'], 2);
        $price = round((float) $data['price_per_kg'], 2);
        $otherCost = round((float) ($data['other_cost'] ?? 0), 2);
        $payNow = round((float) ($data['pay_now'] ?? 0), 2);

        $errors = [];
        if ($date->isAfter(today())) {
            $errors['purchase_date'] = 'Tanggal pembelian tidak boleh di masa depan.';
        }
        if ($quantity <= 0) {
            $errors['input_quantity'] = 'Jumlah harus lebih dari 0.';
        }
        if ($price <= 0) {
            $errors['price_per_kg'] = 'Harga per kg harus lebih dari 0.';
        }
        if ($otherCost < 0) {
            $errors['other_cost'] = 'Ongkos lain tidak boleh negatif.';
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return DB::transaction(function () use ($data, $user, $date, $unit, $quantity, $price, $otherCost, $payNow) {
            $item = Item::findOrFail($data['item_id']);
            if ($item->type !== ItemType::Raw) {
                throw ValidationException::withMessages(['item_id' => 'Pembelian hanya untuk bahan baku (gabah).']);
            }

            // Hitung ulang di server, jangan percaya nilai kg dari form.
            $kg = $item->toKg($quantity, $unit->value);
            $subtotal = round($kg * $price, 2);
            $total = round($subtotal + $otherCost, 2);

            if ($payNow < 0 || $payNow > $total) {
                throw ValidationException::withMessages(['pay_now' => 'Bayar sekarang harus antara 0 dan total.']);
            }
            if ($payNow < $total && empty($data['due_date'])) {
                throw ValidationException::withMessages(['due_date' => 'Jatuh tempo wajib diisi bila belum lunas.']);
            }

            $purchase = Purchase::create([
                'number' => $this->numbers->next('PB', $date),
                'purchase_date' => $date,
                'supplier_id' => $data['supplier_id'],
                'item_id' => $item->id,
                'input_quantity' => $quantity,
                'input_unit' => $unit,
                'quantity_kg' => $kg,
                'moisture_percent' => $data['moisture_percent'] ?? null,
                'price_per_kg' => $price,
                'subtotal' => $subtotal,
                'other_cost' => $otherCost,
                'total' => $total,
                'paid_amount' => 0,
                'payment_status' => PaymentStatus::Unpaid,
                'due_date' => $payNow < $total ? $data['due_date'] : null,
                'notes' => $data['notes'] ?? null,
                'status' => DocumentStatus::Posted,
                'user_id' => $user->id,
            ]);

            // Ongkos angkut/bongkar ikut menjadi biaya gabah.
            $costPerKg = $total / $kg;
            $this->stock->increase($item, $kg, $costPerKg, $purchase, $date, 'Pembelian '.$purchase->number);

            if ($payNow > 0) {
                $this->payments->record($purchase, [
                    'amount' => $payNow,
                    'payment_date' => $date->toDateString(),
                    'method' => $data['payment_method'] ?? null,
                ], $user);
            }

            // Harga pasar terbaru untuk isi awal form berikutnya.
            $item->update(['default_price_per_kg' => $price]);

            return $purchase->refresh();
        });
    }

    public function void(Purchase $purchase, string $reason, User $user): void
    {
        DB::transaction(function () use ($purchase, $reason, $user) {
            $purchase = $purchase->lockForVoid($reason);
            $item = Item::lockForUpdate()->findOrFail($purchase->item_id);

            if ((float) $item->stock_kg < (float) $purchase->quantity_kg) {
                throw ValidationException::withMessages([
                    'reason' => sprintf(
                        'Pembelian tidak bisa dibatalkan: stok %s tinggal %s kg, sedangkan pembelian ini %s kg (gabah sudah terpakai untuk produksi).',
                        $item->name,
                        number_format((float) $item->stock_kg, 2, ',', '.'),
                        number_format((float) $purchase->quantity_kg, 2, ',', '.'),
                    ),
                ]);
            }

            $this->stock->reverse($purchase, $reason);
            $this->payments->voidAllFor($purchase, 'Pembelian dibatalkan: '.$reason, $user);
            $purchase->markVoid($reason, $user);
        });
    }
}
