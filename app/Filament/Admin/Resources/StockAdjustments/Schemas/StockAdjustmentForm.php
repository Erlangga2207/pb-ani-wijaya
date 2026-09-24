<?php

namespace App\Filament\Admin\Resources\StockAdjustments\Schemas;

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

class StockAdjustmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Stock Opname')
                    ->description('Catat hasil hitung fisik. Selisih dengan stok sistem otomatis menjadi mutasi masuk/keluar.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('adjustment_date')
                                ->label('Tanggal')
                                ->default(today())
                                ->maxDate(today())
                                ->required(),
                            Select::make('item_id')
                                ->label('Barang')
                                ->options(fn () => Item::where('is_active', true)->orderBy('code')->pluck('name', 'id'))
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('system_kg')
                                ->label('Stok Sistem')
                                ->suffix('kg')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('physical_kg')
                                ->label('Stok Fisik (hasil hitung)')
                                ->numeric()
                                ->minValue(0)
                                ->suffix('kg')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                            TextInput::make('difference_kg')
                                ->label('Selisih')
                                ->suffix('kg')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                        Textarea::make('reason')
                            ->label('Alasan / Penjelasan Selisih')
                            ->required()
                            ->maxLength(1000),
                    ]),
            ]);
    }

    private static function recalculate(Get $get, Set $set): void
    {
        $item = Item::find($get('item_id'));
        $system = (float) ($item?->stock_kg ?? 0);
        $set('system_kg', number_format($system, 2, '.', ''));

        if (filled($get('physical_kg'))) {
            $set('difference_kg', number_format((float) $get('physical_kg') - $system, 2, '.', ''));
        }
    }
}
