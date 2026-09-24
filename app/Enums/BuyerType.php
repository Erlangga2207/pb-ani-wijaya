<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BuyerType: string implements HasColor, HasLabel
{
    case Retail = 'retail';
    case Wholesale = 'wholesale';
    case Distributor = 'distributor';
    case Export = 'export';

    public function getLabel(): string
    {
        return match ($this) {
            self::Retail => 'Eceran',
            self::Wholesale => 'Grosir',
            self::Distributor => 'Distributor',
            self::Export => 'Ekspor',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Retail => 'gray',
            self::Wholesale => 'info',
            self::Distributor => 'success',
            self::Export => 'warning',
        };
    }
}
