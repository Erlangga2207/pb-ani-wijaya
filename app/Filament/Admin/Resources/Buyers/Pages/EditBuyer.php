<?php

namespace App\Filament\Admin\Resources\Buyers\Pages;

use App\Filament\Admin\Resources\Buyers\BuyerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBuyer extends EditRecord
{
    protected static string $resource = BuyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
