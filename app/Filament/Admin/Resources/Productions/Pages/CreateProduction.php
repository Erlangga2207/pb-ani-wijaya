<?php

namespace App\Filament\Admin\Resources\Productions\Pages;

use App\Filament\Admin\Concerns\CallsService;
use App\Filament\Admin\Resources\Productions\ProductionResource;
use App\Services\ProductionService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduction extends CreateRecord
{
    use CallsService;

    protected static string $resource = ProductionResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        return $this->callService(fn () => app(ProductionService::class)->create($data, auth()->user()));
    }

    // Rendemen di luar batas normal: tetap tersimpan, hanya diberi peringatan.
    protected function afterCreate(): void
    {
        if ($this->record->isYieldOutOfRange()) {
            Notification::make()
                ->warning()
                ->title('Rendemen di luar batas normal')
                ->body(sprintf(
                    'Rendemen %s%% (normal %s–%s%%). Periksa timbangan / kualitas gabah.',
                    number_format((float) $this->record->yield_percent, 2, ',', '.'),
                    config('production.yield_min'),
                    config('production.yield_max'),
                ))
                ->persistent()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
