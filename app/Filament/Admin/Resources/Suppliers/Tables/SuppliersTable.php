<?php

namespace App\Filament\Admin\Resources\Suppliers\Tables;

use App\Models\Supplier;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('HP')
                    ->searchable(),
                TextColumn::make('village')
                    ->label('Desa')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('district')
                    ->label('Kecamatan')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('village')
                    ->label('Desa')
                    ->options(fn () => Supplier::query()->whereNotNull('village')->distinct()->orderBy('village')->pluck('village', 'village')->all()),
                TernaryFilter::make('is_active')->label('Status Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
