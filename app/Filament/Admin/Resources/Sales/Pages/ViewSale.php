<?php

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Filament\Admin\Actions\RecordPaymentAction;
use App\Filament\Admin\Actions\VoidAction;
use App\Filament\Admin\Concerns\RefreshesRecord;
use App\Filament\Admin\Resources\Sales\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewSale extends ViewRecord
{
    use RefreshesRecord;

    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('invoice')
                ->label('Cetak Nota')
                ->icon(Heroicon::OutlinedPrinter)
                ->url(fn (Sale $record) => route('sales.invoice', $record))
                ->openUrlInNewTab(),
            RecordPaymentAction::make(),
            VoidAction::make(fn (Sale $record, string $reason) => app(SaleService::class)->void($record, $reason, auth()->user())),
        ];
    }
}
