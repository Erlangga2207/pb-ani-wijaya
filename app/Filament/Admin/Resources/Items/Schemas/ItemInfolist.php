<?php

namespace App\Filament\Admin\Resources\Items\Schemas;

use App\Models\Item;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Barang')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('code')->label('Kode'),
                        TextEntry::make('name')->label('Nama'),
                        TextEntry::make('type')->label('Tipe')->badge(),
                        TextEntry::make('product.name')->label('Produk Katalog')->placeholder('-'),
                        TextEntry::make('weight_per_sack_kg')->label('Berat per Karung')->numeric(2)->suffix(' kg'),
                        IconEntry::make('is_active')->label('Aktif')->boolean(),
                    ]),
                Section::make('Stok')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('stock_kg')
                            ->label('Stok Saat Ini')
                            ->numeric(2)
                            ->suffix(' kg')
                            ->helperText(fn (Item $record) => '≈ '.number_format($record->stockInSacks(), 1, ',', '.').' karung')
                            ->color(fn (Item $record) => $record->isLowStock() ? 'danger' : null),
                        TextEntry::make('min_stock_kg')->label('Minimum')->numeric(2)->suffix(' kg')->placeholder('-'),
                        TextEntry::make('avg_cost_per_kg')
                            ->label('Biaya Rata-rata / kg')
                            ->money('IDR')
                            ->visible(fn () => auth()->user()->isOwnerOrAbove()),
                        TextEntry::make('default_price_per_kg')->label('Harga Pasar Terbaru / kg')->money('IDR')->placeholder('-'),
                    ]),
            ]);
    }
}
