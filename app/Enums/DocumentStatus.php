<?php

namespace App\Enums;

enum DocumentStatus: string
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
