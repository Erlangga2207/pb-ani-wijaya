<?php

namespace App\Filament\Admin\Resources\Sales\Schemas;

use App\Models\Sale;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isOwner = (bool) auth()->user()?->isOwnerOrAbove();

        return $schema
            ->columns(1)
            ->components([
                Section::make('Penjualan')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('number')->label('Nomor'),
                        TextEntry::make('sale_date')->label('Tanggal')->date('d M Y'),
                        TextEntry::make('status')->label('Status')->badge(),
                        TextEntry::make('user.name')->label('Dicatat oleh'),
                        TextEntry::make('buyer.name')->label('Pembeli'),
                        TextEntry::make('buyer.city')->label('Kota')->placeholder('-'),
                        TextEntry::make('buyer.phone')->label('HP')->placeholder('-'),
                        TextEntry::make('notes')->label('Catatan')->placeholder('-'),
                    ]),
                Section::make('Barang')->schema([
                    RepeatableEntry::make('items')
                        ->hiddenLabel()
                        ->table(array_values(array_filter([
                            TableColumn::make('Barang'),
                            TableColumn::make('Jumlah'),
                            TableColumn::make('Berat'),
                            TableColumn::make('Harga / kg'),
                            TableColumn::make('Total'),
                            $isOwner ? TableColumn::make('HPP / kg') : null,
                        ])))
                        ->schema(array_values(array_filter([
                            TextEntry::make('item.name'),
                            TextEntry::make('input_quantity')->formatStateUsing(fn ($record) => number_format((float) $record->input_quantity, 2, ',', '.').' '.$record->input_unit->getLabel()),
                            TextEntry::make('quantity_kg')->numeric(2)->suffix(' kg'),
                            TextEntry::make('price_per_kg')->money('IDR'),
                            TextEntry::make('line_total')->money('IDR'),
                            $isOwner ? TextEntry::make('cogs_per_kg')->money('IDR') : null,
                        ]))),
                ]),
                Section::make('Tagihan')
                    ->columns(4)
                    ->schema(array_values(array_filter([
                        TextEntry::make('subtotal')->label('Subtotal')->money('IDR'),
                        TextEntry::make('discount')->label('Diskon')->money('IDR'),
                        TextEntry::make('shipping_cost')->label('Ongkir')->money('IDR'),
                        TextEntry::make('total')->label('Total')->money('IDR')->weight('bold'),
                        TextEntry::make('paid_amount')->label('Dibayar')->money('IDR'),
                        TextEntry::make('remaining')->label('Sisa Piutang')->state(fn (Sale $record) => $record->remainingAmount())->money('IDR'),
                        TextEntry::make('payment_status')->label('Status Bayar')->badge(),
                        TextEntry::make('due_date')
                            ->label('Jatuh Tempo')
                            ->date('d M Y')
                            ->placeholder('-')
                            ->color(fn (Sale $record) => $record->isOverdue() ? 'danger' : null),
                        $isOwner ? TextEntry::make('cogs_total')->label('HPP')->money('IDR') : null,
                        $isOwner ? TextEntry::make('gross_profit')->label('Laba Kotor')->state(fn (Sale $record) => $record->grossProfit())->money('IDR') : null,
                    ]))),
                Section::make('Pembatalan')
                    ->columns(3)
                    ->visible(fn (Sale $record) => $record->isVoid())
                    ->schema([
                        TextEntry::make('voidedBy.name')->label('Dibatalkan oleh'),
                        TextEntry::make('voided_at')->label('Waktu')->dateTime('d M Y H:i'),
                        TextEntry::make('void_reason')->label('Alasan'),
                    ]),
            ]);
    }
}
