<?php

namespace App\Enums;

enum SessionPv: string
{
    case Normale = 'normale';
    case Rattrapage = 'rattrapage';

    public function label(): string
    {
        return match ($this) {
            self::Normale => 'Session normale',
            self::Rattrapage => 'Rattrapage',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
