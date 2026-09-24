<?php

namespace App\Filament\Admin\Resources\Purchases\Tables;

use App\Enums\DocumentStatus;
use App\Enums\PaymentStatus;
use App\Models\Supplier;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('supplier'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('number')->label('Nomor')->searchable(),
                TextColumn::make('purchase_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('supplier.name')->label('Petani')->searchable(),
                TextColumn::make('supplier.village')->label('Desa')->placeholder('-'),
                TextColumn::make('quantity_kg')
                    ->label('Berat')
                    ->numeric(2)
                    ->suffix(' kg')
                    ->summarize(Sum::make()->label('Total')->numeric(2)->suffix(' kg')->query(fn ($query) => $query->where('status', DocumentStatus::Posted->value))),
                TextColumn::make('price_per_kg')->label('Harga/kg')->money('IDR'),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->summarize(Sum::make()->label('Total')->money('IDR')->query(fn ($query) => $query->where('status', DocumentStatus::Posted->value))),
                TextColumn::make('payment_status')->label('Status Bayar')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                Filter::make('purchase_date')
                    ->schema([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('purchase_date', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('purchase_date', '<=', $date))),
                SelectFilter::make('supplier_id')->label('Petani')->relationship('supplier', 'name')->searchable()->preload(),
                SelectFilter::make('village')
                    ->label('Desa')
                    ->options(fn () => Supplier::query()->whereNotNull('village')->distinct()->orderBy('village')->pluck('village', 'village')->all())
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'], fn ($q, $village) => $q->whereHas('supplier', fn ($s) => $s->where('village', $village)))),
                SelectFilter::make('payment_status')->label('Status Bayar')->options(PaymentStatus::class),
                SelectFilter::make('status')->label('Status')->options(DocumentStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
