<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Promotion;
use App\Models\Semestre;
use Illuminate\Support\Facades\DB;

/**
 * Génère les promotions attendues d'une année :
 *  - semestres hors niveau « spécialité » (L1, L2) : une promotion par filière et par semestre ;
 *  - semestres du niveau « spécialité » (L3) : une promotion par spécialité active de la filière
 *    (ou une seule promotion sans spécialité si la filière n'en a aucune).
 * Idempotent : relancer ne crée pas de doublons.
 */
class PromotionGenerator
{
    public function generer(AnneeAcademique $annee): array
    {
        $creees = 0;
        $existantes = 0;
        $niveauSpecialite = config('pv.niveau_specialite', 'L3');

        $semestres = Semestre::with('niveau')->orderBy('code')->get();

        DB::transaction(function () use ($annee, $semestres, $niveauSpecialite, &$creees, &$existantes) {
            $filieres = Filiere::where('is_active', true)->with(['specialites' => fn ($q) => $q->where('is_active', true)])->get();

            foreach ($filieres as $filiere) {
                foreach ($semestres as $semestre) {
                    $specialiteIds = [null];

                    if ($semestre->niveau->code === $niveauSpecialite && $filiere->specialites->isNotEmpty()) {
                        $specialiteIds = $filiere->specialites->pluck('id')->all();
                    }

                    foreach ($specialiteIds as $specialiteId) {
                        $existe = Promotion::where('annee_academique_id', $annee->id)
                            ->where('filiere_id', $filiere->id)
                            ->where('semestre_id', $semestre->id)
                            ->when(
                                $specialiteId,
                                fn ($q) => $q->where('specialite_id', $specialiteId),
                                fn ($q) => $q->whereNull('specialite_id')
                            )->exists();

                        if ($existe) {
                            $existantes++;

                            continue;
                        }

                        Promotion::create([
                            'annee_academique_id' => $annee->id,
                            'filiere_id' => $filiere->id,
                            'semestre_id' => $semestre->id,
                            'specialite_id' => $specialiteId,
                            'est_attendue' => true,
                        ]);
                        $creees++;
                    }
                }
            }
        });

        ActivityLogger::log('generation_promotions', $annee, null, ['creees' => $creees, 'existantes' => $existantes]);

        return ['creees' => $creees, 'existantes' => $existantes];
    }
}
