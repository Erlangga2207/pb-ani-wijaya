<?php

namespace App\Filament\Admin\Resources\ContactMessages\Pages;

use App\Filament\Admin\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Balas via Email')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('success')
                ->url(fn () => 'mailto:' . $this->record->email . '?subject=Re: ' . urlencode($this->record->subject ?? 'Pesan Anda'))
                ->openUrlInNewTab(),

            EditAction::make()
                ->label('Edit Catatan'),
        ];
    }

    // Auto-mark as read saat view page dibuka
    protected function afterFill(): void
    {
        $this->record->markAsRead();
    }
}
