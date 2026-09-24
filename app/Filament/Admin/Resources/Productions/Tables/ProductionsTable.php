<?php

namespace App\Filament\Admin\Resources\Productions\Tables;

use App\Enums\DocumentStatus;
use App\Models\Production;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductionsTable
{
    public static function configure(Table $table): Table
    {
        $posted = fn ($query) => $query->where('status', DocumentStatus::Posted->value);

        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('number')->label('Nomor')->searchable(),
                TextColumn::make('production_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('input_kg')
                    ->label('Gabah')
                    ->numeric(2)
                    ->suffix(' kg')
                    ->summarize(Sum::make()->label('Total')->numeric(2)->suffix(' kg')->query($posted)),
                TextColumn::make('main_output_kg')
                    ->label('Beras')
                    ->numeric(2)
                    ->suffix(' kg')
                    ->summarize(Sum::make()->label('Total')->numeric(2)->suffix(' kg')->query($posted)),
                TextColumn::make('yield_percent')
                    ->label('Rendemen')
                    ->suffix(' %')
                    ->badge()
                    ->color(fn (Production $record) => $record->isYieldOutOfRange() ? 'warning' : 'success')
                    // Rata-rata tertimbang: Σ beras ÷ Σ gabah (bukan rata-rata persen biasa).
                    ->summarize(Summarizer::make()
                        ->label('Rata-rata tertimbang')
                        ->query($posted)
                        ->using(function (\Illuminate\Database\Query\Builder $query) {
                            $input = (float) (clone $query)->sum('input_kg');

                            return $input > 0 ? number_format((float) $query->sum('main_output_kg') / $input * 100, 2, ',', '.').' %' : '-';
                        })),
                TextColumn::make('shrinkage_kg')->label('Susut')->numeric(2)->suffix(' kg'),
                TextColumn::make('machine_downtime_minutes')->label('Macet')->suffix(' mnt')->placeholder('-')->toggleable(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                Filter::make('production_date')
                    ->schema([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('production_date', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('production_date', '<=', $date))),
                SelectFilter::make('status')->label('Status')->options(DocumentStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
