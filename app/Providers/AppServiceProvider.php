<?php

namespace App\Providers;

use App\Models\Pv;
use App\Models\User;
use App\Policies\PvPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Pv::class, PvPolicy::class);

        View::composer('*', function (BladeView $view): void {
            $user = auth()->user();
            $roleKey = $user instanceof User ? $user->roleKey() : 'admin';
            $department = $user instanceof User ? $user->departementName() : 'Administration générale';
            $scopeFilieres = $user instanceof User && $user->isChef() ? $user->departementFilieres() : null;

            $profile = $user instanceof User ? [
                'name' => $user->displayName(),
                'full_name' => trim($user->prenoms.' '.$user->nom),
                'role' => config('ui-roles.roles.'.$roleKey.'.label', 'Utilisateur'),
                'initials' => $user->initials(),
                'email' => $user->email,
                'department' => $department,
            ] : config('ui-roles.roles.admin.profile', []);

            $referentiels = $this->referentiels();

            $shared = [
                'authUser' => $user,
                'roleKey' => $roleKey,
                'profile' => $profile,
                'scopeFilieres' => $scopeFilieres,
                'filieres' => $referentiels['filieres'],
                'annees' => $referentiels['annees'],
                'niveaux' => $referentiels['niveaux'],
                'sessions' => ['Normale', 'Rattrapage'],
                'currentAnnee' => $referentiels['annees'][0] ?? date('Y').'-'.(date('Y') + 1),
            ];

            foreach ($shared as $key => $value) {
                if (! array_key_exists($key, $view->getData())) {
                    $view->with($key, $value);
                }
            }
        });

        View::composer('components.ui.pv-choice-modal', function (BladeView $view): void {
            $user = auth()->user();
            $promotions = $this->promotionsChoisissables();

            if ($user instanceof User && $user->isChef()) {
                $codes = $user->departementFilieres();
                $promotions = array_values(array_filter(
                    $promotions,
                    fn (array $promotion): bool => in_array($promotion['filiere_code'], $codes, true)
                ));
            }

            $view->with('promotionsChoisissables', $promotions);
        });
    }

    /**
     * Référentiels réels quand la base est à jour, jeu de démonstration sinon.
     *
     * @return array{filieres: array<int, array<string, mixed>>, annees: array<int, string>, niveaux: array<int, string>}
     */
    private function referentiels(): array
    {
        if (! Schema::hasTable('filieres') || ! Schema::hasColumn('filieres', 'nom')) {
            return [
                'filieres' => $this->demonstrationFilieres(),
                'annees' => config('ui-demo.annees', []),
                'niveaux' => config('ui-demo.niveaux', []),
            ];
        }

        $filieres = DB::table('filieres')->where('is_active', true)->orderBy('code')
            ->get(['id', 'code', 'nom'])
            ->map(fn ($filiere): array => [
                'id' => (int) $filiere->id,
                'code' => $filiere->code,
                'label' => $filiere->nom,
                'department' => $this->departementFor($filiere->code),
            ])->all();

        $annees = Schema::hasTable('annees_academiques') && Schema::hasColumn('annees_academiques', 'date_fin')
            ? DB::table('annees_academiques')->orderByDesc('libelle')->pluck('libelle')->all()
            : config('ui-demo.annees', []);

        $niveaux = Schema::hasTable('niveaux')
            ? DB::table('niveaux')->orderBy('ordre')->pluck('code')->all()
            : config('ui-demo.niveaux', []);

        return [
            'filieres' => $filieres !== [] ? $filieres : $this->demonstrationFilieres(),
            'annees' => $annees !== [] ? $annees : config('ui-demo.annees', []),
            'niveaux' => $niveaux !== [] ? $niveaux : config('ui-demo.niveaux', []),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function demonstrationFilieres(): array
    {
        return collect(config('ui-demo.filieres', []))
            ->map(fn (array $filiere): array => [
                'id' => $filiere['code'],
                'code' => $filiere['code'],
                'label' => $filiere['label'],
                'department' => $filiere['department'] ?? 'Autres filières',
            ])->all();
    }

    private function departementFor(string $code): string
    {
        foreach (config('ui-roles.departements', []) as $nom => $codes) {
            if (in_array($code, $codes, true)) {
                return $nom;
            }
        }

        return 'Autres filières';
    }

    /** Une promotion affichée = niveau + année académique (jamais de semestre). */
    private function promotionsChoisissables(): array
    {
        if (! Schema::hasTable('promotions') || ! Schema::hasTable('semestres') || ! Schema::hasTable('niveaux')) {
            return $this->promotionsDemonstration();
        }

        $rows = DB::table('promotions')
            ->join('filieres', 'filieres.id', '=', 'promotions.filiere_id')
            ->join('annees_academiques', 'annees_academiques.id', '=', 'promotions.annee_academique_id')
            ->join('semestres', 'semestres.id', '=', 'promotions.semestre_id')
            ->join('niveaux', 'niveaux.id', '=', 'semestres.niveau_id')
            ->where('promotions.est_attendue', true)
            ->orderBy('filieres.code')
            ->orderBy('annees_academiques.libelle', 'desc')
            ->orderBy('niveaux.ordre')
            ->get([
                'promotions.id',
                'filieres.id as filiere_id',
                'filieres.code as filiere_code',
                'annees_academiques.libelle as annee',
                'niveaux.code as niveau',
            ]);

        $seen = [];

        return $rows->filter(function ($row) use (&$seen): bool {
            $key = $row->filiere_id.'|'.$row->annee.'|'.$row->niveau;

            if (isset($seen[$key])) {
                return false;
            }

            $seen[$key] = true;

            return true;
        })->map(fn ($row): array => [
            'id' => (int) $row->id,
            'filiere_id' => (int) $row->filiere_id,
            'filiere_code' => $row->filiere_code,
            'annee' => $row->annee,
            'niveau' => $row->niveau,
            'label' => $row->niveau.', '.$row->annee,
        ])->values()->all();
    }

    /** Reprise de l'ancien jeu d'affichage si la base n'est pas encore migrée. */
    private function promotionsDemonstration(): array
    {
        $promotions = [];
        $id = 1;

        foreach (config('ui-demo.filieres', []) as $filiere) {
            foreach (config('ui-demo.annees', []) as $annee) {
                foreach (config('ui-demo.niveaux', []) as $niveau) {
                    $promotions[] = [
                        'id' => $id++,
                        'filiere_id' => $filiere['code'],
                        'filiere_code' => $filiere['code'],
                        'annee' => $annee,
                        'niveau' => $niveau,
                        'label' => $niveau.', '.$annee,
                    ];
                }
            }
        }

        return $promotions;
    }
}
