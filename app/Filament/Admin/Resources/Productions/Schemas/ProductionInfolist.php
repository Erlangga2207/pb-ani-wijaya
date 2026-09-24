<?php

namespace App\Filament\Admin\Resources\Productions\Schemas;

use App\Models\Production;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isOwner = fn () => auth()->user()->isOwnerOrAbove();

        return $schema
            ->columns(1)
            ->components([
                Section::make('Produksi')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('number')->label('Nomor'),
                        TextEntry::make('production_date')->label('Tanggal')->date('d M Y'),
                        TextEntry::make('status')->label('Status')->badge(),
                        TextEntry::make('user.name')->label('Dicatat oleh'),
                        TextEntry::make('inputItem.name')->label('Gabah'),
                        TextEntry::make('input_kg')->label('Berat Gabah')->numeric(2)->suffix(' kg'),
                        TextEntry::make('yield_percent')
                            ->label('Rendemen')
                            ->suffix(' %')
                            ->badge()
                            ->color(fn (Production $record) => $record->isYieldOutOfRange() ? 'warning' : 'success'),
                        TextEntry::make('shrinkage_kg')->label('Susut')->numeric(2)->suffix(' kg'),
                        TextEntry::make('machine_downtime_minutes')->label('Mesin Macet')->suffix(' menit')->placeholder('-'),
                        TextEntry::make('input_cost_per_kg')->label('Biaya Gabah / kg')->money('IDR')->visible($isOwner),
                        TextEntry::make('input_total_cost')->label('Total Biaya Gabah')->money('IDR')->visible($isOwner),
                        TextEntry::make('notes')->label('Catatan')->placeholder('-'),
                    ]),
                Section::make('Hasil Giling')->schema([
                    RepeatableEntry::make('outputs')
                        ->hiddenLabel()
                        ->table(array_filter([
                            RepeatableEntry\TableColumn::make('Barang'),
                            RepeatableEntry\TableColumn::make('Berat'),
                            auth()->user()?->isOwnerOrAbove() ? RepeatableEntry\TableColumn::make('HPP / kg') : null,
                        ]))
                        ->schema(array_filter([
                            TextEntry::make('item.name'),
                            TextEntry::make('quantity_kg')->numeric(2)->suffix(' kg'),
                            auth()->user()?->isOwnerOrAbove() ? TextEntry::make('unit_cost')->money('IDR') : null,
                        ])),
                ]),
                Section::make('Pembatalan')
                    ->columns(3)
                    ->visible(fn (Production $record) => $record->isVoid())
                    ->schema([
                        TextEntry::make('voidedBy.name')->label('Dibatalkan oleh'),
                        TextEntry::make('voided_at')->label('Waktu')->dateTime('d M Y H:i'),
                        TextEntry::make('void_reason')->label('Alasan'),
                    ]),
            ]);
    }
}
