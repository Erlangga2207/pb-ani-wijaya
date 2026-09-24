<?php

namespace App\Filament\Admin\Resources\StockAdjustments\Pages;

use App\Filament\Admin\Concerns\CallsService;
use App\Filament\Admin\Resources\StockAdjustments\StockAdjustmentResource;
use App\Services\StockAdjustmentService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStockAdjustment extends CreateRecord
{
    use CallsService;

    protected static string $resource = StockAdjustmentResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        return $this->callService(fn () => app(StockAdjustmentService::class)->create($data, auth()->user()));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
