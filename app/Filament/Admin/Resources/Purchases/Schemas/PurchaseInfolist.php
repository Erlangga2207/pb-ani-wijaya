<?php

namespace App\Filament\Admin\Resources\Purchases\Schemas;

use App\Models\Purchase;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PurchaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Pembelian')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('number')->label('Nomor'),
                        TextEntry::make('purchase_date')->label('Tanggal')->date('d M Y'),
                        TextEntry::make('status')->label('Status')->badge(),
                        TextEntry::make('user.name')->label('Dicatat oleh'),
                        TextEntry::make('supplier.name')->label('Petani'),
                        TextEntry::make('supplier.village')->label('Desa')->placeholder('-'),
                        TextEntry::make('item.name')->label('Barang'),
                        TextEntry::make('moisture_percent')->label('Kadar Air')->suffix(' %')->placeholder('-'),
                        TextEntry::make('input_quantity')
                            ->label('Jumlah Input')
                            ->formatStateUsing(fn (Purchase $record) => number_format((float) $record->input_quantity, 2, ',', '.').' '.$record->input_unit->getLabel()),
                        TextEntry::make('quantity_kg')->label('Berat')->numeric(2)->suffix(' kg'),
                        TextEntry::make('price_per_kg')->label('Harga / kg')->money('IDR'),
                        TextEntry::make('notes')->label('Catatan')->placeholder('-'),
                    ]),
                Section::make('Tagihan')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('subtotal')->label('Subtotal')->money('IDR'),
                        TextEntry::make('other_cost')->label('Ongkos Lain')->money('IDR'),
                        TextEntry::make('total')->label('Total')->money('IDR')->weight('bold'),
                        TextEntry::make('payment_status')->label('Status Bayar')->badge(),
                        TextEntry::make('paid_amount')->label('Dibayar')->money('IDR'),
                        TextEntry::make('remaining')->label('Sisa Utang')->state(fn (Purchase $record) => $record->remainingAmount())->money('IDR'),
                        TextEntry::make('due_date')
                            ->label('Jatuh Tempo')
                            ->date('d M Y')
                            ->placeholder('-')
                            ->color(fn (Purchase $record) => $record->isOverdue() ? 'danger' : null),
                    ]),
                Section::make('Pembatalan')
                    ->columns(3)
                    ->visible(fn (Purchase $record) => $record->isVoid())
                    ->schema([
                        TextEntry::make('voidedBy.name')->label('Dibatalkan oleh'),
                        TextEntry::make('voided_at')->label('Waktu')->dateTime('d M Y H:i'),
                        TextEntry::make('void_reason')->label('Alasan'),
                    ]),
            ]);
    }
}
