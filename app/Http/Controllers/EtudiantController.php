<?php

namespace App\Http\Controllers;

use App\Enums\StatutPv;
use App\Http\Controllers\Concerns\RendersView;
use App\Models\Etudiant;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class EtudiantController extends Controller
{
    use RendersView;

    public function index(Request $request)
    {
        $etudiants = Etudiant::visiblePour($request->user())
            ->when($request->filled('q'), function ($q) use ($request) {
                $t = '%'.$request->input('q').'%';
                $q->where(fn ($w) => $w->where('matricule', 'like', $t)->orWhere('nom', 'like', $t)->orWhere('prenoms', 'like', $t));
            })
            ->orderBy('nom')->orderBy('prenoms')->paginate(20)->withQueryString();

        return $this->render('etudiants.index', ['etudiants' => $etudiants, 'q' => $request->input('q')]);
    }

    /**
     * Fiche étudiant : parcours construit à partir des PV OFFICIELS (validés/archivés).
     * Un chef ne voit que les lignes des PV de sa filière.
     */
    public function show(Request $request, Etudiant $etudiant)
    {
        $user = $request->user();

        abort_unless(Etudiant::visiblePour($user)->whereKey($etudiant->id)->exists(), 404);

        $lignes = $etudiant->lignes()
            ->whereHas('pv', fn ($p) => $p->visiblePour($user)->whereIn('statut', StatutPv::officiels()))
            ->with(['pv.promotion.annee', 'pv.promotion.filiere', 'pv.promotion.semestre.niveau'])
            ->get();

        $parcours = $lignes->map(function ($l) {
            $p = $l->pv->promotion;

            return [
                'pv_id' => $l->pv->id,
                'reference' => $l->pv->reference,
                'annee' => $p->annee->libelle,
                'filiere' => $p->filiere->code,
                'niveau' => $p->semestre->niveau->code,
                'semestre' => $p->semestre->code,
                'session' => $l->pv->session->value,
                'moyenne_generale' => $l->moyenne_generale,
                'decision' => $l->decision->value,
            ];
        })->sortBy([['annee', 'asc'], ['semestre', 'asc'], ['session', 'asc']])->values();

        // Redoublement : un même niveau suivi sur plusieurs années académiques
        $redoublements = $parcours->groupBy('niveau')
            ->filter(fn ($g) => $g->pluck('annee')->unique()->count() > 1)
            ->keys()->values();

        ActivityLogger::log('consultation', $etudiant);

        return $this->render('etudiants.show', [
            'etudiant' => $etudiant,
            'parcours' => $parcours,
            'redoublements' => $redoublements,
            'derniere_decision' => $parcours->last()['decision'] ?? null,
        ]);
    }
}
