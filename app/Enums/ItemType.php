<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ItemType: string implements HasColor, HasLabel
{
    case Raw = 'raw';
    case Finished = 'finished';
    case Byproduct = 'byproduct';

    public function getLabel(): string
    {
        return match ($this) {
            self::Raw => 'Bahan Baku',
            self::Finished => 'Produk Jadi',
            self::Byproduct => 'Hasil Samping',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Raw => 'warning',
            self::Finished => 'success',
            self::Byproduct => 'info',
        };
    }
}
