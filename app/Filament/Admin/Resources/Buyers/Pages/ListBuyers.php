<?php

namespace App\Filament\Admin\Resources\Buyers\Pages;

use App\Filament\Admin\Resources\Buyers\BuyerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBuyers extends ListRecords
{
    protected static string $resource = BuyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
