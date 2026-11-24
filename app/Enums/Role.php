<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Scolarite = 'scolarite';
    case Chef = 'chef';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Scolarite => 'Agent de scolarité',
            self::Chef => 'Chef de département',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
