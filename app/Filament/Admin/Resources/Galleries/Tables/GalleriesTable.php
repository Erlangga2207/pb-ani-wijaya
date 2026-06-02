<?php

namespace App\Filament\Admin\Resources\Galleries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Preview')
                    ->disk('public')
                    ->square()
                    ->imageSize(60)
                    ->defaultImageUrl(fn ($record) => match ($record->file_type) {
                        'video' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#6b7280"><path d="M8 5v14l11-7z"/></svg>'),
                        default => null,
                    }),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('bold'),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'origin' => '🌾 Origin',
                        'processing' => '⚙️ Processing',
                        'quality' => '✓ Quality',
                        'logistics' => '📦 Logistics',
                        'product' => '🍚 Product',
                        'facility' => '🏭 Facility',
                        'other' => '📷 Other',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'origin' => 'success',
                        'processing' => 'info',
                        'quality' => 'warning',
                        'logistics' => 'primary',
                        'product' => 'danger',
                        'facility' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('file_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'image' => '🖼️ Image',
                        'video' => '🎥 Video',
                    })
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                ToggleColumn::make('is_active')
                    ->label('Aktif'),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'origin' => '🌾 Origin',
                        'processing' => '⚙️ Processing',
                        'quality' => '✓ Quality Control',
                        'logistics' => '📦 Logistics',
                        'product' => '🍚 Final Product',
                        'facility' => '🏭 Facility',
                        'other' => '📷 Other',
                    ]),

                TernaryFilter::make('is_featured')
                    ->label('Foto Unggulan'),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
