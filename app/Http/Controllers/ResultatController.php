<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use App\Http\Controllers\Concerns\RendersView;
use App\Http\Requests\ImportResultatsRequest;
use App\Http\Requests\LigneRequest;
use App\Models\Etudiant;
use App\Models\LigneResultat;
use App\Models\Pv;
use App\Services\ResultatsImportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

/** Lignes de résultats d'un PV : saisie manuelle et import Excel/CSV. */
class ResultatController extends Controller
{
    use RendersView;

    public function store(LigneRequest $request, Pv $pv)
    {
        Gate::authorize('gererResultats', $pv);
        $d = $request->validated();

        $etudiant = Etudiant::ouCreer($d['matricule'], $d['nom'], $d['prenoms']);

        if ($pv->lignes()->where('etudiant_id', $etudiant->id)->exists()) {
            throw ValidationException::withMessages(['matricule' => 'Cet étudiant figure déjà dans ce PV.']);
        }

        $ligne = $pv->lignes()->create([
            'etudiant_id' => $etudiant->id,
            'moyenne_generale' => $d['moyenne_generale'],
            'mention' => $d['mention'] ?? null,
            'rang' => $d['rang'] ?? null,
            'observations' => $d['observations'] ?? null,
            'decision' => $d['decision'],
        ]);

        return $this->done('pv.show', ['pv' => $pv], 'Ligne ajoutée.', ['ligne' => $ligne->load('etudiant')], 201);
    }

    public function update(LigneRequest $request, Pv $pv, LigneResultat $ligne)
    {
        Gate::authorize('gererResultats', $pv);
        abort_unless($ligne->pv_id === $pv->id, 404);

        $ligne->update($request->validated());

        return $this->done('pv.show', ['pv' => $pv], 'Ligne modifiée.', ['ligne' => $ligne->fresh()->load('etudiant')]);
    }

    public function destroy(Pv $pv, LigneResultat $ligne)
    {
        Gate::authorize('gererResultats', $pv);
        abort_unless($ligne->pv_id === $pv->id, 404);

        $ligne->delete();

        return $this->done('pv.show', ['pv' => $pv], 'Ligne supprimée.');
    }

    public function import(ImportResultatsRequest $request, Pv $pv, ResultatsImportService $service)
    {
        Gate::authorize('gererResultats', $pv);

        $mode = $request->input('mode', config('pv.import_mode', 'tout_ou_rien'));
        $rapport = $service->importer($pv, $request->file('fichier'), $mode);

        // Rapport d'import : page dédiée (ou JSON). 422 si tout est refusé.
        return $this->render('pv.import-rapport', ['pv' => $pv, 'rapport' => $rapport], $rapport['refuse'] ? 422 : 200);
    }

    /** Modèle Excel à télécharger (colonnes imposées). */
    public function modele()
    {
        return Excel::download(
            new ArrayExport(
                [['E2025001', 'ADJOVI', 'Koffi', 12.5, 'admis']],
                ['matricule', 'nom', 'prenoms', 'moyenne_generale', 'decision']
            ),
            'modele_import_resultats.xlsx'
        );
    }
}
