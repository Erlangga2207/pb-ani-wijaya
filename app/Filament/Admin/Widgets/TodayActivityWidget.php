<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\DocumentStatus;
use App\Enums\ItemType;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TodayActivityWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Aktivitas Hari Ini';

    protected function getStats(): array
    {
        $today = [today()->toDateString(), today()->addDay()->toDateString()];
        $posted = DocumentStatus::Posted->value;

        $bought = DB::table('purchases')->where('status', $posted)->where('purchase_date', '>=', $today[0])->where('purchase_date', '<', $today[1])->sum('quantity_kg');
        $milled = DB::table('productions')->where('status', $posted)->where('production_date', '>=', $today[0])->where('production_date', '<', $today[1])->sum('input_kg');
        $sold = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('items', 'items.id', '=', 'sale_items.item_id')
            ->where('sales.status', $posted)
            ->where('items.type', ItemType::Finished->value)
            ->where('sales.sale_date', '>=', $today[0])->where('sales.sale_date', '<', $today[1])
            ->sum('sale_items.quantity_kg');

        $kg = fn ($v) => number_format((float) $v, 0, ',', '.').' kg';

        return [
            Stat::make('Gabah dibeli', $kg($bought))->icon('heroicon-o-inbox-arrow-down'),
            Stat::make('Gabah digiling', $kg($milled))->icon('heroicon-o-cog-6-tooth'),
            Stat::make('Beras terjual', $kg($sold))->icon('heroicon-o-shopping-cart'),
        ];
    }
}
