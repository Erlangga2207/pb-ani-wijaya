<?php

namespace App\Filament\Admin\Resources\Buyers;

use App\Filament\Admin\Resources\Buyers\Pages\CreateBuyer;
use App\Filament\Admin\Resources\Buyers\Pages\EditBuyer;
use App\Filament\Admin\Resources\Buyers\Pages\ListBuyers;
use App\Filament\Admin\Resources\Buyers\Schemas\BuyerForm;
use App\Filament\Admin\Resources\Buyers\Tables\BuyersTable;
use App\Models\Buyer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BuyerResource extends Resource
{
    protected static ?string $model = Buyer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Pembeli';

    protected static ?string $modelLabel = 'Pembeli';

    protected static ?string $pluralModelLabel = 'Pembeli';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BuyerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BuyersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBuyers::route('/'),
            'create' => CreateBuyer::route('/create'),
            'edit' => EditBuyer::route('/{record}/edit'),
        ];
    }
}
