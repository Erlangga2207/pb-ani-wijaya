<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\ItemType;
use App\Filament\Admin\Resources\Purchases\PurchaseResource;
use App\Models\Item;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LowStockAlertWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Stok di Bawah Minimum';

    public static function canView(): bool
    {
        return Item::whereNotNull('min_stock_kg')->whereColumn('stock_kg', '<=', 'min_stock_kg')->exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Item::query()->where('is_active', true)->whereNotNull('min_stock_kg')->whereColumn('stock_kg', '<=', 'min_stock_kg'))
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->label('Barang'),
                TextColumn::make('stock_kg')->label('Stok')->numeric(2)->suffix(' kg')->color('danger'),
                TextColumn::make('min_stock_kg')->label('Minimum')->numeric(2)->suffix(' kg'),
            ])
            ->recordActions([
                Action::make('buy')
                    ->label('Catat Pembelian')
                    ->icon('heroicon-o-plus')
                    ->visible(fn (Item $record) => $record->type === ItemType::Raw)
                    ->url(PurchaseResource::getUrl('create')),
            ]);
    }
}
