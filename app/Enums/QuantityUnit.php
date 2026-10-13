<?php

namespace App\Enums;

enum QuantityUnit: string
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
