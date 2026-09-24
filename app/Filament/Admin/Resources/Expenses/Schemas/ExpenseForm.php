<?php

namespace App\Filament\Admin\Resources\Expenses\Schemas;

use App\Enums\ExpenseCategory;
use App\Enums\PaymentMethod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('expense_date')->label('Tanggal')->default(today())->maxDate(today())->required(),
                Select::make('category')->label('Kategori')->options(ExpenseCategory::class)->required()->native(false),
                TextInput::make('description')->label('Keterangan')->required()->maxLength(255),
                TextInput::make('amount')->label('Jumlah')->prefix('Rp')->numeric()->minValue(1)->required(),
                Select::make('method')->label('Metode')->options(PaymentMethod::class)->default(PaymentMethod::Cash->value)->required(),
            ]);
    }
}
