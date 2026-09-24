<?php

namespace App\Filament\Admin\Resources\ContactMessages\Pages;

use App\Filament\Admin\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...ContactMessageResource::leadActions(),
            EditAction::make()->label('Tindak lanjut'),
        ];
    }

    // Auto-mark as read saat view page dibuka
    protected function afterFill(): void
    {
        $this->record->markAsRead();
    }
}
