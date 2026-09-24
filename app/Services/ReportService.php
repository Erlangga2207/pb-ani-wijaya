<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\ExpenseCategory;
use App\Enums\PaymentStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Laporan. Semua angka hanya dari dokumen berstatus "posted" dan dihitung dengan agregat SQL.
 */
class ReportService
{
    public const TABS = [
        'pnl' => 'Laba Rugi',
        'sales' => 'Penjualan',
        'purchases' => 'Pembelian',
        'yield' => 'Rendemen',
        'stock' => 'Stok',
        'aging' => 'Utang/Piutang',
    ];

    // Tab yang boleh dilihat staff (tanpa angka biaya).
    public const STAFF_TABS = ['yield', 'stock'];

    public const PRESETS = [
        'today' => 'Hari ini',
        'this_week' => 'Minggu ini',
        'this_month' => 'Bulan ini',
        'last_month' => 'Bulan lalu',
        'season' => 'Musim ini (6 bln)',
        'this_year' => 'Tahun ini',
        'custom' => 'Custom',
    ];

    /** @return array{0: Carbon, 1: Carbon} */
    public static function period(string $preset, ?string $from = null, ?string $to = null): array
    {
        $today = today();

        return match ($preset) {
            'today' => [$today->copy(), $today->copy()],
            'this_week' => [$today->copy()->startOfWeek(), $today->copy()],
            'last_month' => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()],
            'season' => [$today->copy()->subMonthsNoOverflow(6)->addDay(), $today->copy()],
            'this_year' => [$today->copy()->startOfYear(), $today->copy()],
            'custom' => [Carbon::parse($from ?: $today->copy()->startOfMonth()), Carbon::parse($to ?: $today)],
            default => [$today->copy()->startOfMonth(), $today->copy()],
        };
    }

    public function profitAndLoss(CarbonInterface $from, CarbonInterface $to): array
    {
        $sales = $this->posted('sales', 'sale_date', $from, $to)
            ->selectRaw('COALESCE(SUM(subtotal - discount), 0) AS revenue, COALESCE(SUM(cogs_total), 0) AS cogs, COALESCE(SUM(shipping_cost), 0) AS shipping')
            ->first();

        $expenseRows = $this->posted('expenses', 'expense_date', $from, $to)
            ->selectRaw('category, SUM(amount) AS total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $expenses = [];
        foreach (ExpenseCategory::cases() as $category) {
            $expenses[$category->getLabel()] = round((float) ($expenseRows[$category->value] ?? 0), 2);
        }

        $revenue = round((float) $sales->revenue, 2);
        $cogs = round((float) $sales->cogs, 2);
        $gross = round($revenue - $cogs, 2);
        $totalExpenses = round(array_sum($expenses), 2);
        $net = round($gross - $totalExpenses, 2);

        return [
            'revenue' => $revenue, // penjualan barang setelah diskon (ongkir tidak termasuk)
            'cogs' => $cogs,
            'gross_profit' => $gross,
            'expenses' => $expenses,
            'total_expenses' => $totalExpenses,
            'net_profit' => $net,
            'margin_percent' => $revenue > 0 ? round($net / $revenue * 100, 2) : 0,
            'shipping' => round((float) $sales->shipping, 2),
        ];
    }

    public function salesSummary(CarbonInterface $from, CarbonInterface $to): array
    {
        $perDay = $this->posted('sales', 'sale_date', $from, $to)
            ->selectRaw('sale_date AS day, SUM(subtotal - discount) AS revenue, COUNT(*) AS count')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        $perItem = $this->posted('sales', 'sale_date', $from, $to, 'sales')
            ->join('sale_items', 'sale_items.sale_id', '=', 'sales.id')
            ->join('items', 'items.id', '=', 'sale_items.item_id')
            ->selectRaw('items.name, SUM(sale_items.quantity_kg) AS kg, SUM(sale_items.line_total) AS amount')
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('amount')
            ->get();

        $buyers = fn () => $this->posted('sales', 'sale_date', $from, $to, 'sales')
            ->join('buyers', 'buyers.id', '=', 'sales.buyer_id');

        $perBuyer = $buyers()
            ->selectRaw('buyers.name, buyers.city, COUNT(*) AS count, SUM(sales.subtotal - sales.discount) AS revenue')
            ->groupBy('buyers.id', 'buyers.name', 'buyers.city')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $perCity = $buyers()
            ->selectRaw("COALESCE(buyers.city, '-') AS city, COUNT(*) AS count, SUM(sales.subtotal - sales.discount) AS revenue")
            ->groupBy('buyers.city')
            ->orderByDesc('revenue')
            ->get();

        return [
            'per_day' => $perDay,
            'per_month' => $this->byMonth($perDay, ['revenue', 'count']),
            'per_item' => $perItem,
            'per_buyer' => $perBuyer,
            'per_city' => $perCity,
        ];
    }

    public function purchaseSummary(CarbonInterface $from, CarbonInterface $to): array
    {
        $suppliers = fn () => $this->posted('purchases', 'purchase_date', $from, $to, 'purchases')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id');

        $perSupplier = $suppliers()
            ->selectRaw('suppliers.name, suppliers.village, SUM(purchases.quantity_kg) AS kg, SUM(purchases.total) AS amount')
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.village')
            ->orderByDesc('kg')
            ->get();

        $perVillage = $suppliers()
            ->selectRaw("COALESCE(suppliers.village, '-') AS village, COUNT(DISTINCT suppliers.id) AS farmers, SUM(purchases.quantity_kg) AS kg, SUM(purchases.total) AS amount")
            ->groupBy('suppliers.village')
            ->orderByDesc('kg')
            ->get();

        $perDay = $this->posted('purchases', 'purchase_date', $from, $to)
            ->selectRaw('purchase_date AS day, SUM(quantity_kg) AS kg, SUM(subtotal) AS subtotal')
            ->groupBy('purchase_date')
            ->get();

        // Harga rata-rata/kg per bulan = Σ nilai gabah ÷ Σ kg (tertimbang).
        $perMonth = $this->byMonth($perDay, ['kg', 'subtotal'])
            ->map(fn ($row) => $row + ['avg_price' => $row['kg'] > 0 ? round($row['subtotal'] / $row['kg'], 2) : 0]);

        return ['per_supplier' => $perSupplier, 'per_village' => $perVillage, 'per_month' => $perMonth];
    }

    public function yieldSummary(CarbonInterface $from, CarbonInterface $to): array
    {
        $batches = $this->posted('productions', 'production_date', $from, $to)
            ->select('number', 'production_date', 'input_kg', 'main_output_kg', 'yield_percent', 'shrinkage_kg', 'machine_downtime_minutes')
            ->orderBy('production_date')
            ->orderBy('id')
            ->get();

        $totals = $this->posted('productions', 'production_date', $from, $to)
            ->selectRaw('COALESCE(SUM(input_kg), 0) AS input_kg, COALESCE(SUM(main_output_kg), 0) AS output_kg, COALESCE(SUM(shrinkage_kg), 0) AS shrinkage_kg, COALESCE(SUM(machine_downtime_minutes), 0) AS downtime')
            ->first();

        return [
            'batches' => $batches,
            'input_kg' => round((float) $totals->input_kg, 2),
            'output_kg' => round((float) $totals->output_kg, 2),
            // Rata-rata tertimbang: Σ beras ÷ Σ gabah (bukan rata-rata persen biasa).
            'weighted_yield' => (float) $totals->input_kg > 0 ? round($totals->output_kg / $totals->input_kg * 100, 2) : 0,
            'shrinkage_kg' => round((float) $totals->shrinkage_kg, 2),
            'downtime_minutes' => (int) $totals->downtime,
        ];
    }

    // Saldo per barang pada tanggal $to dihitung dari buku besar.
    public function stockSummary(CarbonInterface $to): array
    {
        $rows = DB::table('items')
            ->leftJoin('stock_movements', function ($join) use ($to) {
                $join->on('stock_movements.item_id', '=', 'items.id')
                    ->where('stock_movements.movement_date', '<', $to->copy()->addDay()->toDateString());
            })
            ->selectRaw("items.code, items.name, items.avg_cost_per_kg, items.weight_per_sack_kg,
                COALESCE(SUM(CASE WHEN stock_movements.direction = 'in' THEN stock_movements.quantity_kg ELSE -stock_movements.quantity_kg END), 0) AS balance")
            ->where('items.is_active', true)
            ->groupBy('items.id', 'items.code', 'items.name', 'items.avg_cost_per_kg', 'items.weight_per_sack_kg')
            ->orderBy('items.id')
            ->get()
            // Nilai persediaan memakai biaya rata-rata saat ini (riwayat avg tidak disimpan).
            ->map(fn ($r) => [
                'code' => $r->code,
                'name' => $r->name,
                'balance_kg' => round((float) $r->balance, 2),
                'sacks' => (float) $r->weight_per_sack_kg > 0 ? round($r->balance / $r->weight_per_sack_kg, 1) : 0,
                'avg_cost' => round((float) $r->avg_cost_per_kg, 2),
                'value' => round($r->balance * $r->avg_cost_per_kg, 2),
            ]);

        return ['items' => $rows, 'total_value' => round($rows->sum('value'), 2)];
    }

    // Umur piutang & utang per tanggal $to: belum jatuh tempo / 1–30 / 31–60 / > 60 hari.
    public function agingSummary(CarbonInterface $to): array
    {
        $aging = function (string $table, string $dateColumn) use ($to) {
            $day = $to->toDateString();
            $d30 = $to->copy()->subDays(30)->toDateString();
            $d60 = $to->copy()->subDays(60)->toDateString();

            $rows = DB::table($table)
                ->where('status', DocumentStatus::Posted->value)
                ->where('payment_status', '!=', PaymentStatus::Paid->value)
                ->where($dateColumn, '<', $to->copy()->addDay()->toDateString())
                ->selectRaw("CASE
                        WHEN due_date IS NULL OR due_date >= ? THEN 'current'
                        WHEN due_date >= ? THEN 'd1_30'
                        WHEN due_date >= ? THEN 'd31_60'
                        ELSE 'd60_plus' END AS bucket,
                    COUNT(*) AS count, SUM(total - paid_amount) AS amount", [$day, $d30, $d60])
                ->groupBy('bucket')
                ->get()
                ->keyBy('bucket');

            $result = [];
            foreach (['current', 'd1_30', 'd31_60', 'd60_plus'] as $bucket) {
                $result[$bucket] = [
                    'count' => (int) ($rows[$bucket]->count ?? 0),
                    'amount' => round((float) ($rows[$bucket]->amount ?? 0), 2),
                ];
            }
            $result['total'] = round(array_sum(array_column($result, 'amount')), 2);

            return $result;
        };

        return ['receivables' => $aging('sales', 'sale_date'), 'payables' => $aging('purchases', 'purchase_date')];
    }

    /**
     * Satu bentuk tabel untuk halaman, cetak, dan CSV.
     *
     * @return list<array{heading:string, columns:list<array{0:string,1:string}>, rows:list<list<mixed>>}>
     */
    public function sections(string $tab, CarbonInterface $from, CarbonInterface $to, bool $withCost): array
    {
        return match ($tab) {
            'pnl' => $this->pnlSections($from, $to),
            'sales' => $this->salesSections($from, $to),
            'purchases' => $this->purchaseSections($from, $to),
            'yield' => $this->yieldSections($from, $to),
            'stock' => $this->stockSections($to, $withCost),
            'aging' => $this->agingSections($to),
        };
    }

    private function pnlSections(CarbonInterface $from, CarbonInterface $to): array
    {
        $p = $this->profitAndLoss($from, $to);
        $rows = [
            ['Penjualan barang (setelah diskon)', $p['revenue']],
            ['Harga Pokok Penjualan (HPP)', -$p['cogs']],
            ['LABA KOTOR', $p['gross_profit']],
        ];
        foreach ($p['expenses'] as $label => $amount) {
            $rows[] = ['Biaya: '.$label, -$amount];
        }
        $rows[] = ['Total biaya operasional', -$p['total_expenses']];
        $rows[] = ['LABA BERSIH', $p['net_profit']];

        return [
            ['heading' => 'Laba Rugi', 'columns' => [['Keterangan', 'text'], ['Jumlah', 'money']], 'rows' => $rows],
            ['heading' => 'Info', 'columns' => [['Keterangan', 'text'], ['Nilai', 'text']], 'rows' => [
                ['Margin laba bersih', self::format($p['margin_percent'], 'pct')],
                ['Ongkir ditagihkan (bukan omzet barang)', self::format($p['shipping'], 'money')],
            ]],
        ];
    }

    private function salesSections(CarbonInterface $from, CarbonInterface $to): array
    {
        $s = $this->salesSummary($from, $to);

        return [
            ['heading' => 'Omzet per Bulan', 'columns' => [['Bulan', 'text'], ['Jumlah Nota', 'int'], ['Omzet', 'money']],
                'rows' => $s['per_month']->map(fn ($r) => [$r['month'], $r['count'], $r['revenue']])->values()->all()],
            ['heading' => 'Omzet per Hari', 'columns' => [['Tanggal', 'date'], ['Jumlah Nota', 'int'], ['Omzet', 'money']],
                'rows' => $s['per_day']->map(fn ($r) => [$r->day, $r->count, $r->revenue])->all()],
            ['heading' => 'Per Barang', 'columns' => [['Barang', 'text'], ['Berat', 'kg'], ['Nilai', 'money']],
                'rows' => $s['per_item']->map(fn ($r) => [$r->name, $r->kg, $r->amount])->all()],
            ['heading' => '10 Pembeli Teratas', 'columns' => [['Pembeli', 'text'], ['Kota', 'text'], ['Jumlah Nota', 'int'], ['Omzet', 'money']],
                'rows' => $s['per_buyer']->map(fn ($r) => [$r->name, $r->city ?? '-', $r->count, $r->revenue])->all()],
            ['heading' => 'Per Kota', 'columns' => [['Kota', 'text'], ['Jumlah Nota', 'int'], ['Omzet', 'money']],
                'rows' => $s['per_city']->map(fn ($r) => [$r->city, $r->count, $r->revenue])->all()],
        ];
    }

    private function purchaseSections(CarbonInterface $from, CarbonInterface $to): array
    {
        $p = $this->purchaseSummary($from, $to);

        return [
            ['heading' => 'Per Desa', 'columns' => [['Desa', 'text'], ['Jumlah Petani', 'int'], ['Gabah', 'kg'], ['Nilai', 'money']],
                'rows' => $p['per_village']->map(fn ($r) => [$r->village, $r->farmers, $r->kg, $r->amount])->all()],
            ['heading' => 'Per Petani', 'columns' => [['Petani', 'text'], ['Desa', 'text'], ['Gabah', 'kg'], ['Nilai', 'money']],
                'rows' => $p['per_supplier']->map(fn ($r) => [$r->name, $r->village ?? '-', $r->kg, $r->amount])->all()],
            ['heading' => 'Harga Rata-rata Gabah per Bulan', 'columns' => [['Bulan', 'text'], ['Gabah', 'kg'], ['Harga rata-rata / kg', 'money']],
                'rows' => $p['per_month']->map(fn ($r) => [$r['month'], $r['kg'], $r['avg_price']])->values()->all()],
        ];
    }

    private function yieldSections(CarbonInterface $from, CarbonInterface $to): array
    {
        $y = $this->yieldSummary($from, $to);

        return [
            ['heading' => 'Ringkasan', 'columns' => [['Keterangan', 'text'], ['Nilai', 'text']], 'rows' => [
                ['Total gabah digiling', self::format($y['input_kg'], 'kg')],
                ['Total beras', self::format($y['output_kg'], 'kg')],
                ['Rendemen rata-rata tertimbang', self::format($y['weighted_yield'], 'pct')],
                ['Total susut', self::format($y['shrinkage_kg'], 'kg')],
                ['Total mesin macet', $y['downtime_minutes'].' menit'],
            ]],
            ['heading' => 'Per Batch', 'columns' => [['Nomor', 'text'], ['Tanggal', 'date'], ['Gabah', 'kg'], ['Beras', 'kg'], ['Rendemen', 'pct'], ['Susut', 'kg'], ['Macet (menit)', 'int']],
                'rows' => $y['batches']->map(fn ($r) => [$r->number, $r->production_date, $r->input_kg, $r->main_output_kg, $r->yield_percent, $r->shrinkage_kg, $r->machine_downtime_minutes ?? 0])->all()],
        ];
    }

    private function stockSections(CarbonInterface $to, bool $withCost): array
    {
        $s = $this->stockSummary($to);
        $columns = [['Kode', 'text'], ['Barang', 'text'], ['Saldo', 'kg'], ['Karung', 'text']];
        if ($withCost) {
            $columns[] = ['Biaya rata-rata / kg', 'money'];
            $columns[] = ['Nilai persediaan', 'money'];
        }

        $rows = $s['items']->map(fn ($r) => array_merge(
            [$r['code'], $r['name'], $r['balance_kg'], number_format($r['sacks'], 1, ',', '.')],
            $withCost ? [$r['avg_cost'], $r['value']] : [],
        ))->all();

        if ($withCost) {
            $rows[] = ['', 'TOTAL', null, '', null, $s['total_value']];
        }

        return [['heading' => 'Saldo Stok per '.$to->translatedFormat('d F Y'), 'columns' => $columns, 'rows' => $rows]];
    }

    private function agingSections(CarbonInterface $to): array
    {
        $a = $this->agingSummary($to);
        $labels = ['current' => 'Belum jatuh tempo', 'd1_30' => 'Lewat 1–30 hari', 'd31_60' => 'Lewat 31–60 hari', 'd60_plus' => 'Lewat > 60 hari'];

        $rows = [];
        foreach ($labels as $bucket => $label) {
            $rows[] = [$label, $a['receivables'][$bucket]['count'], $a['receivables'][$bucket]['amount'], $a['payables'][$bucket]['count'], $a['payables'][$bucket]['amount']];
        }
        $rows[] = ['TOTAL', null, $a['receivables']['total'], null, $a['payables']['total']];

        return [[
            'heading' => 'Umur Piutang & Utang per '.$to->translatedFormat('d F Y'),
            'columns' => [['Umur', 'text'], ['Nota Piutang', 'int'], ['Piutang Pembeli', 'money'], ['Nota Utang', 'int'], ['Utang ke Petani', 'money']],
            'rows' => $rows,
        ]];
    }

    public static function format(mixed $value, string $type): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return match ($type) {
            'money' => 'Rp '.number_format((float) $value, 2, ',', '.'),
            'kg' => number_format((float) $value, 2, ',', '.').' kg',
            'pct' => number_format((float) $value, 2, ',', '.').' %',
            'int' => number_format((float) $value, 0, ',', '.'),
            'date' => Carbon::parse($value)->format('d/m/Y'),
            default => (string) $value,
        };
    }

    // Untuk CSV Excel Indonesia: angka tanpa pemisah ribuan, desimal koma.
    public static function csvValue(mixed $value, string $type): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return match ($type) {
            'money', 'kg', 'pct' => number_format((float) $value, 2, ',', ''),
            'int' => (string) (int) $value,
            'date' => Carbon::parse($value)->format('d/m/Y'),
            default => (string) $value,
        };
    }

    private function posted(string $table, string $dateColumn, CarbonInterface $from, CarbonInterface $to, ?string $qualify = null): Builder
    {
        $prefix = $qualify ? $qualify.'.' : '';

        return DB::table($table)
            ->where($prefix.'status', DocumentStatus::Posted->value)
            // Batas atas "< besok" supaya benar untuk kolom date maupun datetime.
            ->where($prefix.$dateColumn, '>=', $from->toDateString())
            ->where($prefix.$dateColumn, '<', $to->copy()->addDay()->toDateString());
    }

    // Kelompokkan baris harian (hasil agregat SQL) menjadi bulanan.
    private function byMonth($perDay, array $sumFields)
    {
        return collect($perDay)
            ->groupBy(fn ($row) => substr((string) $row->day, 0, 7))
            ->map(function ($rows, $month) use ($sumFields) {
                $result = ['month' => Carbon::parse($month.'-01')->translatedFormat('F Y')];
                foreach ($sumFields as $field) {
                    $result[$field] = round($rows->sum(fn ($r) => (float) $r->{$field}), 2);
                }

                return $result;
            })
            ->values();
    }
}
