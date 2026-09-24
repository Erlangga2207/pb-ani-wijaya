<?php

namespace App\Filament\Admin\Resources\Purchases\Pages;

use App\Filament\Admin\Concerns\CallsService;
use App\Filament\Admin\Resources\Purchases\PurchaseResource;
use App\Services\PurchaseService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePurchase extends CreateRecord
{
    use CallsService;

    protected static string $resource = PurchaseResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        return $this->callService(fn () => app(PurchaseService::class)->create($data, auth()->user()));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
