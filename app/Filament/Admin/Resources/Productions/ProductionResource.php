<?php

namespace App\Filament\Admin\Resources\Productions;

use App\Filament\Admin\Resources\Productions\Pages\CreateProduction;
use App\Filament\Admin\Resources\Productions\Pages\ListProductions;
use App\Filament\Admin\Resources\Productions\Pages\ViewProduction;
use App\Filament\Admin\Resources\Productions\Schemas\ProductionForm;
use App\Filament\Admin\Resources\Productions\Schemas\ProductionInfolist;
use App\Filament\Admin\Resources\Productions\Tables\ProductionsTable;
use App\Models\Production;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

// Tidak ada halaman edit: koreksi = void.
class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Produksi / Giling';

    protected static ?string $modelLabel = 'Produksi';

    protected static ?string $pluralModelLabel = 'Produksi / Giling';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return ProductionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductions::route('/'),
            'create' => CreateProduction::route('/create'),
            'view' => ViewProduction::route('/{record}'),
        ];
    }
}
