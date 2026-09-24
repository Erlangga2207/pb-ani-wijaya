<?php

namespace App\Filament\Admin\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

// Tombol "Batalkan" untuk semua dokumen transaksi. $void = fn (Model $record, string $reason) memanggil service.
class VoidAction
{
    public static function make(Closure $void): Action
    {
        return Action::make('void')
            ->label('Batalkan')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->visible(fn (Model $record): bool => ! $record->isVoid() && auth()->user()->can('void', $record))
            ->authorize(fn (Model $record): bool => auth()->user()->can('void', $record))
            ->requiresConfirmation()
            ->modalHeading('Batalkan dokumen')
            ->modalDescription('Dokumen tidak dihapus, hanya ditandai dibatalkan. Stok & pembayaran dikembalikan lewat mutasi balik.')
            ->schema([
                Textarea::make('reason')
                    ->label('Alasan pembatalan')
                    ->required()
                    ->maxLength(1000),
            ])
            ->action(function (Model $record, array $data, Action $action) use ($void) {
                try {
                    $void($record, $data['reason']);
                } catch (ValidationException $e) {
                    Notification::make()->danger()->title('Gagal membatalkan')->body(collect($e->errors())->flatten()->first())->send();
                    $action->halt();
                }

                $record->refresh();
                Notification::make()->success()->title('Dokumen dibatalkan')->send();
            });
    }
}
