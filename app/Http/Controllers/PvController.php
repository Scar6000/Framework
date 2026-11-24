<?php

namespace App\Http\Controllers;

use App\Enums\Decision;
use App\Enums\SessionPv;
use App\Enums\StatutPv;
use App\Http\Controllers\Concerns\RendersView;
use App\Http\Requests\StorePvRequest;
use App\Http\Requests\UpdatePvRequest;
use App\Models\AnneeAcademique;
use App\Models\Promotion;
use App\Models\Pv;
use App\Services\ActivityLogger;
use App\Services\PvSearchService;
use App\Support\Referentiels;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PvController extends Controller
{
    use RendersView;

    public function index(Request $request, PvSearchService $recherche)
    {
        Gate::authorize('viewAny', Pv::class);

        $pvs = $recherche->query($request->user(), $request->only(PvSearchService::FILTRES))
            ->paginate(15)->withQueryString();

        return $this->render('pv.index', [
            'pvs' => $pvs,
            'filtres' => $request->only(PvSearchService::FILTRES),
            'references' => Referentiels::pourFiltres($request->user()),
            'statuts' => StatutPv::cases(),
            'sessions' => SessionPv::cases(),
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Pv::class);

        return $this->render('pv.form', [
            'pv' => null,
            'promotions' => $this->promotionsSaisissables($request),
            'sessions' => SessionPv::cases(),
        ]);
    }

    public function store(StorePvRequest $request)
    {
        Gate::authorize('create', Pv::class);

        $promotion = Promotion::with(['filiere', 'annee', 'semestre', 'specialite'])->findOrFail($request->input('promotion_id'));
        $session = SessionPv::from($request->input('session'));

        $pv = Pv::create([
            'reference' => $request->input('reference') ?: $this->genererReference($promotion, $session),
            'promotion_id' => $promotion->id,
            'session' => $session,
            'date_deliberation' => $request->input('date_deliberation'),
            'president_jury' => $request->input('president_jury'),
            'membres_jury' => $request->input('membres_jury'),
            'statut' => StatutPv::Brouillon,
            'created_by' => $request->user()->id,
        ] + $this->stockerFichier($request->file('fichier'), $promotion));

        return $this->done('pv.show', ['pv' => $pv], 'PV créé en brouillon. Ajoutez maintenant les résultats.', ['pv' => $pv], 201);
    }

    public function show(Request $request, Pv $pv)
    {
        Gate::authorize('view', $pv);
        $user = $request->user();

        $pv->load(['promotion.filiere', 'promotion.annee', 'promotion.semestre.niveau', 'promotion.specialite', 'createur', 'validateur']);

        $lignes = $pv->lignes()->with('etudiant')
            ->join('etudiants', 'etudiants.id', '=', 'lignes_resultats.etudiant_id')
            ->orderBy('etudiants.nom')->orderBy('etudiants.prenoms')
            ->select('lignes_resultats.*')
            ->paginate(100)->withQueryString();

        ActivityLogger::log('consultation', $pv);

        return $this->render('pv.show', [
            'pv' => $pv,
            'lignes' => $lignes,
            'decisions' => Decision::cases(),
            // Boutons à afficher côté front
            'permissions' => [
                'modifier' => $user->can('update', $pv),
                'gerer_resultats' => $user->can('gererResultats', $pv),
                'soumettre' => $user->can('soumettre', $pv),
                'valider' => $user->can('valider', $pv),
                'rejeter' => $user->can('rejeter', $pv),
                'archiver' => $user->can('archiver', $pv),
                'supprimer' => $user->can('delete', $pv),
                'partager' => $user->can('partager', $pv),
                'telecharger' => (bool) config('pv.telechargement_autorise'),
            ],
        ]);
    }

    public function edit(Pv $pv)
    {
        Gate::authorize('update', $pv);

        return $this->render('pv.form', ['pv' => $pv->load('promotion'), 'promotions' => [], 'sessions' => SessionPv::cases()]);
    }

    public function update(UpdatePvRequest $request, Pv $pv)
    {
        Gate::authorize('update', $pv);

        $data = $request->safe()->only(['reference', 'date_deliberation', 'president_jury', 'membres_jury']);
        if (blank($data['reference'] ?? null)) {
            unset($data['reference']);
        }

        $ancienFichier = null;
        if ($request->hasFile('fichier')) {
            $ancienFichier = $pv->getRawOriginal('fichier_path');
            $data += $this->stockerFichier($request->file('fichier'), $pv->promotion()->with(['filiere', 'annee'])->first());
        }

        $pv->update($data);

        if ($ancienFichier) {
            Storage::disk(config('pv.disk'))->delete($ancienFichier);
        }

        return $this->done('pv.show', ['pv' => $pv], 'PV mis à jour.', ['pv' => $pv->fresh()]);
    }

    /** Suppression douce (admin) : le fichier est conservé. */
    public function destroy(Pv $pv)
    {
        Gate::authorize('delete', $pv);
        $pv->delete();

        return $this->done('pv.index', [], 'PV supprimé.');
    }

    /** Promotions sur lesquelles on peut créer un PV : année en cours, attendues, sans PV de cette session. */
    private function promotionsSaisissables(Request $request)
    {
        $annee = AnneeAcademique::enCours();

        return Promotion::with(['filiere', 'semestre.niveau', 'specialite', 'annee'])
            ->when($annee, fn ($q) => $q->where('annee_academique_id', $annee->id))
            ->where('est_attendue', true)
            ->orderBy('filiere_id')->orderBy('semestre_id')
            ->get();
    }

    private function genererReference(Promotion $p, SessionPv $session): string
    {
        $base = collect(['PV', $p->filiere->code, $p->annee->libelle, $p->semestre->code, $p->specialite?->code, strtoupper($session->value)])
            ->filter()->implode('-');

        $ref = $base;
        $i = 2;
        while (Pv::withTrashed()->where('reference', $ref)->exists()) {
            $ref = $base.'-'.$i++;
        }

        return $ref;
    }

    /** Stockage PRIVÉ, nom aléatoire (jamais le nom d'origine). */
    private function stockerFichier(UploadedFile $fichier, Promotion $promotion): array
    {
        $dossier = 'pv/'.$promotion->annee->libelle.'/'.$promotion->filiere->code;

        return [
            'fichier_path' => $fichier->store($dossier, config('pv.disk')),
            'fichier_nom_original' => $fichier->getClientOriginalName(),
            'fichier_mime' => $fichier->getMimeType(),
            'fichier_taille' => $fichier->getSize(),
        ];
    }
}
