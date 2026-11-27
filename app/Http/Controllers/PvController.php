<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePvRequest;
use App\Http\Requests\UpdatePvRequest;
use App\Models\ActivityLog;
use App\Models\Filiere;
use App\Models\Promotion;
use App\Models\Pv;
use App\Models\User;
use App\Support\PvPresentation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PvController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Pv::class);

        /** @var User $user */
        $user = $request->user();

        $query = Pv::query()
            ->visiblePour($user)
            ->with(['promotion.filiere', 'promotion.annee', 'promotion.semestre.niveau', 'createur'])
            ->when($request->filled('annee'), fn ($query) => $query->whereHas(
                'promotion.annee',
                fn ($annee) => $annee->where('libelle', (string) $request->string('annee'))
            ))
            ->when($request->filled('filiere'), fn ($query) => $query->whereHas(
                'promotion.filiere',
                fn ($filiere) => $filiere->where('code', (string) $request->string('filiere'))
            ))
            ->when($request->filled('niveau'), fn ($query) => $query->whereHas(
                'promotion.semestre.niveau',
                fn ($niveau) => $niveau->where('code', (string) $request->string('niveau'))
            ))
            ->when($request->filled('session'), fn ($query) => $query->where(
                'session',
                strtolower((string) $request->input('session'))
            ))
            ->when($request->filled('statut'), fn ($query) => $query->where(
                'statut',
                $request->string('statut') === 'Archivé' ? 'archive' : 'brouillon'
            ))
            ->when($request->filled('reference'), fn ($query) => $query->where(
                'reference',
                'like',
                '%'.(string) $request->string('reference').'%'
            ));

        $pvs = $query->latest('date_deliberation')->get()->map(fn (Pv $pv): array => PvPresentation::toArray($pv));

        return view('shared.pv.index', [
            'pvs' => $pvs,
            'totalPvs' => $pvs->count(),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        Gate::authorize('create', Pv::class);

        $selection = $this->selection($request);

        if ($selection === null) {
            return redirect()->to(route($request->user()->roleKey().'.pv.index').'#pv-choice-modal')
                ->with('error', 'Choisissez d’abord une filière et une promotion.');
        }

        [$filiere, $promotion] = $selection;

        return view('pv.form', [
            'mode' => 'create',
            'pv' => null,
            'filiere' => $filiere,
            'promotion' => $promotion,
            'reference' => $this->genererReference($promotion, $promotion->semestre->niveau->code),
            'isArchived' => false,
            'canEdit' => true,
        ]);
    }

    public function store(StorePvRequest $request): RedirectResponse
    {
        Gate::authorize('create', Pv::class);

        /** @var Promotion $promotion */
        $promotion = Promotion::with(['filiere', 'annee', 'semestre.niveau'])
            ->findOrFail($request->integer('promotion_id'));
        $niveau = $promotion->semestre->niveau->code;
        $archiver = $request->input('action') === 'archive';

        $pv = Pv::create([
            'reference' => $this->genererReference($promotion, $niveau),
            'promotion_id' => $promotion->id,
            'session' => strtolower((string) $request->input('session')),
            'date_deliberation' => $request->input('date_deliberation'),
            'president_jury' => $request->input('president_jury'),
            'membres_jury' => $request->input('membres_jury', []),
            'statut' => $archiver ? 'archive' : 'brouillon',
            'created_by' => $request->user()->id,
        ] + $this->stockerFichier($request->file('fichier'), $promotion));

        $this->journal('creation', $pv, 'Enregistrement du PV et du scan associé');

        if ($archiver) {
            $this->journal('archivage', $pv, 'PV enregistré définitivement');
        }

        return redirect()->route($request->user()->roleKey().'.pv.index')
            ->with('success', $archiver ? 'PV enregistré et archivé.' : 'PV enregistré comme brouillon.');
    }

    public function edit(Request $request, Pv $pv): View
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isAgent() && $pv->statut === 'archive') {
            abort(403, 'PV archivé : contactez l\'administrateur pour une correction.');
        }

        Gate::authorize('update', $pv);

        $pv->load(['promotion.filiere', 'promotion.annee', 'promotion.semestre.niveau']);
        $promotion = $pv->promotion;
        $isArchived = $pv->statut === 'archive';

        return view('pv.form', [
            'mode' => 'edit',
            'pv' => $pv,
            'filiere' => $promotion->filiere,
            'promotion' => $promotion,
            'reference' => $pv->reference,
            'isArchived' => $isArchived,
            'canEdit' => true,
        ]);
    }

    public function update(UpdatePvRequest $request, Pv $pv): RedirectResponse
    {
        Gate::authorize('update', $pv);

        $data = $request->safe()->only(['date_deliberation', 'president_jury', 'membres_jury']);
        $ancienFichier = null;

        if ($request->hasFile('fichier')) {
            $ancienFichier = $pv->getRawOriginal('fichier_path');
            $data += $this->stockerFichier($request->file('fichier'), $pv->promotion);
        }

        $archiver = $request->input('action') === 'archive' && $pv->statut === 'brouillon';

        if ($archiver) {
            $data['statut'] = 'archive';
        }

        $pv->update($data);

        if ($ancienFichier !== null) {
            Storage::disk('public')->delete($ancienFichier);
        }

        $motif = trim((string) $request->input('motif', ''));
        $description = $motif !== '' ? 'Motif : '.$motif : 'Correction des informations du PV';
        $this->journal('modification', $pv, $description);

        if ($archiver) {
            $this->journal('archivage', $pv, 'PV archivé après correction');
        }

        return redirect()->route($request->user()->roleKey().'.pv.index')
            ->with('success', $archiver ? 'PV corrigé et archivé.' : 'PV mis à jour.');
    }

    public function import(): View
    {
        Gate::authorize('create', Pv::class);

        return view('shared.pv.import');
    }

    /**
     * @return array{Filiere, Promotion}|null
     */
    private function selection(Request $request): ?array
    {
        $filiereId = $request->integer('filiere_id');
        $promotionId = $request->integer('promotion_id');

        if ($filiereId === 0 || $promotionId === 0) {
            return null;
        }

        $filiere = Filiere::find($filiereId);
        $promotion = Promotion::with(['filiere', 'annee', 'semestre.niveau'])->find($promotionId);

        if ($filiere === null || $promotion === null || (int) $promotion->filiere_id !== (int) $filiere->id) {
            return null;
        }

        return [$filiere, $promotion];
    }

    private function genererReference(Promotion $promotion, string $niveau): string
    {
        $anneeFin = substr((string) $promotion->annee->libelle, -4);
        $prefix = sprintf('PV-%s-%s-%s-', $anneeFin, $promotion->filiere->code, $niveau);
        $dernierNumero = Pv::withTrashed()
            ->where('reference', 'like', $prefix.'%')
            ->pluck('reference')
            ->map(fn (string $reference): int => (int) substr($reference, strlen($prefix)))
            ->max() ?? 0;

        return $prefix.str_pad((string) ($dernierNumero + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * @return array<string, string>
     */
    private function stockerFichier(UploadedFile $fichier, Promotion $promotion): array
    {
        $dossier = 'pv/'.$promotion->annee->libelle.'/'.$promotion->filiere->code;

        return [
            'fichier_path' => $fichier->store($dossier, 'public'),
            'fichier_nom_original' => $fichier->getClientOriginalName(),
            'fichier_mime' => $fichier->getMimeType() ?? 'application/octet-stream',
            'fichier_taille' => $fichier->getSize(),
        ];
    }

    private function journal(string $action, Pv $pv, string $description): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entite' => 'pv',
            'entite_id' => $pv->id,
            'description' => $description.' — '.$pv->reference,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
