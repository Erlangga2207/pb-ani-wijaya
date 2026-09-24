<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\DocumentStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class YieldTrendChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Rendemen per Batch (30 hari)';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $batches = DB::table('productions')
            ->where('status', DocumentStatus::Posted->value)
            ->where('production_date', '>=', today()->subDays(30)->toDateString())
            ->orderBy('production_date')
            ->orderBy('id')
            ->get(['production_date', 'yield_percent']);

        $count = $batches->count();

        return [
            'labels' => $batches->map(fn ($b) => Carbon::parse($b->production_date)->format('d/m'))->all(),
            'datasets' => [
                ['label' => 'Rendemen (%)', 'data' => $batches->pluck('yield_percent')->map(fn ($v) => (float) $v)->all(), 'borderColor' => '#ba0013'],
                ['label' => 'Batas bawah', 'data' => array_fill(0, $count, (float) config('production.yield_min')), 'borderColor' => '#e8c404', 'borderDash' => [6, 4], 'pointRadius' => 0],
                ['label' => 'Batas atas', 'data' => array_fill(0, $count, (float) config('production.yield_max')), 'borderColor' => '#e8c404', 'borderDash' => [6, 4], 'pointRadius' => 0],
            ],
        ];
    }
}
