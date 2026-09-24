<?php

namespace App\Filament\Admin\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::fields());
    }

    // Dipakai juga oleh tombol "petani baru" di form pembelian.
    public static function fields(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama Petani / Pemasok')
                ->required()
                ->maxLength(255),
            TextInput::make('phone')
                ->label('No. HP / WhatsApp')
                ->tel(),
            Grid::make(3)->schema([
                TextInput::make('village')->label('Desa'),
                TextInput::make('district')->label('Kecamatan'),
                TextInput::make('city')->label('Kota / Kabupaten'),
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
