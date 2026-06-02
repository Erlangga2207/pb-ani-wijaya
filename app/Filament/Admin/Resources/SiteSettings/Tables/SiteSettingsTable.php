<?php

namespace App\Filament\Admin\Resources\SiteSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;

class SiteSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultGroup('group')
            ->groups([
                Group::make('group')
                    ->label('Kategori')
                    ->collapsible(),
            ])
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('label')
                    ->label('Pengaturan')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->key),

                TextColumn::make('value')
                    ->label('Nilai')
                    ->limit(60)
                    ->wrap()
                    ->placeholder('— Belum diisi —')
                    ->color(fn ($state) => empty($state) ? 'gray' : null),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'textarea' => 'gray',
                        'email' => 'info',
                        'phone' => 'success',
                        'url' => 'warning',
                        'image' => 'primary',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Kategori')
                    ->options([
                        'general' => 'General',
                        'hero' => 'Hero Section',
                        'about' => 'About',
                        'contact' => 'Contact',
                        'social' => 'Social Media',
                    ]),

                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'email' => 'Email',
                        'phone' => 'Phone',
                        'url' => 'URL',
                        'image' => 'Image',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->paginated([10, 25, 50, 'all']);
    }
}
