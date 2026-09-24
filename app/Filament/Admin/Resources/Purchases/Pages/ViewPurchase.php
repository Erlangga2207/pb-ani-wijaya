<?php

namespace App\Filament\Admin\Resources\Purchases\Pages;

use App\Filament\Admin\Actions\RecordPaymentAction;
use App\Filament\Admin\Actions\VoidAction;
use App\Filament\Admin\Concerns\RefreshesRecord;
use App\Filament\Admin\Resources\Purchases\PurchaseResource;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPurchase extends ViewRecord
{
    use RefreshesRecord;

    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RecordPaymentAction::make(),
            Action::make('receipt')
                ->label('Cetak Bukti')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('gray')
                ->url(fn (Purchase $record) => route('purchases.receipt', $record))
                ->openUrlInNewTab(),
            VoidAction::make(fn (Purchase $record, string $reason) => app(PurchaseService::class)->void($record, $reason, auth()->user())),
        ];
    }
}
