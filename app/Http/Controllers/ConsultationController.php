<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\LigneResultat;
use App\Models\Pv;
use App\Models\User;
use App\Support\PvPresentation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Recherche et fiche étudiant alimentées par la base, avec périmètre départemental pour le chef. */
class ConsultationController extends Controller
{
    /** @var array<string, string> */
    private const DECISIONS = [
        'Admis' => 'admis',
        'Rattrapage' => 'rattrapage',
        'Ajourné' => 'ajourne',
        'Exclu' => 'exclu',
        'Absent' => 'absent',
    ];

    public function index(Request $request): View
    {
        $pvResults = $this->pvQuery($request)
            ->get()
            ->map(fn (Pv $pv): array => PvPresentation::toArray($pv));

        $etudiantResults = $this->etudiantQuery($request)
            ->get()
            ->map(fn (Etudiant $etudiant): array => $this->etudiantPresentation($etudiant));

        return view($this->viewName($request, 'index'), [
            'pvResults' => $pvResults,
            'etudiantResults' => $etudiantResults,
            'decisionOptions' => array_keys(self::DECISIONS),
        ]);
    }

    public function show(Request $request, string $matricule): View
    {
        /** @var User $user */
        $user = $request->user();

        $etudiant = Etudiant::query()
            ->where('matricule', $matricule)
            ->whereHas('lignesResultats.pv', fn (Builder $pv): Builder => $pv->visiblePour($user))
            ->with($this->relations())
            ->first();

        if ($etudiant === null) {
            abort(404, 'Étudiant introuvable.');
        }

        return view($this->viewName($request, 'show'), [
            'etudiant' => $this->etudiantPresentation($etudiant),
        ]);
    }

    private function pvQuery(Request $request): Builder
    {
        /** @var User $user */
        $user = $request->user();

        return Pv::query()
            ->visiblePour($user)
            ->with(['promotion.filiere', 'promotion.annee', 'promotion.semestre.niveau', 'createur'])
            ->when($request->filled('q'), fn (Builder $query): Builder => $query->where(
                'reference',
                'like',
                '%'.(string) $request->string('q').'%'
            ))
            ->when($request->filled('annee'), fn (Builder $query): Builder => $query->whereHas(
                'promotion.annee',
                fn (Builder $annee): Builder => $annee->where('libelle', (string) $request->string('annee'))
            ))
            ->when($request->filled('filiere'), fn (Builder $query): Builder => $query->whereHas(
                'promotion.filiere',
                fn (Builder $filiere): Builder => $filiere->where('code', (string) $request->string('filiere'))
            ))
            ->when($request->filled('niveau'), fn (Builder $query): Builder => $query->whereHas(
                'promotion.semestre.niveau',
                fn (Builder $niveau): Builder => $niveau->where('code', (string) $request->string('niveau'))
            ))
            ->when($request->filled('session'), fn (Builder $query): Builder => $query->where(
                'session',
                strtolower((string) $request->string('session'))
            ))
            ->when($request->filled('decision'), fn (Builder $query): Builder => $query->whereHas(
                'lignesResultats',
                fn (Builder $ligne): Builder => $ligne->where('decision', $this->decisionKey((string) $request->string('decision')))
            ))
            ->latest('date_deliberation');
    }

    private function etudiantQuery(Request $request): Builder
    {
        /** @var User $user */
        $user = $request->user();
        $search = '%'.(string) $request->string('q').'%';

        return Etudiant::query()
            ->whereHas('lignesResultats.pv', function (Builder $pv) use ($request, $user): void {
                $pv->visiblePour($user)
                    ->when($request->filled('annee'), fn (Builder $query): Builder => $query->whereHas(
                        'promotion.annee',
                        fn (Builder $annee): Builder => $annee->where('libelle', (string) $request->string('annee'))
                    ))
                    ->when($request->filled('filiere'), fn (Builder $query): Builder => $query->whereHas(
                        'promotion.filiere',
                        fn (Builder $filiere): Builder => $filiere->where('code', (string) $request->string('filiere'))
                    ))
                    ->when($request->filled('niveau'), fn (Builder $query): Builder => $query->whereHas(
                        'promotion.semestre.niveau',
                        fn (Builder $niveau): Builder => $niveau->where('code', (string) $request->string('niveau'))
                    ))
                    ->when($request->filled('session'), fn (Builder $query): Builder => $query->where(
                        'session',
                        strtolower((string) $request->string('session'))
                    ));
            })
            ->when($request->filled('q'), fn (Builder $query): Builder => $query->where(
                fn (Builder $student): Builder => $student->where('nom', 'like', $search)
                    ->orWhere('prenoms', 'like', $search)
                    ->orWhere('matricule', 'like', $search)
            ))
            ->when($request->filled('decision'), fn (Builder $query): Builder => $query->whereHas(
                'lignesResultats',
                fn (Builder $ligne): Builder => $ligne->where('decision', $this->decisionKey((string) $request->string('decision')))
            ))
            ->with($this->relations())
            ->orderBy('nom');
    }

    /**
     * @return array<int, string>
     */
    private function relations(): array
    {
        return [
            'lignesResultats.pv.promotion.filiere',
            'lignesResultats.pv.promotion.annee',
            'lignesResultats.pv.promotion.semestre.niveau',
            'lignesResultats.pv.createur',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function etudiantPresentation(Etudiant $etudiant): array
    {
        $parcours = $etudiant->lignesResultats
            ->sortByDesc(fn (LigneResultat $ligne): string => $ligne->pv?->date_deliberation?->toDateString() ?? '')
            ->map(function (LigneResultat $ligne): array {
                $presentation = $ligne->pv !== null ? PvPresentation::toArray($ligne->pv) : [];

                return [
                    'annee' => $presentation['annee'] ?? '',
                    'filiere' => $presentation['filiere'] ?? '',
                    'niveau' => $presentation['niveau'] ?? '',
                    'session' => $presentation['session'] ?? '',
                    'moyenne' => number_format((float) $ligne->moyenne_generale, 2, ',', ' '),
                    'credits' => null,
                    'decision' => $this->decisionLabel($ligne->decision),
                    'reference' => $presentation['reference'] ?? '',
                ];
            })
            ->values()
            ->all();

        return [
            'matricule' => $etudiant->matricule,
            'nom' => trim($etudiant->prenoms.' '.$etudiant->nom),
            'filiere' => $parcours[0]['filiere'] ?? '',
            'niveau' => $parcours[0]['niveau'] ?? '',
            'parcours' => $parcours,
        ];
    }

    private function viewName(Request $request, string $suffix): string
    {
        if ($suffix === 'index') {
            return $request->user()->isAdmin() ? 'admin.recherche.index' : 'shared.recherche.index';
        }

        return $request->user()->isAdmin() ? 'admin.etudiants.show' : 'shared.recherche.etudiant';
    }

    private function decisionKey(string $label): string
    {
        return self::DECISIONS[$label] ?? strtolower($label);
    }

    private function decisionLabel(string $key): string
    {
        return array_search($key, self::DECISIONS, true) ?: $key;
    }
}
