<?php

namespace App\Filament\Admin\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pesan dari Pengunjung')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama')
                                ->disabled(),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->disabled(),
                        ]),

                        TextInput::make('subject')
                            ->label('Subjek')
                            ->disabled()
                            ->placeholder('Tidak ada subjek'),

                        Textarea::make('message')
                            ->label('Pesan')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make('Tindakan Admin')
                    ->schema([
                        Toggle::make('is_read')
                            ->label('Sudah Dibaca')
                            ->onColor('success'),

                        Textarea::make('admin_notes')
                            ->label('Catatan Internal')
                            ->placeholder('Catatan untuk tim (tidak terlihat oleh pengirim)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
