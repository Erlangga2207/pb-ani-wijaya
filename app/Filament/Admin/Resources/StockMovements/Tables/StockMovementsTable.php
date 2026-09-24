<?php

namespace App\Filament\Admin\Resources\StockMovements\Tables;

use App\Models\StockMovement;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['item', 'source', 'user']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('movement_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('item.name')->label('Barang'),
                TextColumn::make('direction')
                    ->label('Arah')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'in' ? 'Masuk' : 'Keluar')
                    ->color(fn (string $state) => $state === 'in' ? 'success' : 'danger'),
                TextColumn::make('quantity_kg')->label('Jumlah')->numeric(2)->suffix(' kg'),
                TextColumn::make('balance_after_kg')->label('Saldo')->numeric(2)->suffix(' kg')->weight('bold'),
                // Data biaya tidak dikirim sama sekali ke staff.
                ...(auth()->user()?->isOwnerOrAbove() ? [
                    TextColumn::make('unit_cost')->label('Biaya/kg')->money('IDR')->toggleable(),
                ] : []),
                TextColumn::make('source_number')
                    ->label('Dokumen')
                    ->state(fn (StockMovement $record) => $record->source?->number ?? class_basename($record->source_type))
                    ->url(fn (StockMovement $record) => self::sourceUrl($record))
                    ->color('primary'),
                TextColumn::make('description')->label('Keterangan')->placeholder('-')->wrap()->toggleable(),
                TextColumn::make('user.name')->label('Oleh')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('item_id')->label('Barang')->relationship('item', 'name'),
                SelectFilter::make('direction')->label('Arah')->options(['in' => 'Masuk', 'out' => 'Keluar']),
                Filter::make('movement_date')
                    ->schema([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('movement_date', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('movement_date', '<=', $date))),
            ]);
    }

    // Link ke halaman view dokumen sumber (pembelian, produksi, penjualan, opname).
    public static function sourceUrl(StockMovement $movement): ?string
    {
        $resource = Filament::getModelResource($movement->source_type);

        if (! $resource || ! $resource::hasPage('view') || ! $movement->source || ! $resource::canView($movement->source)) {
            return null;
        }

        return $resource::getUrl('view', ['record' => $movement->source_id]);
    }
}
