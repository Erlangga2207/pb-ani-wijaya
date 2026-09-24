<?php

namespace App\Filament\Admin\Resources\StockAdjustments\Pages;

use App\Filament\Admin\Actions\VoidAction;
use App\Filament\Admin\Resources\StockAdjustments\StockAdjustmentResource;
use App\Models\StockAdjustment;
use App\Services\StockAdjustmentService;
use Filament\Resources\Pages\ViewRecord;

class ViewStockAdjustment extends ViewRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            VoidAction::make(fn (StockAdjustment $record, string $reason) => app(StockAdjustmentService::class)->void($record, $reason, auth()->user())),
        ];
    }
}
