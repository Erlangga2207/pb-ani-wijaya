<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\DocumentStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesTrendChart extends ChartWidget
{
    protected static ?int $sort = 6;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Omzet 12 Bulan Terakhir';

    public static function canView(): bool
    {
        return auth()->user()->isOwnerOrAbove();
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $start = today()->startOfMonth()->subMonths(11);

        // Agregat harian di DB, lalu dijumlah per bulan (format tanggal SQL berbeda antar database).
        $perDay = DB::table('sales')
            ->where('status', DocumentStatus::Posted->value)
            ->where('sale_date', '>=', $start->toDateString())
            ->selectRaw('sale_date AS day, SUM(subtotal - discount) AS revenue')
            ->groupBy('sale_date')
            ->get();

        $perMonth = $perDay->groupBy(fn ($r) => substr((string) $r->day, 0, 7))->map->sum('revenue');

        $labels = [];
        $data = [];
        for ($month = $start->copy(); $month <= today(); $month->addMonth()) {
            $labels[] = $month->translatedFormat('M Y');
            $data[] = round((float) ($perMonth[$month->format('Y-m')] ?? 0), 2);
        }

        return [
            'labels' => $labels,
            'datasets' => [['label' => 'Omzet (Rp)', 'data' => $data, 'backgroundColor' => '#ba0013']],
        ];
    }
}
