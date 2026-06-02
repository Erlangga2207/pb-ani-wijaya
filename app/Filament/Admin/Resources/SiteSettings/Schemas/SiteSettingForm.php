<?php

namespace App\Filament\Admin\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengaturan Website')
                    ->description('Ubah nilai untuk menampilkan perubahan di website public.')
                    ->schema([
                        TextInput::make('label')
                            ->label('Label')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Label tidak bisa diubah (sistem)'),

                        TextInput::make('key')
                            ->label('Key')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Key tidak bisa diubah (sistem)'),

                        // Conditional field berdasarkan type
                        TextInput::make('value')
                            ->label('Nilai')
                            ->visible(fn (Get $get) => in_array($get('type'), ['text', 'email', 'phone', 'url']))
                            ->dehydrated(fn (Get $get) => in_array($get('type'), ['text', 'email', 'phone', 'url']))
                            ->email(fn (Get $get) => $get('type') === 'email')
                            ->url(fn (Get $get) => $get('type') === 'url')
                            ->tel(fn (Get $get) => $get('type') === 'phone')
                            ->columnSpanFull(),

                        Textarea::make('value')
                            ->label('Nilai')
                            ->visible(fn (Get $get) => $get('type') === 'textarea')
                            ->dehydrated(fn (Get $get) => $get('type') === 'textarea')
                            ->rows(5)
                            ->columnSpanFull(),

                        FileUpload::make('value')
                            ->label('File')
                            ->visible(fn (Get $get) => $get('type') === 'image')
                            ->dehydrated(fn (Get $get) => $get('type') === 'image')
                            ->afterStateHydrated(function ($component, $state, $record): void {
                                if ($record?->type !== 'image') {
                                    $component->state(null);
                                }
                            })
                            ->disk('public')
                            ->directory('site_settings')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->columnSpanFull(),

                        // Hidden fields (tidak bisa diubah user)
                        TextInput::make('type')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),

                        TextInput::make('group')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),
                    ]),

                Section::make('Informasi')
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi / Petunjuk')
                            ->disabled()
                            ->rows(2)
                            ->placeholder('Tidak ada petunjuk khusus.')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => !empty($get('description')))
                    ->collapsed(),
            ]);
    }
}
