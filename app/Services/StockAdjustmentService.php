<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\Item;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockAdjustmentService
{
    public function __construct(
        private StockService $stock,
        private DocumentNumberService $numbers,
    ) {}

    /**
     * Stock opname: catat stok fisik, selisih dengan sistem jadi mutasi masuk/keluar.
     *
     * @param  array{item_id:int, physical_kg:float, reason:string, adjustment_date?:string}  $data
     */
    public function create(array $data, User $user): StockAdjustment
    {
        $reason = trim($data['reason'] ?? '');
        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => 'Alasan penyesuaian wajib diisi.']);
        }

        $physical = round((float) $data['physical_kg'], 2);
        if ($physical < 0) {
            throw ValidationException::withMessages(['physical_kg' => 'Stok fisik tidak boleh negatif.']);
        }

        $date = Carbon::parse($data['adjustment_date'] ?? today());

        return DB::transaction(function () use ($data, $user, $physical, $reason, $date) {
            $item = Item::lockForUpdate()->findOrFail($data['item_id']);
            $system = (float) $item->stock_kg;
            $difference = round($physical - $system, 2);

            $adjustment = StockAdjustment::create([
                'number' => $this->numbers->next('SO', $date),
                'adjustment_date' => $date,
                'item_id' => $item->id,
                'system_kg' => $system,
                'physical_kg' => $physical,
                'difference_kg' => $difference,
                'reason' => $reason,
                'status' => DocumentStatus::Posted,
                'user_id' => $user->id,
            ]);

            $desc = 'Stock opname: '.mb_substr($reason, 0, 200);

            if ($difference > 0) {
                // Selisih lebih dinilai dengan biaya rata-rata saat ini (avg tidak berubah).
                $this->stock->increase($item, $difference, (float) $item->avg_cost_per_kg, $adjustment, $date, $desc);
            } elseif ($difference < 0) {
                $this->stock->decrease($item, abs($difference), $adjustment, $date, $desc);
            }

            return $adjustment;
        });
    }

    public function void(StockAdjustment $adjustment, string $reason, User $user): void
    {
        DB::transaction(function () use ($adjustment, $reason, $user) {
            $adjustment = $adjustment->lockForVoid($reason);

            try {
                $this->stock->reverse($adjustment, $reason);
            } catch (InsufficientStockException $e) {
                throw ValidationException::withMessages([
                    'reason' => 'Opname tidak bisa dibatalkan karena stok sudah terpakai. '.$e->getMessage(),
                ]);
            }
            $adjustment->markVoid($reason, $user);
        });
    }
}
