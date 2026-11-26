<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Promotion;
use App\Models\Semestre;
use Illuminate\Support\Facades\DB;

/**
 * Génère une promotion par filière active et par semestre pour une année.
 * Idempotent : relancer ne crée pas de doublons.
 */
class PromotionGenerator
{
    public function generer(AnneeAcademique $annee): array
    {
        $creees = 0;
        $existantes = 0;
        $semestres = Semestre::orderBy('code')->get();

        DB::transaction(function () use ($annee, $semestres, &$creees, &$existantes) {
            foreach (Filiere::where('is_active', true)->get() as $filiere) {
                foreach ($semestres as $semestre) {
                    $existe = Promotion::where('annee_academique_id', $annee->id)
                        ->where('filiere_id', $filiere->id)
                        ->where('semestre_id', $semestre->id)
                        ->exists();

                    if ($existe) {
                        $existantes++;

                        continue;
                    }

                    Promotion::create([
                        'annee_academique_id' => $annee->id,
                        'filiere_id' => $filiere->id,
                        'semestre_id' => $semestre->id,
                        'est_attendue' => true,
                    ]);
                    $creees++;
                }
            }
        });

        ActivityLogger::log('generation_promotions', $annee, null, ['creees' => $creees, 'existantes' => $existantes]);

        return ['creees' => $creees, 'existantes' => $existantes];
    }
}
