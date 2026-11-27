<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Etudiant;
use App\Models\Filiere;
use App\Models\Promotion;
use App\Models\Pv;
use App\Models\User;
use App\Support\PvPresentation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        $scope = $user->isChef() ? $user->departementFilieres() : null;
        $pvs = Pv::query()
            ->visiblePour($user)
            ->with(['promotion.filiere', 'promotion.annee', 'promotion.semestre.niveau', 'createur'])
            ->latest('date_deliberation')
            ->get()
            ->map(fn (Pv $pv): array => PvPresentation::toArray($pv));

        $filieres = Filiere::query()
            ->when($scope !== null && $scope !== [], fn ($query) => $query->whereIn('code', $scope))
            ->orderBy('code')
            ->get(['id', 'code', 'nom']);

        $aNumeriser = Promotion::query()
            ->where('est_attendue', true)
            ->when($user->isChef(), fn ($query) => $query->whereIn('filiere_id', $filieres->pluck('id')))
            ->whereDoesntHave('pvs')
            ->count();

        $data = [
            'pvs' => $pvs,
            'drafts' => $pvs->where('status', 'Brouillon')->values(),
            'stats' => [
                'archives' => $pvs->where('status', 'Archivé')->count(),
                'brouillons' => $pvs->where('status', 'Brouillon')->count(),
                'a_numeriser' => $aNumeriser,
                'etudiants' => Etudiant::count(),
            ],
        ];

        if ($user->isAgent()) {
            return view('agent.dashboard', $data);
        }

        $years = ['2023-2024', '2024-2025', '2025-2026'];

        $chartSeries = $filieres->map(fn (Filiere $filiere): array => [
            'code' => $filiere->code,
            'name' => $filiere->nom,
            'values' => collect($years)
                ->map(fn (string $year): int => $pvs->where('filiere', $filiere->code)->where('annee', $year)->count())
                ->all(),
        ])->all();

        $promotionsAttendues = Promotion::query()
            ->where('est_attendue', true)
            ->whereIn('filiere_id', $filieres->pluck('id'))
            ->get(['id', 'filiere_id']);

        $coverage = $filieres->map(function (Filiere $filiere) use ($pvs, $promotionsAttendues): array {
            return [
                'code' => $filiere->code,
                'label' => $filiere->nom,
                'done' => $pvs->where('filiere', $filiere->code)->pluck('niveau')->unique()->count(),
                'expected' => $promotionsAttendues->where('filiere_id', $filiere->id)->count(),
            ];
        })->all();

        return view('chef.dashboard', $data + [
            'scopeFilieres' => $scope,
            'filieres' => $filieres->all(),
            'chartSeries' => $chartSeries,
            'chartYears' => $years,
            'coverage' => $coverage,
        ]);
    }

    private function adminDashboard(): View
    {
        $pvs = Pv::query()
            ->with(['promotion.filiere', 'promotion.annee', 'promotion.semestre.niveau', 'createur'])
            ->latest('date_deliberation')
            ->get()
            ->map(fn (Pv $pv): array => PvPresentation::toArray($pv));

        $filieres = Filiere::query()->orderBy('code')->get(['id', 'code', 'nom']);
        $promotionsAttendues = Promotion::query()->where('est_attendue', true)->get(['id', 'filiere_id']);
        $annees = $pvs->pluck('annee')->unique()->sort()->values()->all();
        $annees = array_slice(array_values(array_unique(array_merge(
            ['2023-2024', '2024-2025', '2025-2026'],
            $annees
        ))), -3);

        $coverage = $filieres->map(fn (Filiere $filiere): array => [
            'code' => $filiere->code,
            'label' => $filiere->nom,
            'done' => $pvs->where('filiere', $filiere->code)->pluck('niveau')->unique()->count(),
            'expected' => $promotionsAttendues->where('filiere_id', $filiere->id)->count(),
        ])->all();

        $chartSeries = $filieres->map(fn (Filiere $filiere): array => [
            'name' => $filiere->nom,
            'values' => collect($annees)
                ->map(fn (string $annee): int => $pvs->where('filiere', $filiere->code)->where('annee', $annee)->count())
                ->all(),
        ])->all();

        $journal = ActivityLog::query()
            ->with('user')
            ->latest('created_at')
            ->take(6)
            ->get()
            ->map(fn (ActivityLog $log): array => [
                'initials' => $log->user?->initials() ?? 'SY',
                'name' => $log->user?->displayName() ?? 'Système',
                'role' => $log->user instanceof User ? config('ui-roles.roles.'.$log->user->roleKey().'.label', 'Utilisateur') : 'Système',
                'action' => ucfirst($log->action),
                'object' => $log->entite === 'pv' ? 'PV' : ucfirst($log->entite),
                'reference' => $log->entite === 'pv' ? (string) $log->description : '',
                'date' => $log->created_at?->format('d/m/Y — H:i') ?? '—',
                'type' => ucfirst($log->action),
                'tone' => $log->action === 'archivage' ? 'dark' : 'gold',
                'highlight' => false,
            ])->all();

        return view('admin.dashboard', [
            'pvs' => $pvs,
            'stats' => [
                'archives' => $pvs->where('status', 'Archivé')->count(),
                'brouillons' => $pvs->where('status', 'Brouillon')->count(),
                'a_numeriser' => Promotion::query()->where('est_attendue', true)->whereDoesntHave('pvs')->count(),
                'etudiants' => Etudiant::count(),
            ],
            'latestPv' => $pvs->take(5)->map(fn (array $pv): array => [
                'reference' => $pv['reference'],
                'promotion' => $pv['filiere'].' · '.$pv['niveau'],
                'annee' => $pv['annee'],
                'session' => $pv['session'],
                'status' => $pv['status'],
                'tone' => $pv['status'] === 'Archivé' ? 'dark' : 'gold',
            ])->all(),
            'filieres' => $filieres->map(fn (Filiere $filiere): array => [
                'name' => $filiere->nom,
                'short' => $filiere->code,
                'done' => collect($coverage)->firstWhere('code', $filiere->code)['done'] ?? 0,
                'expected' => collect($coverage)->firstWhere('code', $filiere->code)['expected'] ?? 0,
            ])->all(),
            'chartSeries' => $chartSeries,
            'chartYears' => $annees,
            'journalRows' => $journal,
            'missingPv' => collect($coverage)
                ->filter(fn (array $item): bool => $item['done'] < $item['expected'])
                ->take(3)
                ->map(fn (array $item): array => [
                    'promotion' => $item['label'],
                    'session' => 'Normale',
                    'annee' => $this->currentAnneeLabel(),
                ])->all(),
        ]);
    }

    private function currentAnneeLabel(): string
    {
        return (string) (config('ui-demo.annees', [])[0] ?? date('Y').'-'.(date('Y') + 1));
    }
}
