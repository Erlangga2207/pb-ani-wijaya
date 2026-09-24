<?php

namespace App\Filament\Admin\Resources\StockAdjustments\Tables;

use App\Enums\DocumentStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockAdjustmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('item'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('number')->label('Nomor')->searchable(),
                TextColumn::make('adjustment_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('item.name')->label('Barang'),
                TextColumn::make('system_kg')->label('Sistem')->numeric(2)->suffix(' kg'),
                TextColumn::make('physical_kg')->label('Fisik')->numeric(2)->suffix(' kg'),
                TextColumn::make('difference_kg')
                    ->label('Selisih')
                    ->numeric(2)
                    ->suffix(' kg')
                    ->color(fn ($state) => (float) $state < 0 ? 'danger' : 'success'),
                TextColumn::make('reason')->label('Alasan')->limit(40)->toggleable(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                SelectFilter::make('item_id')->label('Barang')->relationship('item', 'name'),
                SelectFilter::make('status')->label('Status')->options(DocumentStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
