<?php

namespace App\Enums;

enum StatutPv: string
{
    case Brouillon = 'brouillon';
    case Soumis = 'soumis';
    case Valide = 'valide';
    case Archive = 'archive';

    public function label(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Soumis => 'Soumis à validation',
            self::Valide => 'Validé',
            self::Archive => 'Archivé',
        };
    }

    /** Statuts considérés comme officiels (comptent dans les stats et les parcours). */
    public static function officiels(): array
    {
        return [self::Valide->value, self::Archive->value];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
