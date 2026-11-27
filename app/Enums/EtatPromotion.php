<?php

namespace App\Enums;

enum EtatPromotion: string
{
    case Manquant = 'manquant';
    case EnCours = 'en_cours';
    case Complet = 'complet';
    case NonApplicable = 'non_applicable';

    public function label(): string
    {
        return match ($this) {
            self::Manquant => 'Manquant',
            self::EnCours => 'En cours',
            self::Complet => 'Complet',
            self::NonApplicable => 'Non applicable',
        };
    }
}
