<?php

namespace App\Filament\Admin\Resources\Sales;

use App\Filament\Admin\RelationManagers\PaymentsRelationManager;
use App\Filament\Admin\Resources\Sales\Pages\CreateSale;
use App\Filament\Admin\Resources\Sales\Pages\ListSales;
use App\Filament\Admin\Resources\Sales\Pages\ViewSale;
use App\Filament\Admin\Resources\Sales\Schemas\SaleForm;
use App\Filament\Admin\Resources\Sales\Schemas\SaleInfolist;
use App\Filament\Admin\Resources\Sales\Tables\SalesTable;
use App\Models\Sale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

// Tidak ada halaman edit: koreksi = void.
class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Penjualan';

    protected static ?string $modelLabel = 'Penjualan';

    protected static ?string $pluralModelLabel = 'Penjualan';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return SaleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SaleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'create' => CreateSale::route('/create'),
            'view' => ViewSale::route('/{record}'),
        ];
    }
}
