<?php

namespace App\Filament\Admin\RelationManagers;

use App\Filament\Admin\Actions\VoidAction;
use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

// Daftar pembayaran di halaman view Pembelian / Penjualan.
class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Pembayaran';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    #[On('document-updated')]
    public function refreshTable(): void {}

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                TextColumn::make('number')->label('Nomor'),
                TextColumn::make('payment_date')->label('Tanggal')->date('d M Y'),
                TextColumn::make('amount')->label('Jumlah')->money('IDR'),
                TextColumn::make('method')->label('Metode')->badge(),
                TextColumn::make('notes')->label('Catatan')->placeholder('-'),
                TextColumn::make('user.name')->label('Dicatat oleh'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('void_reason')->label('Alasan Batal')->placeholder('-')->toggleable(),
            ])
            ->recordActions([
                VoidAction::make(fn (Payment $record, string $reason) => app(PaymentService::class)->void($record, $reason, auth()->user())),
            ]);
    }
}
