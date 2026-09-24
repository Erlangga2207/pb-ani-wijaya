<?php

namespace App\Filament\Admin\Resources\Items\Schemas;

use App\Enums\ItemType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

// stock_kg & avg_cost_per_kg sengaja tidak ada: hanya berubah lewat transaksi.
class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Tipe')
                    ->options(ItemType::class)
                    ->required()
                    ->native(false),
                Select::make('product_id')
                    ->label('Produk di Katalog Website')
                    ->relationship('product', 'name')
                    ->helperText('Opsional, untuk menghubungkan barang ke halaman produk.'),
                TextInput::make('weight_per_sack_kg')
                    ->label('Berat per Karung')
                    ->numeric()
                    ->minValue(0.01)
                    ->default(25)
                    ->suffix('kg')
                    ->required(),
                TextInput::make('min_stock_kg')
                    ->label('Batas Minimum Stok')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('kg')
                    ->helperText('Muncul peringatan di dashboard bila stok ≤ angka ini.'),
                TextInput::make('default_price_per_kg')
                    ->label('Harga Pasar Terbaru / kg')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->helperText('Otomatis diperbarui dari transaksi terakhir. Dipakai untuk isi awal form.'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
