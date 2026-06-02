<?php

namespace App\Filament\Admin\Resources\StockTransactions\Pages;

use App\Filament\Admin\Resources\StockTransactions\StockTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockTransactions extends ListRecords
{
    protected static string $resource = StockTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
