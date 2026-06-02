<?php

namespace App\Filament\Admin\Resources\StockTransactions\Pages;

use App\Filament\Admin\Resources\StockTransactions\StockTransactionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockTransaction extends EditRecord
{
    protected static string $resource = StockTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_nota')
                ->label('📋 Lihat Nota')
                ->color('warning')
                ->url(fn () => route('nota.show', $this->record->id))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin'])),

            DeleteAction::make(),
        ];
    }
}
