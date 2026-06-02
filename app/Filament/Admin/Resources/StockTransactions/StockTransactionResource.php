<?php

namespace App\Filament\Admin\Resources\StockTransactions;

use App\Filament\Admin\Resources\StockTransactions\Pages\CreateStockTransaction;
use App\Filament\Admin\Resources\StockTransactions\Pages\EditStockTransaction;
use App\Filament\Admin\Resources\StockTransactions\Pages\ListStockTransactions;
use App\Filament\Admin\Resources\StockTransactions\Schemas\StockTransactionForm;
use App\Filament\Admin\Resources\StockTransactions\Tables\StockTransactionsTable;
use App\Models\StockTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockTransactionResource extends Resource
{
    protected static ?string $model = StockTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $recordTitleAttribute = 'reference_number';

    public static function form(Schema $schema): Schema
    {
        return StockTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockTransactionsTable::configure($table);
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
            'index' => ListStockTransactions::route('/'),
            'create' => CreateStockTransaction::route('/create'),
            'edit' => EditStockTransaction::route('/{record}/edit'),
        ];
    }
}
