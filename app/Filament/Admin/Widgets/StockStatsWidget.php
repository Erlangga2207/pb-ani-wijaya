<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Stok Saat Ini';

    protected function getStats(): array
    {
        return Item::where('is_active', true)->orderBy('id')->get()
            ->map(fn (Item $item) => Stat::make($item->name, number_format((float) $item->stock_kg, 0, ',', '.').' kg')
                ->description('≈ '.number_format($item->stockInSacks(), 1, ',', '.').' karung'.($item->isLowStock() ? ' · di bawah minimum' : ''))
                ->descriptionIcon($item->isLowStock() ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-cube')
                ->color($item->isLowStock() ? 'danger' : 'success'))
            ->all();
    }
}
