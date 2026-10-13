<?php

namespace App\Enums;

enum BuyerType: string
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
