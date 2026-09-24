<?php

namespace App\Filament\Admin\Resources\Expenses\Schemas;

use App\Models\Expense;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Biaya Operasional')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('number')->label('Nomor'),
                        TextEntry::make('expense_date')->label('Tanggal')->date('d M Y'),
                        TextEntry::make('category')->label('Kategori')->badge(),
                        TextEntry::make('status')->label('Status')->badge(),
                        TextEntry::make('description')->label('Keterangan'),
                        TextEntry::make('amount')->label('Jumlah')->money('IDR')->weight('bold'),
                        TextEntry::make('method')->label('Metode'),
                        TextEntry::make('user.name')->label('Dicatat oleh'),
                    ]),
                Section::make('Pembatalan')
                    ->columns(3)
                    ->visible(fn (Expense $record) => $record->isVoid())
                    ->schema([
                        TextEntry::make('voidedBy.name')->label('Dibatalkan oleh'),
                        TextEntry::make('voided_at')->label('Waktu')->dateTime('d M Y H:i'),
                        TextEntry::make('void_reason')->label('Alasan'),
                    ]),
            ]);
    }
}
