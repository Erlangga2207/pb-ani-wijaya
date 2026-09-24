<?php

namespace App\Filament\Admin\Actions;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

// "Catat Pembayaran" untuk pembelian (utang) & penjualan (piutang).
class RecordPaymentAction
{
    public static function make(): Action
    {
        return Action::make('recordPayment')
            ->label('Catat Pembayaran')
            ->icon(Heroicon::OutlinedBanknotes)
            ->color('success')
            ->visible(fn (Model $record) => ! $record->isVoid() && $record->payment_status !== PaymentStatus::Paid)
            ->fillForm(fn (Model $record) => [
                'payment_date' => today()->toDateString(),
                'amount' => $record->remainingAmount(),
                'method' => PaymentMethod::Cash->value,
            ])
            ->schema([
                DatePicker::make('payment_date')->label('Tanggal Bayar')->maxDate(today())->required(),
                TextInput::make('amount')
                    ->label('Jumlah')
                    ->prefix('Rp')
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->helperText(fn (Model $record) => 'Sisa tagihan: Rp '.number_format($record->remainingAmount(), 2, ',', '.')),
                Select::make('method')->label('Metode')->options(PaymentMethod::class)->required(),
                Textarea::make('notes')->label('Catatan'),
            ])
            ->action(function (Model $record, array $data, Action $action, Component $livewire) {
                try {
                    app(PaymentService::class)->record($record, $data, auth()->user());
                } catch (ValidationException $e) {
                    Notification::make()->danger()->title('Pembayaran ditolak')->body(collect($e->errors())->flatten()->first())->send();
                    $action->halt();
                }

                $record->refresh();
                $livewire->dispatch('document-updated');
                Notification::make()->success()->title('Pembayaran tercatat')->send();
            });
    }
}
