<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExpenseCategory: string implements HasLabel
{
    case Wages = 'wages';
    case Electricity = 'electricity';
    case Fuel = 'fuel';
    case MachineMaintenance = 'machine_maintenance';
    case Transport = 'transport';
    case Packaging = 'packaging';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Wages => 'Gaji / Upah',
            self::Electricity => 'Listrik',
            self::Fuel => 'Solar / BBM',
            self::MachineMaintenance => 'Perawatan Mesin',
            self::Transport => 'Transportasi',
            self::Packaging => 'Karung & Kemasan',
            self::Other => 'Lainnya',
        };
    }
}
