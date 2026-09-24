<?php

namespace App\Filament\Admin\Resources\Items\Tables;

use App\Enums\ItemType;
use App\Models\Item;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('code')
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable()->sortable(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('type')->label('Tipe')->badge(),
                TextColumn::make('stock_kg')
                    ->label('Stok')
                    ->numeric(2)
                    ->suffix(' kg')
                    ->description(fn (Item $record) => '≈ '.number_format($record->stockInSacks(), 1, ',', '.').' karung')
                    ->sortable(),
                TextColumn::make('min_stock_kg')->label('Min. Stok')->numeric(2)->suffix(' kg')->placeholder('-')->toggleable(),
                TextColumn::make('stock_status')
                    ->label('Status Stok')
                    ->badge()
                    ->state(fn (Item $record) => match (true) {
                        (float) $record->stock_kg <= 0 => 'Habis',
                        $record->isLowStock() => 'Menipis',
                        default => 'Aman',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Habis' => 'danger',
                        'Menipis' => 'warning',
                        default => 'success',
                    }),
                // Data biaya tidak dikirim sama sekali ke staff.
                ...(auth()->user()?->isOwnerOrAbove() ? [
                    TextColumn::make('avg_cost_per_kg')->label('Biaya Rata-rata/kg')->money('IDR'),
                ] : []),
            ])
            ->filters([
                SelectFilter::make('type')->label('Tipe')->options(ItemType::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
