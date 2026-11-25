@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Fiche étudiant')

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $isChef = $roleKey === 'chef';
        $scopeFilieres = $isChef ? config('ui-roles.roles.chef.profile.filieres', ['IG', 'STAT']) : null;

        $searchRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.recherche.index')
            ? route($roleKey.'.recherche.index')
            : route('admin.recherche.index');

        $filiereOptions = collect(config('ui-demo.filieres', []));

        $filiereLabel = function (string $code) use ($filiereOptions): string {
            $filiere = $filiereOptions->firstWhere('code', $code);

            return is_array($filiere) ? $filiere['label'] : $code;
        };

        $etudiants = collect(config('ui-demo.etudiants', []));
        $etudiant = $etudiants->firstWhere('matricule', $matricule ?? '')
            ?? $etudiants->first()
            ?? [
                'matricule' => $matricule ?? '',
                'nom' => 'Étudiant non trouvé',
                'filiere' => '',
                'niveau' => '',
                'parcours' => [],
            ];

        $parcours = collect($etudiant['parcours'] ?? [])
            ->when($isChef, fn ($items) => $items->whereIn('filiere', $scopeFilieres))
            ->values();

        $decisionTones = [
            'Admis' => 'blue',
            'Rattrapage' => 'gold',
            'Ajourné' => 'red',
            'Exclu' => 'red',
        ];

        $pvs = collect(config('ui-demo.pvs', []))
            ->when($isChef, fn ($items) => $items->whereIn('filiere', $scopeFilieres))
            ->keyBy('reference');

        foreach ($parcours as $etape) {
            if (! $pvs->has($etape['reference'])) {
                $pvs->put($etape['reference'], [
                    'reference' => $etape['reference'],
                    'annee' => $etape['annee'],
                    'filiere' => $etape['filiere'],
                    'niveau' => $etape['niveau'],
                    'session' => $etape['session'],
                    'date' => null,
                    'pages' => 4,
                    'status' => 'Archivé',
                ]);
            }
        }

        $studentPvReferences = $parcours->pluck('reference')->unique()->values();
        $studentPvs = $pvs->filter(fn ($pv, string $reference) => $studentPvReferences->contains($reference))->values();

        $initialsOf = function (string $name): string {
            $words = preg_split('/\s+/', trim($name)) ?: [];

            return mb_strtoupper(mb_substr($words[0] ?? '', 0, 1).mb_substr($words[1] ?? '', 0, 1));
        };

        $drawerIdFor = function (string $reference): string {
            return 'apercu-'.preg_replace('/[^A-Za-z0-9_-]/', '', $reference);
        };

        $promotionOf = function (array $pv) use ($filiereLabel): string {
            return $filiereLabel($pv['filiere']).', '.$pv['niveau'];
        };
    @endphp

    <header class="admin-student-head">
        <x-admin.avatar
            :initials="$initialsOf($etudiant['nom'])"
            size="lg"
            tone="gold"
            :label="'Initiales de '.$etudiant['nom']"
        />
        <div>
            <h1 class="admin-student-head__name">{{ $etudiant['nom'] }}</h1>
            <p class="admin-student-head__meta">
                Matricule {{ $etudiant['matricule'] }} · {{ $filiereLabel($etudiant['filiere']) }}, {{ $etudiant['niveau'] }}
            </p>
        </div>
    </header>

    <section class="admin-section" aria-labelledby="shared-etudiant-parcours-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-etudiant-parcours-title">Parcours</h2>
                <p class="admin-section__hint">Situation académique année par année et par session.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.btn :href="$searchRoute" variant="compact" class="admin-button--secondary" icon="chevron-left" label="Retour à la recherche" />
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h3 class="admin-card__title">Années enregistrées</h3>
                <span class="admin-tiny admin-muted">{{ $parcours->count() }} entrées</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Parcours académique de {{ $etudiant['nom'] }}</caption>
                    <colgroup>
                        <col>
                        <col>
                        <col>
                        <col>
                        <col>
                        <col>
                        <col>
                        <col class="col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">Année académique</th>
                            <th scope="col">Filière</th>
                            <th scope="col">Niveau</th>
                            <th scope="col">Session</th>
                            <th scope="col">Moyenne annuelle</th>
                            <th scope="col">Crédits obtenus</th>
                            <th scope="col">Décision</th>
                            <th scope="col"><span class="sr-only" hidden>PV et aperçu</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parcours as $index => $etape)
                            @php
                                $anneeDebut = (int) substr($etape['annee'], 0, 4);
                                $anneePrecedente = ($anneeDebut - 1).'-'.$anneeDebut;
                                $etapeSuivante = $parcours->get($index + 1);
                                $redoublement = $etapeSuivante !== null
                                    && $etape['niveau'] === $etapeSuivante['niveau']
                                    && $etapeSuivante['annee'] === $anneePrecedente;
                                $decision = $etape['decision'];
                            @endphp
                            <tr class="is-hoverable-row">
                                <td>{{ $etape['annee'] }}</td>
                                <td>{{ $filiereLabel($etape['filiere']) }}</td>
                                <td>
                                    {{ $etape['niveau'] }}
                                    @if ($redoublement)
                                        <x-ui.badge label="Redoublement" tone="gold" />
                                    @endif
                                </td>
                                <td>{{ $etape['session'] }}</td>
                                <td>{{ $etape['moyenne'] }} / 20</td>
                                <td>{{ $etape['credits'] }}</td>
                                <td>
                                    <x-ui.badge :label="$decision" :tone="$decisionTones[$decision] ?? 'neutral'" />
                                </td>
                                <td>
                                    <span class="admin-table__primary">
                                        <x-ui.pv-reference :reference="$etape['reference']" />
                                    </span>
                                    <div data-drawer-open data-target="{{ $drawerIdFor($etape['reference']) }}">
                                        <x-ui.table-actions :reference="$etape['reference']" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="shared-etudiant-pv-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-etudiant-pv-title">PV où figure l'étudiant</h2>
                <p class="admin-section__hint">Procès-verbaux mentionnant {{ $etudiant['nom'] }}.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.badge :label="(string) $studentPvs->count().' PV'" tone="neutral" />
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h3 class="admin-card__title">Documents associés</h3>
            </div>
            <div class="admin-card__body">
                @forelse ($studentPvs as $pv)
                    <div class="admin-missing-card is-hoverable-card">
                        <span>
                            <span class="admin-missing-card__title">
                                <x-ui.pv-reference :reference="$pv['reference']" />
                            </span>
                            <span class="admin-missing-card__meta">
                                {{ $promotionOf($pv) }} · {{ $pv['annee'] }} · Session {{ $pv['session'] }}
                            </span>
                        </span>
                        <div data-drawer-open data-target="{{ $drawerIdFor($pv['reference']) }}">
                            <x-ui.table-actions :reference="$pv['reference']" />
                        </div>
                    </div>
                @empty
                    <p class="admin-empty">Aucun procès-verbal associé à cette fiche.</p>
                @endforelse
            </div>
        </div>
    </section>

    @foreach ($studentPvs as $pv)
        <x-ui.drawer
            :id="$drawerIdFor($pv['reference'])"
            title="Aperçu du procès-verbal"
            :subtitle="$pv['reference'].' — '.$promotionOf($pv)"
            :wide="true"
        >
            <x-ui.viewer
                :reference="$pv['reference']"
                :pages="$pv['pages'] ?? 4"
                :can-download="! $isChef"
            />
        </x-ui.drawer>
    @endforeach
@endsection
