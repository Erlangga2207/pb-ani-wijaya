<?php

namespace App\Filament\Admin\Resources\Productions\Pages;

use App\Filament\Admin\Actions\VoidAction;
use App\Filament\Admin\Resources\Productions\ProductionResource;
use App\Models\Production;
use App\Services\ProductionService;
use Filament\Resources\Pages\ViewRecord;

class ViewProduction extends ViewRecord
{
    protected static string $resource = ProductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            VoidAction::make(fn (Production $record, string $reason) => app(ProductionService::class)->void($record, $reason, auth()->user())),
        ];
    }
}
