<?php

namespace App\Filament\Admin\Resources\ContactMessages\Schemas;

use App\Enums\LeadStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Pesan dari Pengunjung')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label('Nama')->disabled(),
                            TextInput::make('email')->label('Email')->disabled(),
                        ]),
                        TextInput::make('subject')->label('Subjek')->disabled()->placeholder('Tidak ada subjek'),
                        Textarea::make('message')->label('Pesan')->disabled()->rows(6),
                    ]),

                Section::make('Tindak Lanjut')
                    ->description('Diisi admin setelah menghubungi calon pembeli.')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('status')->label('Status')->options(LeadStatus::class)->required()->native(false),
                            TextInput::make('phone')->label('No. HP / WhatsApp')->tel(),
                            TextInput::make('company')->label('Perusahaan'),
                            TextInput::make('country')->label('Negara'),
                            Toggle::make('is_read')->label('Sudah Dibaca')->onColor('success'),
                        ]),
                        Textarea::make('admin_notes')
                            ->label('Catatan Internal')
                            ->placeholder('Catatan untuk tim (tidak terlihat oleh pengirim)')
                            ->rows(3),
                    ]),
            ]);
    }
}
