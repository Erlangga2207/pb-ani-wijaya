<?php

namespace App\Filament\Admin\Pages;

use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\Purchases\PurchaseResource;
use App\Models\Purchase;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use UnitEnum;

// Keuangan › Utang ke Petani: pembelian yang belum lunas, urut jatuh tempo.
class Payables extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.admin.pages.table-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Utang ke Petani';

    protected static ?string $title = 'Utang ke Petani';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->can('viewAny', Purchase::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Purchase::query()->posted()->where('payment_status', '!=', PaymentStatus::Paid)->with('supplier'))
            ->defaultSort('due_date')
            ->columns([
                TextColumn::make('number')->label('Nomor')->searchable(),
                TextColumn::make('purchase_date')->label('Tanggal')->date('d M Y'),
                TextColumn::make('supplier.name')->label('Petani')->searchable(),
                TextColumn::make('supplier.village')->label('Desa')->placeholder('-'),
                TextColumn::make('total')->label('Total')->money('IDR'),
                TextColumn::make('paid_amount')->label('Dibayar')->money('IDR'),
                TextColumn::make('remaining')
                    ->label('Sisa Utang')
                    ->state(fn (Purchase $record) => $record->remainingAmount())
                    ->money('IDR')
                    ->weight('bold')
                    ->summarize(Summarizer::make()
                        ->label('Total utang')
                        ->money('IDR')
                        ->using(fn (Builder $query) => $query->sum('total') - $query->sum('paid_amount'))),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable()
                    ->badge()
                    ->color(fn (Purchase $record) => $record->isOverdue() ? 'danger' : 'gray')
                    ->description(fn (Purchase $record) => $record->isOverdue() ? 'Lewat jatuh tempo' : null),
                TextColumn::make('payment_status')->label('Status')->badge(),
            ])
            ->recordUrl(fn (Purchase $record) => PurchaseResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                Action::make('open')
                    ->label('Buka')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (Purchase $record) => PurchaseResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
