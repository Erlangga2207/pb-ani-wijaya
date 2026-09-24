<?php

namespace App\Filament\Admin\Resources\Buyers\Schemas;

use App\Enums\BuyerType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BuyerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::fields());
    }

    // Dipakai juga oleh tombol "pembeli baru" di form penjualan.
    public static function fields(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama Pembeli')
                ->required()
                ->maxLength(255),
            Select::make('type')
                ->label('Tipe')
                ->options(BuyerType::class)
                ->default(BuyerType::Retail)
                ->required()
                ->native(false),
            TextInput::make('phone')
                ->label('No. HP / WhatsApp')
                ->tel(),
            TextInput::make('email')
                ->label('Email')
                ->email(),
            Grid::make(3)->schema([
                TextInput::make('city')->label('Kota / Kabupaten'),
                TextInput::make('province')->label('Provinsi'),
                TextInput::make('country')->label('Negara')->default('Indonesia'),
            ])->columnSpanFull(),
            Textarea::make('address')
                ->label('Alamat Lengkap')
                ->columnSpanFull(),
            Textarea::make('notes')
                ->label('Catatan')
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ];
    }
}
