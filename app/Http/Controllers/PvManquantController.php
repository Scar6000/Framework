<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Services\PvManquantsService;
use Illuminate\Http\Request;

/** Détection des PV manquants (obligatoire). Le chef ne voit que sa filière. */
class PvManquantController extends Controller
{
    use RendersView;

    public function index(Request $request, PvManquantsService $service)
    {
        $user = $request->user();
        $annee = $request->filled('annee_id')
            ? AnneeAcademique::findOrFail($request->input('annee_id'))
            : AnneeAcademique::enCours();

        if (! $annee) {
            return $this->render('pv-manquants.index', ['annee' => null, 'resume' => null, 'etats' => [], 'message' => 'Aucune année académique en cours.']);
        }

        $filiereId = $user->isChef() ? $user->filiere_id : ($request->integer('filiere_id') ?: null);
        $etats = $service->etats($annee, $filiereId);

        if ($request->filled('etat')) {
            $etats = $etats->where('etat', $request->input('etat'))->values();
        }
        if ($request->boolean('en_retard')) {
            $etats = $etats->where('en_retard', true)->values();
        }

        return $this->render('pv-manquants.index', [
            'annee' => $annee,
            'resume' => $service->resume($annee, $filiereId),
            'etats' => $etats,
            'annees' => AnneeAcademique::orderByDesc('libelle')->get(),
            'filieres' => $user->isChef() ? Filiere::where('id', $filiereId)->get() : Filiere::orderBy('code')->get(),
        ]);
    }
}
