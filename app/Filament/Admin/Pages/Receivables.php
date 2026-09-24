<?php

namespace App\Filament\Admin\Pages;

use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\Sales\SaleResource;
use App\Models\Sale;
use App\Models\SiteSetting;
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

// Keuangan › Piutang Pembeli: penjualan yang belum lunas, urut jatuh tempo.
class Receivables extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.admin.pages.table-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Piutang Pembeli';

    protected static ?string $title = 'Piutang Pembeli';

    protected static ?int $navigationSort = 1;

    // Daftar piutang semua pembeli: owner ke atas.
    public static function canAccess(): bool
    {
        return auth()->user()->isOwnerOrAbove();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Sale::query()->posted()->where('payment_status', '!=', PaymentStatus::Paid)->with('buyer'))
            ->defaultSort('due_date')
            ->columns([
                TextColumn::make('number')->label('Nomor')->searchable(),
                TextColumn::make('sale_date')->label('Tanggal')->date('d M Y'),
                TextColumn::make('buyer.name')->label('Pembeli')->searchable(),
                TextColumn::make('buyer.city')->label('Kota')->placeholder('-'),
                TextColumn::make('total')->label('Total')->money('IDR'),
                TextColumn::make('paid_amount')->label('Dibayar')->money('IDR'),
                TextColumn::make('remaining')
                    ->label('Sisa Tagihan')
                    ->state(fn (Sale $record) => $record->remainingAmount())
                    ->money('IDR')
                    ->weight('bold')
                    ->summarize(Summarizer::make()
                        ->label('Total piutang')
                        ->money('IDR')
                        ->using(fn (Builder $query) => $query->sum('total') - $query->sum('paid_amount'))),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable()
                    ->badge()
                    ->color(fn (Sale $record) => $record->isOverdue() ? 'danger' : 'gray')
                    ->description(fn (Sale $record) => $record->isOverdue() ? 'Lewat jatuh tempo' : null),
                TextColumn::make('payment_status')->label('Status')->badge(),
            ])
            ->recordUrl(fn (Sale $record) => SaleResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                Action::make('remind')
                    ->label('Ingatkan via WA')
                    ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                    ->color('success')
                    ->visible(fn (Sale $record) => filled($record->buyer?->phone))
                    ->url(fn (Sale $record) => $record->whatsappUrl(sprintf(
                        "Yth. %s,\nkami mengingatkan tagihan nota %s tanggal %s sebesar Rp %s%s.\nTerima kasih.\n- %s",
                        $record->buyer->name,
                        $record->number,
                        $record->sale_date->translatedFormat('d F Y'),
                        number_format($record->remainingAmount(), 0, ',', '.'),
                        $record->due_date ? ' (jatuh tempo '.$record->due_date->translatedFormat('d F Y').')' : '',
                        SiteSetting::get('company_name', 'PB. Ani Wijaya'),
                    )))
                    ->openUrlInNewTab(),
            ]);
    }
}
