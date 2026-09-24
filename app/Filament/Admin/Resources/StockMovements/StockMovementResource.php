<?php

namespace App\Filament\Admin\Resources\StockMovements;

use App\Filament\Admin\Resources\StockMovements\Pages\ListStockMovements;
use App\Filament\Admin\Resources\StockMovements\Tables\StockMovementsTable;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

// Kartu stok: read-only, tanpa create/edit/delete.
class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Stok';

    protected static ?string $navigationLabel = 'Kartu Stok';

    protected static ?string $modelLabel = 'Mutasi Stok';

    protected static ?string $pluralModelLabel = 'Kartu Stok';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
        ];
    }
}
