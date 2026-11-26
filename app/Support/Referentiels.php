<?php

namespace App\Support;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Semestre;
use App\Models\User;

/** Listes de référence pour alimenter les filtres et formulaires du front. */
class Referentiels
{
    public static function pourFiltres(User $user): array
    {
        $filieres = Filiere::orderBy('code');

        if ($user->isChef()) {
            $filieres->where('id', $user->filiere_id);
        }

        return [
            'annees' => AnneeAcademique::orderByDesc('libelle')->get(),
            'filieres' => $filieres->get(),
            'niveaux' => Niveau::orderBy('ordre')->get(),
            'semestres' => Semestre::with('niveau')->orderBy('code')->get(),
        ];
    }
}
