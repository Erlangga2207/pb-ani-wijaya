<?php

namespace App\Filament\Admin\Resources\StockTransactions\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;

class StockTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->description('Detail dasar transaksi stok')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('type')
                                ->label('Tipe Transaksi')
                                ->options([
                                    'in' => '📥 Barang Masuk',
                                    'out' => '📤 Barang Keluar',
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    // Reset supplier & buyer saat type berubah
                                    if ($state === 'in') {
                                        $set('buyer_id', null);
                                    } else {
                                        $set('supplier_id', null);
                                    }
                                }),

                            DatePicker::make('transaction_date')
                                ->label('Tanggal Transaksi')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->displayFormat('d M Y'),
                        ]),

                        TextInput::make('reference_number')
                            ->label('Nomor Referensi')
                            ->placeholder('Contoh: INV-2026-001 (opsional)')
                            ->maxLength(255),
                    ]),

                Section::make('Detail Produk & Pihak')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->relationship('product', 'name', fn ($query) => $query->where('is_active', true))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),

                        // Supplier hanya muncul kalau type = in
                        Select::make('supplier_id')
                            ->label('Pemasok')
                            ->relationship('supplier', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get) => $get('type') === 'in')
                            ->visible(fn (Get $get) => $get('type') === 'in'),

                        // Buyer hanya muncul kalau type = out
                        Select::make('buyer_id')
                            ->label('Pembeli')
                            ->relationship('buyer', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get) => $get('type') === 'out')
                            ->visible(fn (Get $get) => $get('type') === 'out'),
                    ]),

                Section::make('Jumlah & Harga')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('input_quantity')
                                ->label('Jumlah')
                                ->required()
                                ->numeric()
                                ->minValue(0.01)
                                ->step(0.01)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateKg($get, $set)),

                            Select::make('input_unit')
                                ->label('Satuan')
                                ->options([
                                    'kg' => 'Kg',
                                    'karung' => 'Karung',
                                ])
                                ->default('kg')
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateKg($get, $set)),
                        ]),

                        // Helper text untuk tampilkan hasil konversi
                        TextInput::make('quantity_kg')
                            ->label('Total dalam Kg (otomatis)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated() // tetap simpan ke DB walau disabled
                            ->required()
                            ->suffix('kg')
                            ->helperText('Dihitung otomatis dari Jumlah × Satuan'),

                        Grid::make(2)->schema([
                            TextInput::make('price_per_kg')
                                ->label('Harga per Kg')
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateTotal($get, $set)),

                            TextInput::make('total_price')
                                ->label('Total Harga')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated()
                                ->helperText('Otomatis: Total Kg × Harga per Kg'),
                        ]),
                    ]),

                Section::make('Catatan')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->placeholder('Catatan khusus untuk transaksi ini (opsional)'),
                    ]),

                // Hidden field: auto-fill user_id dengan user yang login
                Hidden::make('user_id')
                    ->default(fn () => Auth::id()),
            ]);
    }

    /**
     * Hitung quantity_kg otomatis berdasarkan input_quantity dan input_unit
     */
    protected static function recalculateKg(Get $get, Set $set): void
    {
        $qty = (float) $get('input_quantity');
        $unit = $get('input_unit');
        $productId = $get('product_id');

        if ($qty <= 0 || !$unit) {
            $set('quantity_kg', null);
            return;
        }

        if ($unit === 'kg') {
            $set('quantity_kg', $qty);
        } elseif ($unit === 'karung') {
            // Ambil weight_per_karung dari product
            $weightPerKarung = 25; // default fallback
            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $weightPerKarung = (float) $product->weight_per_karung;
                }
            }
            $set('quantity_kg', $qty * $weightPerKarung);
        }

        // Recalculate total juga
        self::recalculateTotal($get, $set);
    }

    /**
     * Hitung total_price otomatis berdasarkan quantity_kg dan price_per_kg
     */
    protected static function recalculateTotal(Get $get, Set $set): void
    {
        $kg = (float) $get('quantity_kg');
        $price = (float) $get('price_per_kg');

        if ($kg > 0 && $price > 0) {
            $set('total_price', $kg * $price);
        }
    }
}
