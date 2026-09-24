<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyStock extends Command
{
    protected $signature = 'stock:verify';

    protected $description = 'Bandingkan cache items.stock_kg dengan saldo buku besar (Σ masuk − Σ keluar)';

    public function handle(): int
    {
        $ledger = DB::table('stock_movements')
            ->selectRaw("item_id, SUM(CASE WHEN direction = 'in' THEN quantity_kg ELSE -quantity_kg END) AS balance")
            ->groupBy('item_id')
            ->pluck('balance', 'item_id');

        $rows = [];
        $mismatch = 0;

        foreach (Item::orderBy('code')->get() as $item) {
            $ledgerKg = round((float) ($ledger[$item->id] ?? 0), 2);
            $diff = round((float) $item->stock_kg - $ledgerKg, 2);
            $mismatch += $diff != 0 ? 1 : 0;
            $rows[] = [$item->code, $item->name, $item->stock_kg, number_format($ledgerKg, 2, '.', ''), number_format($diff, 2, '.', '')];
        }

        $this->table(['Kode', 'Barang', 'Stok (cache)', 'Stok (ledger)', 'Selisih'], $rows);

        if ($mismatch > 0) {
            $this->error("{$mismatch} barang memiliki selisih.");

            return self::FAILURE;
        }

        $this->info('Semua stok cocok dengan buku besar (0 selisih).');

        return self::SUCCESS;
    }
}
