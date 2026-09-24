<?php

namespace App\Filament\Admin\Resources\Sales\Tables;

use App\Enums\DocumentStatus;
use App\Enums\PaymentStatus;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('buyer')->withSum('items', 'quantity_kg');

                // Staff hanya melihat nota yang dia buat sendiri (sesuai SalePolicy::view).
                if (! auth()->user()->isOwnerOrAbove()) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('number')->label('Nomor')->searchable(),
                TextColumn::make('sale_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('buyer.name')->label('Pembeli')->searchable(),
                TextColumn::make('buyer.city')->label('Kota')->placeholder('-'),
                TextColumn::make('items_sum_quantity_kg')->label('Total Berat')->numeric(2)->suffix(' kg'),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->summarize(Sum::make()->label('Total')->money('IDR')->query(fn ($query) => $query->where('status', DocumentStatus::Posted->value))),
                TextColumn::make('payment_status')->label('Status Bayar')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                Filter::make('sale_date')
                    ->schema([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('sale_date', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('sale_date', '<=', $date))),
                SelectFilter::make('buyer_id')->label('Pembeli')->relationship('buyer', 'name')->searchable()->preload(),
                SelectFilter::make('payment_status')->label('Status Bayar')->options(PaymentStatus::class),
                SelectFilter::make('status')->label('Status')->options(DocumentStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
