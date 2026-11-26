<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use App\Models\AnneeAcademique;
use App\Models\Pv;
use App\Services\ActivityLogger;
use App\Services\PvManquantsService;
use App\Services\PvSearchService;
use App\Services\StatistiquesService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Exports Excel (par défaut) ou PDF (?format=pdf, nécessite barryvdh/laravel-dompdf).
 * Le périmètre du chef est appliqué (sa filière uniquement).
 */
class ExportController extends Controller
{
    public function pv(Request $request, PvSearchService $recherche)
    {
        $pvs = $recherche->query($request->user(), $request->only(PvSearchService::FILTRES))->get();

        $lignes = $pvs->map(fn (Pv $pv) => [
            $pv->reference,
            $pv->promotion->filiere->code,
            $pv->promotion->annee->libelle,
            $pv->promotion->semestre->niveau->code,
            $pv->promotion->semestre->code,
            $pv->session->label(),
            $pv->date_deliberation->format('d/m/Y'),
            $pv->statut->label(),
        ])->all();

        return $this->sortie($request, 'liste_pv', 'Liste des PV de délibération',
            ['Référence', 'Filière', 'Année', 'Niveau', 'Semestre', 'Session', 'Date', 'Statut'], $lignes);
    }

    public function resultats(Request $request, Pv $pv)
    {
        Gate::authorize('view', $pv);

        $lignes = $pv->lignes()->with('etudiant')->get()
            ->sortBy(fn ($l) => $l->etudiant->nom.' '.$l->etudiant->prenoms)
            ->map(fn ($l) => [
                $l->etudiant->matricule, $l->etudiant->nom, $l->etudiant->prenoms,
                $l->moyenne_generale, $l->decision->label(), $l->mention, $l->rang,
            ])->values()->all();

        return $this->sortie($request, 'resultats_'.$pv->reference, 'Résultats du PV '.$pv->reference,
            ['Matricule', 'Nom', 'Prénoms', 'Moyenne générale', 'Décision', 'Mention', 'Rang'], $lignes);
    }

    public function statistiques(Request $request, StatistiquesService $stats)
    {
        $user = $request->user();
        $filiereId = $user->isChef() ? $user->filiere_id : ($request->integer('filiere_id') ?: null);

        $lignes = $stats->tauxReussite($filiereId, $request->integer('annee_id') ?: null)
            ->map(fn ($r) => [$r['annee'], $r['filiere'], $r['total'], $r['admis'], $r['ajournes'], $r['rattrapages'], $r['exclus'], $r['taux_reussite'].' %'])
            ->all();

        return $this->sortie($request, 'statistiques', 'Taux de réussite par filière et par année',
            ['Année', 'Filière', 'Total', 'Admis', 'Ajournés', 'Rattrapages', 'Exclus', 'Taux de réussite'], $lignes);
    }

    public function pvManquants(Request $request, PvManquantsService $service)
    {
        $user = $request->user();
        $annee = $request->filled('annee_id') ? AnneeAcademique::findOrFail($request->input('annee_id')) : AnneeAcademique::enCours();
        abort_unless($annee, 404, 'Aucune année académique en cours.');

        $filiereId = $user->isChef() ? $user->filiere_id : ($request->integer('filiere_id') ?: null);

        $lignes = $service->etats($annee, $filiereId)->map(fn ($e) => [
            $e['promotion']->filiere->code,
            $e['promotion']->semestre->niveau->code,
            $e['promotion']->semestre->code,
            $e['etat_label'],
            $e['en_retard'] ? 'Oui' : 'Non',
            $e['rattrapage_attendu'] ? 'Oui' : 'Non',
        ])->all();

        return $this->sortie($request, 'pv_manquants_'.$annee->libelle, 'État des PV — '.$annee->libelle,
            ['Filière', 'Niveau', 'Semestre', 'État', 'En retard', 'Rattrapage attendu'], $lignes);
    }

    private function sortie(Request $request, string $nom, string $titre, array $entetes, array $lignes)
    {
        $format = $request->input('format', 'xlsx');
        ActivityLogger::log('export', null, null, ['export' => $nom, 'format' => $format, 'lignes' => count($lignes)]);

        if ($format === 'pdf') {
            abort_unless(class_exists(Pdf::class), 501, 'Export PDF indisponible : installez barryvdh/laravel-dompdf.');

            return Pdf::loadView('exports.table', compact('titre', 'entetes', 'lignes'))
                ->setPaper('a4', 'landscape')->download($nom.'.pdf');
        }

        return Excel::download(new ArrayExport($lignes, $entetes), $nom.'.xlsx');
    }
}
