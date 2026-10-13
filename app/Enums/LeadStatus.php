<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Won = 'won';
    case Lost = 'lost';
    case Spam = 'spam';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'Baru',
            self::Contacted => 'Dihubungi',
            self::Won => 'Deal',
            self::Lost => 'Tidak Lanjut',
            self::Spam => 'Spam',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'danger',
            self::Contacted => 'warning',
            self::Won => 'success',
            self::Lost => 'gray',
            self::Spam => 'gray',
        };
    }
}
