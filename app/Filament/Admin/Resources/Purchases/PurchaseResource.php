<?php

namespace App\Filament\Admin\Resources\Purchases;

use App\Filament\Admin\RelationManagers\PaymentsRelationManager;
use App\Filament\Admin\Resources\Purchases\Pages\CreatePurchase;
use App\Filament\Admin\Resources\Purchases\Pages\ListPurchases;
use App\Filament\Admin\Resources\Purchases\Pages\ViewPurchase;
use App\Filament\Admin\Resources\Purchases\Schemas\PurchaseForm;
use App\Filament\Admin\Resources\Purchases\Schemas\PurchaseInfolist;
use App\Filament\Admin\Resources\Purchases\Tables\PurchasesTable;
use App\Models\Purchase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

// Tidak ada halaman edit: koreksi = void.
class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Pembelian Gabah';

    protected static ?string $modelLabel = 'Pembelian Gabah';

    protected static ?string $pluralModelLabel = 'Pembelian Gabah';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return PurchaseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchasesTable::configure($table);
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
            'index' => ListPurchases::route('/'),
            'create' => CreatePurchase::route('/create'),
            'view' => ViewPurchase::route('/{record}'),
        ];
    }
}
