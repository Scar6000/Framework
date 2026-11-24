<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use App\Models\AnneeAcademique;
use App\Services\ActivityLogger;
use App\Services\PromotionGenerator;
use Illuminate\Support\Facades\DB;

class AnneeAcademiqueController extends Controller
{
    use RendersView;

    /** Passe l'année en « en cours » (une seule à la fois). */
    public function enCours(AnneeAcademique $annee)
    {
        DB::transaction(function () use ($annee) {
            AnneeAcademique::where('est_en_cours', true)->where('id', '!=', $annee->id)->update(['est_en_cours' => false]);
            $annee->forceFill(['est_en_cours' => true])->saveQuietly();
        });
        ActivityLogger::log('changement_annee_en_cours', $annee, null, ['libelle' => $annee->libelle]);

        return $this->done('admin.referentiels.index', [], "Année {$annee->libelle} définie comme année en cours.", ['annee' => $annee->fresh()]);
    }

    /** Bouton « Générer les promotions de l'année ». */
    public function genererPromotions(AnneeAcademique $annee, PromotionGenerator $generateur)
    {
        $res = $generateur->generer($annee);

        return $this->done(
            'admin.promotions.index', ['annee_id' => $annee->id],
            "{$res['creees']} promotion(s) créée(s), {$res['existantes']} déjà existante(s).", $res
        );
    }
}
