<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuantityUnit: string implements HasLabel
{
    case Kg = 'kg';
    case Sack = 'sack';

    public function getLabel(): string
    {
        return match ($this) {
            self::Kg => 'Kg',
            self::Sack => 'Karung',
        };
    }
}
