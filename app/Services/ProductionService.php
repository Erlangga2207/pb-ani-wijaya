<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\ItemType;
use App\Models\Item;
use App\Models\Production;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionService
{
    public function __construct(
        private StockService $stock,
        private DocumentNumberService $numbers,
    ) {}

    /**
     * @param  array{production_date:string, input_item_id:int, input_kg:float, outputs:array<int|string,float|string|null>,
     *     machine_downtime_minutes?:?int, notes?:?string}  $data  outputs = [item_id => kg]
     */
    public function create(array $data, User $user): Production
    {
        $date = Carbon::parse($data['production_date']);
        $inputKg = round((float) $data['input_kg'], 2);

        if ($date->isAfter(today())) {
            throw ValidationException::withMessages(['production_date' => 'Tanggal produksi tidak boleh di masa depan.']);
        }
        if ($inputKg <= 0) {
            throw ValidationException::withMessages(['input_kg' => 'Jumlah gabah harus lebih dari 0.']);
        }

        return DB::transaction(function () use ($data, $user, $date, $inputKg) {
            $input = Item::lockForUpdate()->findOrFail($data['input_item_id']);
            if ($input->type !== ItemType::Raw) {
                throw ValidationException::withMessages(['input_item_id' => 'Bahan giling harus bahan baku (gabah).']);
            }

            [$main, $outputs] = $this->validatedOutputs($data['outputs'] ?? [], $inputKg);
            $totalOutputKg = round(array_sum(array_column($outputs, 'kg')), 2);

            $inputCostPerKg = (float) $input->avg_cost_per_kg;
            $inputTotalCost = round($inputKg * $inputCostPerKg, 2);

            // Metode by-product: seluruh biaya gabah dibebankan ke beras (HPP hasil samping = 0).
            // Penjualan menir/dedak/sekam tetap tercatat sebagai omzet. Penyederhanaan wajar untuk UMKM.
            $hppPerKg = round($inputTotalCost / $main['kg'], 2);

            $production = Production::create([
                'number' => $this->numbers->next('PR', $date),
                'production_date' => $date,
                'input_item_id' => $input->id,
                'input_kg' => $inputKg,
                'input_cost_per_kg' => $inputCostPerKg,
                'input_total_cost' => $inputTotalCost,
                'main_output_kg' => $main['kg'],
                // Rendemen = beras ÷ gabah × 100%
                'yield_percent' => round($main['kg'] / $inputKg * 100, 2),
                // Susut = gabah − Σ semua hasil
                'shrinkage_kg' => round($inputKg - $totalOutputKg, 2),
                'machine_downtime_minutes' => $data['machine_downtime_minutes'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => DocumentStatus::Posted,
                'user_id' => $user->id,
            ]);

            $desc = 'Produksi '.$production->number;
            $this->stock->decrease($input, $inputKg, $production, $date, $desc);

            foreach ($outputs as $output) {
                $unitCost = $output['item']->type === ItemType::Finished ? $hppPerKg : 0;
                $production->outputs()->create([
                    'item_id' => $output['item']->id,
                    'quantity_kg' => $output['kg'],
                    'unit_cost' => $unitCost,
                ]);
                $this->stock->increase($output['item'], $output['kg'], $unitCost, $production, $date, $desc);
            }

            return $production->load('outputs');
        });
    }

    public function void(Production $production, string $reason, User $user): void
    {
        DB::transaction(function () use ($production, $reason, $user) {
            $production = $production->lockForVoid($reason);

            foreach ($production->outputs()->with('item')->get() as $output) {
                $item = Item::lockForUpdate()->findOrFail($output->item_id);
                if ((float) $item->stock_kg < (float) $output->quantity_kg) {
                    throw ValidationException::withMessages([
                        'reason' => sprintf(
                            'Produksi tidak bisa dibatalkan: stok %s tinggal %s kg, sedangkan hasil produksi ini %s kg (sudah terjual/terpakai).',
                            $item->name,
                            number_format((float) $item->stock_kg, 2, ',', '.'),
                            number_format((float) $output->quantity_kg, 2, ',', '.'),
                        ),
                    ]);
                }
            }

            $this->stock->reverse($production, $reason);
            $production->markVoid($reason, $user);
        });
    }

    /**
     * @return array{0: array{item: Item, kg: float}, 1: list<array{item: Item, kg: float}>}
     */
    private function validatedOutputs(array $raw, float $inputKg): array
    {
        $outputs = [];
        foreach ($raw as $itemId => $kg) {
            $kg = round((float) $kg, 2);
            if ($kg < 0) {
                throw ValidationException::withMessages(['outputs' => 'Hasil giling tidak boleh negatif.']);
            }
            if ($kg == 0) {
                continue; // baris 0 tidak disimpan
            }

            $item = Item::findOrFail($itemId);
            if (! in_array($item->type, [ItemType::Finished, ItemType::Byproduct], true)) {
                throw ValidationException::withMessages(['outputs' => "{$item->name} bukan hasil giling."]);
            }
            $outputs[] = ['item' => $item, 'kg' => $kg];
        }

        $main = array_values(array_filter($outputs, fn ($o) => $o['item']->type === ItemType::Finished));
        if (count($main) !== 1) {
            throw ValidationException::withMessages(['outputs' => 'Wajib ada tepat satu hasil beras (produk jadi) dengan berat lebih dari 0.']);
        }

        if (round(array_sum(array_column($outputs, 'kg')), 2) > $inputKg) {
            throw ValidationException::withMessages(['outputs' => 'Total hasil giling tidak boleh lebih berat dari gabah yang digiling.']);
        }

        return [$main[0], $outputs];
    }
}
