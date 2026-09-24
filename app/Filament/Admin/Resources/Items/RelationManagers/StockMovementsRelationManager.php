<?php

namespace App\Filament\Admin\Resources\Items\RelationManagers;

use App\Filament\Admin\Resources\StockMovements\Tables\StockMovementsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    protected static ?string $title = 'Riwayat Stok';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }
}
