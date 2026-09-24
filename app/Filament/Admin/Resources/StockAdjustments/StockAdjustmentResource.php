<?php

namespace App\Filament\Admin\Resources\StockAdjustments;

use App\Filament\Admin\Resources\StockAdjustments\Pages\CreateStockAdjustment;
use App\Filament\Admin\Resources\StockAdjustments\Pages\ListStockAdjustments;
use App\Filament\Admin\Resources\StockAdjustments\Pages\ViewStockAdjustment;
use App\Filament\Admin\Resources\StockAdjustments\Schemas\StockAdjustmentForm;
use App\Filament\Admin\Resources\StockAdjustments\Schemas\StockAdjustmentInfolist;
use App\Filament\Admin\Resources\StockAdjustments\Tables\StockAdjustmentsTable;
use App\Models\StockAdjustment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|UnitEnum|null $navigationGroup = 'Stok';

    protected static ?string $navigationLabel = 'Stock Opname';

    protected static ?string $modelLabel = 'Stock Opname';

    protected static ?string $pluralModelLabel = 'Stock Opname';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return StockAdjustmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockAdjustmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAdjustmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'view' => ViewStockAdjustment::route('/{record}'),
        ];
    }
}
