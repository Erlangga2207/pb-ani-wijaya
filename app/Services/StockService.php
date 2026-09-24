<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Item;
use App\Models\StockMovement;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

/**
 * Satu-satunya pintu perubahan stok. Setiap perubahan:
 * - dijalankan di dalam DB::transaction milik service pemanggil,
 * - mengunci baris item (lockForUpdate) agar input bersamaan tidak membuat stok minus,
 * - mencatat satu baris di buku besar stock_movements.
 */
class StockService
{
    public function increase(Item $item, float $kg, ?float $unitCost, Model $source, CarbonInterface $date, ?string $desc = null): StockMovement
    {
        return $this->write($item, 'in', $kg, $unitCost, $source, $date, $desc);
    }

    public function decrease(Item $item, float $kg, Model $source, CarbonInterface $date, ?string $desc = null): StockMovement
    {
        return $this->write($item, 'out', $kg, null, $source, $date, $desc);
    }

    /**
     * Untuk void: buat mutasi kebalikan dari semua mutasi milik $source, memakai unit_cost aslinya.
     * Keterbatasan yang diterima: avg cost tidak dihitung ulang mundur.
     */
    public function reverse(Model $source, string $reason): void
    {
        $movements = StockMovement::query()
            ->where('source_type', $source->getMorphClass())
            ->where('source_id', $source->getKey())
            ->orderByDesc('id')
            ->get();

        $desc = trim('Void '.($source->number ?? '').': '.$reason);

        foreach ($movements as $movement) {
            $this->write(
                $movement->item,
                $movement->direction === 'in' ? 'out' : 'in',
                (float) $movement->quantity_kg,
                $movement->unit_cost === null ? null : (float) $movement->unit_cost,
                $source,
                now(),
                mb_substr($desc, 0, 255),
            );
        }
    }

    private function write(Item $item, string $direction, float $kg, ?float $unitCost, Model $source, CarbonInterface $date, ?string $desc): StockMovement
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException('StockService harus dipanggil di dalam DB::transaction.');
        }

        $kg = round($kg, 2);
        if ($kg <= 0) {
            throw new InvalidArgumentException('Jumlah kg harus lebih dari 0.');
        }

        $item = Item::lockForUpdate()->findOrFail($item->id);
        $stock = (float) $item->stock_kg;
        $avg = (float) $item->avg_cost_per_kg;

        if ($direction === 'in') {
            if ($unitCost !== null) {
                // Moving average: (stok_lama × avg_lama + kg × biaya) ÷ (stok_lama + kg)
                $avg = $stock <= 0 ? $unitCost : ($stock * $avg + $kg * $unitCost) / ($stock + $kg);
                $item->avg_cost_per_kg = round($avg, 2);
            }
            $balance = round($stock + $kg, 2);
        } else {
            if ($stock < $kg) {
                throw new InsufficientStockException(sprintf(
                    'Stok %s tidak cukup. Tersedia %s kg, diminta %s kg',
                    $item->name,
                    number_format($stock, 2, ',', '.'),
                    number_format($kg, 2, ',', '.'),
                ));
            }
            // Keluar dicatat dengan biaya rata-rata saat itu (dasar HPP), kecuali reverse memberi biaya asli.
            $unitCost ??= $avg;
            $balance = round($stock - $kg, 2);
        }

        $item->stock_kg = $balance;
        $item->save();

        return StockMovement::create([
            'item_id' => $item->id,
            'movement_date' => $date->toDateString(),
            'direction' => $direction,
            'quantity_kg' => $kg,
            'unit_cost' => $unitCost === null ? $avg : round($unitCost, 2),
            'balance_after_kg' => $balance,
            'source_type' => $source->getMorphClass(),
            'source_id' => $source->getKey(),
            'description' => $desc,
            'user_id' => auth()->id(),
        ]);
    }
}
