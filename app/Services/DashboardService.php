<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\ItemType;
use App\Enums\PaymentStatus;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

// Angka ringkasan dashboard panel.
class DashboardService
{
    public function __construct(private ReportService $reports) {}

    /** @return Collection<int, Item> */
    public function activeItems(): Collection
    {
        return Item::where('is_active', true)->orderBy('id')->get();
    }

    /** @return array{bought: float, milled: float, sold: float} kg hari ini */
    public function todayActivity(): array
    {
        $from = today()->toDateString();
        $to = today()->addDay()->toDateString();
        $posted = DocumentStatus::Posted->value;

        return [
            'bought' => (float) DB::table('purchases')->where('status', $posted)
                ->where('purchase_date', '>=', $from)->where('purchase_date', '<', $to)->sum('quantity_kg'),
            'milled' => (float) DB::table('productions')->where('status', $posted)
                ->where('production_date', '>=', $from)->where('production_date', '<', $to)->sum('input_kg'),
            'sold' => (float) DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->join('items', 'items.id', '=', 'sale_items.item_id')
                ->where('sales.status', $posted)
                ->where('items.type', ItemType::Finished->value)
                ->where('sales.sale_date', '>=', $from)->where('sales.sale_date', '<', $to)
                ->sum('sale_items.quantity_kg'),
        ];
    }

    /** @return array{labels: list<string>, values: list<float>} rendemen per batch 30 hari */
    public function yieldTrend(): array
    {
        $batches = DB::table('productions')
            ->where('status', DocumentStatus::Posted->value)
            ->where('production_date', '>=', today()->subDays(30)->toDateString())
            ->orderBy('production_date')
            ->orderBy('id')
            ->get(['production_date', 'yield_percent']);

        return [
            'labels' => $batches->map(fn ($b) => Carbon::parse($b->production_date)->format('d/m'))->all(),
            'values' => $batches->map(fn ($b) => (float) $b->yield_percent)->all(),
        ];
    }

    // --- Data keuangan: hanya dipanggil controller untuk owner ke atas ---

    /** @return array{revenue: float, gross_profit: float, net_profit: float, receivables: float, payables: float} bulan berjalan */
    public function finance(): array
    {
        $pnl = $this->reports->profitAndLoss(today()->startOfMonth(), today());
        $outstanding = fn (string $table) => (float) DB::table($table)
            ->where('status', DocumentStatus::Posted->value)
            ->where('payment_status', '!=', PaymentStatus::Paid->value)
            ->sum(DB::raw('total - paid_amount'));

        return [
            'revenue' => (float) $pnl['revenue'],
            'gross_profit' => (float) $pnl['gross_profit'],
            'net_profit' => (float) $pnl['net_profit'],
            'receivables' => $outstanding('sales'),
            'payables' => $outstanding('purchases'),
        ];
    }

    /** @return array{labels: list<string>, values: list<float>} omzet 12 bulan terakhir */
    public function salesTrend(): array
    {
        $start = today()->startOfMonth()->subMonths(11);

        // Agregat harian di DB, lalu dijumlah per bulan (format tanggal SQL berbeda antar database).
        $perMonth = DB::table('sales')
            ->where('status', DocumentStatus::Posted->value)
            ->where('sale_date', '>=', $start->toDateString())
            ->selectRaw('sale_date AS day, SUM(subtotal - discount) AS revenue')
            ->groupBy('sale_date')
            ->get()
            ->groupBy(fn ($r) => substr((string) $r->day, 0, 7))
            ->map->sum('revenue');

        $labels = [];
        $values = [];
        for ($month = $start->copy(); $month <= today(); $month->addMonth()) {
            $labels[] = $month->translatedFormat('M Y');
            $values[] = round((float) ($perMonth[$month->format('Y-m')] ?? 0), 2);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /** @return array{receivables: Collection<int, Sale>, payables: Collection<int, Purchase>} 5 teratas lewat jatuh tempo */
    public function overdue(): array
    {
        $overdue = fn (string $model) => $model::query()
            ->posted()
            ->where('payment_status', '!=', PaymentStatus::Paid)
            ->whereNotNull('due_date')
            ->where('due_date', '<', today()->toDateString())
            ->orderBy('due_date')
            ->limit(5);

        return [
            'receivables' => $overdue(Sale::class)->with('buyer')->get(),
            'payables' => $overdue(Purchase::class)->with('supplier')->get(),
        ];
    }
}
