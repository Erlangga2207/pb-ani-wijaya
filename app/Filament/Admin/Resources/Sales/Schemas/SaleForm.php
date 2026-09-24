<?php

namespace App\Filament\Admin\Resources\Sales\Schemas;

use App\Enums\ItemType;
use App\Enums\PaymentMethod;
use App\Enums\QuantityUnit;
use App\Filament\Admin\Resources\Buyers\Schemas\BuyerForm;
use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

// Angka di form hanya pratinjau; semua dihitung ulang di SaleService.
class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('1. Pembeli & Tanggal')->schema([
                    Grid::make(2)->schema([
                        Select::make('buyer_id')
                            ->label('Pembeli')
                            ->relationship('buyer', 'name', fn ($query) => $query->where('is_active', true))
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->city ? "{$record->name} ({$record->city})" : $record->name)
                            ->searchable(['name', 'city'])
                            ->preload()
                            ->createOptionForm(BuyerForm::fields())
                            ->required(),
                        DatePicker::make('sale_date')
                            ->label('Tanggal')
                            ->default(today())
                            ->maxDate(today())
                            ->required(),
                    ]),
                ]),

                Section::make('2. Barang')->schema([
                    Repeater::make('items')
                        ->hiddenLabel()
                        ->addActionLabel('Tambah barang')
                        ->minItems(1)
                        ->defaultItems(1)
                        ->columns(5)
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateTotal($get, $set, ''))
                        ->schema([
                            Select::make('item_id')
                                ->label('Barang')
                                ->options(fn () => Item::ofType(ItemType::Finished, ItemType::Byproduct)
                                    ->where('is_active', true)
                                    ->where('stock_kg', '>', 0)
                                    ->orderBy('id')
                                    ->get()
                                    ->mapWithKeys(fn (Item $item) => [$item->id => $item->name.' — stok '.number_format((float) $item->stock_kg, 0, ',', '.').' kg']))
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    $set('price_per_kg', Item::find($get('item_id'))?->default_price_per_kg);
                                    self::recalculateLine($get, $set);
                                })
                                ->columnSpan(2),
                            TextInput::make('input_quantity')
                                ->label('Jumlah')
                                ->numeric()
                                ->minValue(0.01)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateLine($get, $set)),
                            Select::make('input_unit')
                                ->label('Satuan')
                                ->options(QuantityUnit::class)
                                ->default(QuantityUnit::Sack->value)
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateLine($get, $set)),
                            TextInput::make('price_per_kg')
                                ->label('Harga / kg')
                                ->prefix('Rp')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateLine($get, $set)),
                            TextInput::make('line_preview')
                                ->label('Total Baris')
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),
                        ]),
                ]),

                Section::make('3. Total')->schema([
                    Grid::make(3)->schema([
                        TextInput::make('discount')
                            ->label('Diskon')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateTotal($get, $set)),
                        TextInput::make('shipping_cost')
                            ->label('Ongkir (ditagihkan)')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateTotal($get, $set)),
                        TextInput::make('total_preview')
                            ->label('Total')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
                ]),

                Section::make('4. Pembayaran')->schema([
                    Grid::make(3)->schema([
                        TextInput::make('pay_now')
                            ->label('Bayar Sekarang')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true),
                        Select::make('payment_method')
                            ->label('Metode')
                            ->options(PaymentMethod::class)
                            ->default(PaymentMethod::Cash->value),
                        DatePicker::make('due_date')
                            ->label('Jatuh Tempo')
                            ->minDate(today())
                            ->required(fn (Get $get) => (float) $get('pay_now') < self::total($get))
                            ->helperText('Wajib bila bayar kurang dari total.'),
                    ]),
                    Textarea::make('notes')->label('Catatan'),
                ]),
            ]);
    }

    private static function lineTotal(?array $line): float
    {
        $item = Item::find($line['item_id'] ?? null);
        if (! $item) {
            return 0;
        }
        $unit = $line['input_unit'] ?? 'kg';
        $unit = $unit instanceof QuantityUnit ? $unit->value : (string) $unit;

        return round($item->toKg((float) ($line['input_quantity'] ?? 0), $unit) * (float) ($line['price_per_kg'] ?? 0), 2);
    }

    // $prefix: '' dari level form, '../../' dari dalam item repeater.
    private static function total(Get $get, string $prefix = ''): float
    {
        $subtotal = array_sum(array_map(fn ($line) => self::lineTotal($line), (array) $get($prefix.'items')));

        return round($subtotal - (float) $get($prefix.'discount') + (float) $get($prefix.'shipping_cost'), 2);
    }

    private static function recalculateLine(Get $get, Set $set): void
    {
        $line = [
            'item_id' => $get('item_id'),
            'input_quantity' => $get('input_quantity'),
            'input_unit' => $get('input_unit'),
            'price_per_kg' => $get('price_per_kg'),
        ];
        $set('line_preview', 'Rp '.number_format(self::lineTotal($line), 2, ',', '.'));
        self::recalculateTotal($get, $set, '../../');
    }

    private static function recalculateTotal(Get $get, Set $set, string $prefix = ''): void
    {
        $total = self::total($get, $prefix);
        $set($prefix.'total_preview', number_format($total, 2, ',', '.'));
        $set($prefix.'pay_now', $total);
    }
}
