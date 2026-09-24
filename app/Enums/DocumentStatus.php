<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentStatus: string implements HasColor, HasLabel
{
    case Posted = 'posted';
    case Void = 'void';

    public function getLabel(): string
    {
        return match ($this) {
            self::Posted => 'Tercatat',
            self::Void => 'Dibatalkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Posted => 'success',
            self::Void => 'gray',
        };
    }
}
