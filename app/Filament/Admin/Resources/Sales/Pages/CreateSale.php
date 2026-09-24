<?php

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Filament\Admin\Concerns\CallsService;
use App\Filament\Admin\Resources\Sales\SaleResource;
use App\Services\SaleService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSale extends CreateRecord
{
    use CallsService;

    protected static string $resource = SaleResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        return $this->callService(fn () => app(SaleService::class)->create($data, auth()->user()));
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Penjualan '.$this->record->number.' tersimpan')
            ->actions([
                Action::make('print')
                    ->label('Cetak Nota')
                    ->url(route('sales.invoice', $this->record), shouldOpenInNewTab: true),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
