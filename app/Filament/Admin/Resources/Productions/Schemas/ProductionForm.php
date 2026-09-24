<?php

namespace App\Filament\Admin\Resources\Productions\Schemas;

use App\Enums\ItemType;
use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProductionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('1. Gabah yang Digiling')->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('production_date')
                            ->label('Tanggal')
                            ->default(today())
                            ->maxDate(today())
                            ->required(),
                        Select::make('input_item_id')
                            ->label('Gabah')
                            ->options(fn () => Item::ofType(ItemType::Raw)->where('is_active', true)->pluck('name', 'id'))
                            ->default(fn () => Item::where('code', 'GBH')->value('id'))
                            ->required()
                            ->live(),
                        TextInput::make('input_kg')
                            ->label('Berat Gabah')
                            ->numeric()
                            ->minValue(0.01)
                            ->suffix('kg')
                            ->required()
                            ->live(onBlur: true)
                            ->helperText(fn (Get $get) => 'Stok gabah tersedia: '.number_format((float) Item::find($get('input_item_id'))?->stock_kg, 2, ',', '.').' kg'),
                    ]),
                ]),

                // Field tetap per barang (bukan repeater) supaya operator cepat mengisi.
                Section::make('2. Hasil Giling')->schema([
                    Grid::make(4)->schema(fn () => Item::ofType(ItemType::Finished, ItemType::Byproduct)
                        ->where('is_active', true)
                        ->orderByRaw("CASE WHEN type = 'finished' THEN 0 ELSE 1 END")
                        ->orderBy('id')
                        ->get()
                        ->map(fn (Item $item) => TextInput::make("outputs.{$item->id}")
                            ->label($item->name)
                            ->numeric()
                            ->minValue($item->type === ItemType::Finished ? 0.01 : 0)
                            ->required($item->type === ItemType::Finished)
                            ->suffix('kg')
                            ->live(onBlur: true))
                        ->all()),
                ]),

                Section::make('3. Ringkasan')->schema([
                    Grid::make(2)->schema([
                        Text::make(fn (Get $get) => 'Rendemen: '.number_format(self::yield($get), 2, ',', '.').'%')
                            ->badge()
                            ->color(fn (Get $get) => self::yieldInRange(self::yield($get)) ? 'success' : 'warning'),
                        Text::make(fn (Get $get) => 'Susut: '.number_format(self::shrinkage($get), 2, ',', '.').' kg')
                            ->badge()
                            ->color(fn (Get $get) => self::shrinkage($get) < 0 ? 'danger' : 'gray'),
                    ]),
                ]),

                Section::make('4. Lain-lain')->schema([
                    TextInput::make('machine_downtime_minutes')
                        ->label('Mesin Macet (menit)')
                        ->numeric()
                        ->integer()
                        ->minValue(0),
                    Textarea::make('notes')->label('Catatan'),
                ]),
            ]);
    }

    public static function yieldInRange(float $yield): bool
    {
        return $yield >= config('production.yield_min') && $yield <= config('production.yield_max');
    }

    private static function mainOutputKg(Get $get): float
    {
        $mainId = Item::ofType(ItemType::Finished)->where('is_active', true)->value('id');

        return (float) ($get("outputs.{$mainId}") ?? 0);
    }

    private static function yield(Get $get): float
    {
        $input = (float) $get('input_kg');

        return $input > 0 ? round(self::mainOutputKg($get) / $input * 100, 2) : 0;
    }

    private static function shrinkage(Get $get): float
    {
        return round((float) $get('input_kg') - array_sum(array_map('floatval', (array) $get('outputs'))), 2);
    }
}
