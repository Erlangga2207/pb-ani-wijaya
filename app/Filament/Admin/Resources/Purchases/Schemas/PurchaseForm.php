<?php

namespace App\Filament\Admin\Resources\Purchases\Schemas;

use App\Enums\ItemType;
use App\Enums\PaymentMethod;
use App\Enums\QuantityUnit;
use App\Filament\Admin\Resources\Suppliers\Schemas\SupplierForm;
use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

// Angka di form hanya pratinjau; semua dihitung ulang di PurchaseService.
class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        $rawItems = fn () => Item::ofType(ItemType::Raw)->where('is_active', true)->orderBy('code')->pluck('name', 'id');

        return $schema
            ->columns(1)
            ->components([
                Section::make('1. Petani & Tanggal')->schema([
                    Grid::make(2)->schema([
                        Select::make('supplier_id')
                            ->label('Petani / Pemasok')
                            ->relationship('supplier', 'name', fn ($query) => $query->where('is_active', true))
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->village ? "{$record->name} ({$record->village})" : $record->name)
                            ->searchable(['name', 'village'])
                            ->preload()
                            ->createOptionForm(SupplierForm::fields())
                            ->required(),
                        DatePicker::make('purchase_date')
                            ->label('Tanggal')
                            ->default(today())
                            ->maxDate(today())
                            ->required(),
                    ]),
                ]),

                Section::make('2. Jumlah Gabah')->schema([
                    Grid::make(4)->schema([
                        Select::make('item_id')
                            ->label('Barang')
                            ->options($rawItems)
                            ->default(fn () => Item::where('code', 'GBH')->value('id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('price_per_kg', Item::find($get('item_id'))?->default_price_per_kg);
                                self::recalculate($get, $set);
                            }),
                        TextInput::make('input_quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(0.01)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                        Select::make('input_unit')
                            ->label('Satuan')
                            ->options(QuantityUnit::class)
                            ->default(QuantityUnit::Kg->value)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                        TextInput::make('quantity_kg_preview')
                            ->label('Total Berat')
                            ->suffix('kg')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
                    TextInput::make('moisture_percent')
                        ->label('Kadar Air (opsional)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%'),
                ]),

                Section::make('3. Harga')->schema([
                    Grid::make(3)->schema([
                        TextInput::make('price_per_kg')
                            ->label('Harga / kg')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(1)
                            ->default(fn () => Item::where('code', 'GBH')->value('default_price_per_kg'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                        TextInput::make('other_cost')
                            ->label('Ongkos Lain (angkut/bongkar)')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
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

    private static function kg(Get $get): float
    {
        $item = Item::find($get('item_id'));
        $unit = $get('input_unit');
        $unit = $unit instanceof QuantityUnit ? $unit->value : (string) $unit;

        return $item ? $item->toKg((float) $get('input_quantity'), $unit) : 0;
    }

    private static function total(Get $get): float
    {
        return round(self::kg($get) * (float) $get('price_per_kg') + (float) $get('other_cost'), 2);
    }

    private static function recalculate(Get $get, Set $set): void
    {
        $total = self::total($get);
        $set('quantity_kg_preview', number_format(self::kg($get), 2, ',', '.'));
        $set('total_preview', number_format($total, 2, ',', '.'));
        $set('pay_now', $total);
    }
}
