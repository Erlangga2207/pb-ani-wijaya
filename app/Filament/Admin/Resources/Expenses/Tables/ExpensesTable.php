<?php

namespace App\Filament\Admin\Resources\Expenses\Tables;

use App\Enums\DocumentStatus;
use App\Enums\ExpenseCategory;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('number')->label('Nomor')->searchable(),
                TextColumn::make('expense_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('description')->label('Keterangan')->searchable()->limit(40),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->summarize(Sum::make()->label('Total')->money('IDR')->query(fn ($query) => $query->where('status', DocumentStatus::Posted->value))),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                Filter::make('expense_date')
                    ->schema([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('expense_date', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('expense_date', '<=', $date))),
                SelectFilter::make('category')->label('Kategori')->options(ExpenseCategory::class),
                SelectFilter::make('status')->label('Status')->options(DocumentStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
