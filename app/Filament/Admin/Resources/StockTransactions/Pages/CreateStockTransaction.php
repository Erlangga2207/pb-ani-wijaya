<?php

namespace App\Filament\Admin\Resources\StockTransactions\Pages;

use App\Filament\Admin\Resources\StockTransactions\StockTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockTransaction extends CreateRecord
{
    protected static string $resource = StockTransactionResource::class;
}
