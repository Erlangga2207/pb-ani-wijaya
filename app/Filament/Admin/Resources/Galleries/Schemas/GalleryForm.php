<?php

namespace App\Filament\Admin\Resources\Galleries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Gallery')
                    ->description('Detail untuk foto/video yang akan ditampilkan di website')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Pemandangan Sawah Indramayu'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Deskripsi singkat (opsional)')
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            Select::make('category')
                                ->label('Kategori')
                                ->options([
                                    'origin' => '🌾 Origin (Sumber Bahan Baku)',
                                    'processing' => '⚙️ Processing (Pengolahan)',
                                    'quality' => '✓ Quality Control',
                                    'logistics' => '📦 Logistics (Distribusi)',
                                    'product' => '🍚 Final Product',
                                    'facility' => '🏭 Facility (Fasilitas)',
                                    'other' => '📷 Other',
                                ])
                                ->default('other')
                                ->required()
                                ->native(false),

                            Select::make('file_type')
                                ->label('Tipe File')
                                ->options([
                                    'image' => '🖼️ Image',
                                    'video' => '🎥 Video',
                                ])
                                ->default('image')
                                ->required()
                                ->live()
                                ->native(false),
                        ]),
                    ]),

                FileUpload::make('file_path')
                    ->label('File')
                    ->required()
                    ->disk('public')
                    ->directory('gallery')
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                        $extension = $file->getClientOriginalExtension();
                        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                        // Strip extension dari filename kalau masih ada (double-safety)
                        $filename = preg_replace('/\.'.preg_quote($extension, '/').'$/i', '', $filename);

                        return Str::slug($filename).'.'.$extension;
                    })
                    // Satu file, state berupa string (bukan array) supaya file_path tidak tersimpan sebagai JSON.
                    ->multiple(false)
                    ->maxSize(fn (Get $get) => $get('file_type') === 'video' ? 51200 : 5120)
                    ->acceptedFileTypes(fn (Get $get) => $get('file_type') === 'video'
                        ? ['video/mp4']
                        : ['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText(fn (Get $get) => $get('file_type') === 'video'
                        ? 'Format: MP4. Maks. 50 MB.'
                        : 'Format: JPG, PNG, WEBP. Maks. 5 MB.')
                    ->columnSpanFull(),

                Section::make('Pengaturan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('order')
                                ->label('Urutan')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->helperText('Angka kecil tampil duluan'),

                            Toggle::make('is_featured')
                                ->label('Foto Unggulan')
                                ->helperText('Tampilkan di slider beranda')
                                ->onColor('warning')
                                ->offColor('gray'),

                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->helperText('Tampilkan di website')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger'),
                        ]),
                    ]),
            ]);
    }
}
