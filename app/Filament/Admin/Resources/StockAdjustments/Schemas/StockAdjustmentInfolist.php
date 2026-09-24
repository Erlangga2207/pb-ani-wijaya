<?php

namespace App\Filament\Admin\Resources\StockAdjustments\Schemas;

use App\Models\StockAdjustment;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockAdjustmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Stock Opname')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('number')->label('Nomor'),
                        TextEntry::make('adjustment_date')->label('Tanggal')->date('d M Y'),
                        TextEntry::make('status')->label('Status')->badge(),
                        TextEntry::make('item.name')->label('Barang'),
                        TextEntry::make('system_kg')->label('Stok Sistem')->numeric(2)->suffix(' kg'),
                        TextEntry::make('physical_kg')->label('Stok Fisik')->numeric(2)->suffix(' kg'),
                        TextEntry::make('difference_kg')
                            ->label('Selisih')
                            ->numeric(2)
                            ->suffix(' kg')
                            ->color(fn (StockAdjustment $record) => (float) $record->difference_kg < 0 ? 'danger' : 'success'),
                        TextEntry::make('user.name')->label('Dicatat oleh'),
                        TextEntry::make('created_at')->label('Waktu Input')->dateTime('d M Y H:i'),
                        TextEntry::make('reason')->label('Alasan')->columnSpanFull(),
                    ]),
                Section::make('Pembatalan')
                    ->columns(3)
                    ->columnSpanFull()
                    ->visible(fn (StockAdjustment $record) => $record->isVoid())
                    ->schema([
                        TextEntry::make('voidedBy.name')->label('Dibatalkan oleh'),
                        TextEntry::make('voided_at')->label('Waktu')->dateTime('d M Y H:i'),
                        TextEntry::make('void_reason')->label('Alasan'),
                    ]),
            ]);
    }
}
