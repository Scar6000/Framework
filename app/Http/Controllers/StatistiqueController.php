<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Services\PvManquantsService;
use App\Services\StatistiquesService;
use Illuminate\Http\Request;

/** Statistiques : admin = toutes les filières ; chef = uniquement la sienne. */
class StatistiqueController extends Controller
{
    use RendersView;

    public function index(Request $request, StatistiquesService $stats, PvManquantsService $manquants)
    {
        $user = $request->user();
        $filiereId = $user->isChef() ? $user->filiere_id : ($request->integer('filiere_id') ?: null);
        $anneeId = $request->integer('annee_id') ?: null;

        $annee = $anneeId ? AnneeAcademique::find($anneeId) : AnneeAcademique::enCours();

        return $this->render('statistiques.index', [
            'taux_reussite' => $stats->tauxReussite($filiereId, $anneeId),
            'repartition_decisions' => $stats->repartitionDecisions($filiereId, $anneeId),
            'resume_pv' => $annee ? $manquants->resume($annee, $filiereId) : null,
            'filtres' => ['filiere_id' => $filiereId, 'annee_id' => $anneeId],
            'annees' => AnneeAcademique::orderByDesc('libelle')->get(),
            'filieres' => $user->isChef() ? Filiere::where('id', $filiereId)->get() : Filiere::orderBy('code')->get(),
        ]);
    }
}
