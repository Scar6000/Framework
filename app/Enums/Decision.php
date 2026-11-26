<?php

namespace App\Enums;

enum Decision: string
{
    case Admis = 'admis';
    case Ajourne = 'ajourne';
    case Rattrapage = 'rattrapage';
    case Exclu = 'exclu';
    case Absent = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::Admis => 'Admis',
            self::Ajourne => 'Ajourné',
            self::Rattrapage => 'Rattrapage',
            self::Exclu => 'Exclu',
            self::Absent => 'Absent',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
