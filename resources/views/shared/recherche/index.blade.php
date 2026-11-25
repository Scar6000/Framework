@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Recherche')

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $isChef = $roleKey === 'chef';
        $scopeFilieres = $isChef ? config('ui-roles.roles.chef.profile.filieres', ['IG', 'STAT']) : null;

        $searchRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.recherche.index')
            ? route($roleKey.'.recherche.index')
            : route('admin.recherche.index');

        $filiereOptions = collect(config('ui-demo.filieres', []))
            ->when($isChef, fn ($items) => $items->whereIn('code', $scopeFilieres))
            ->values();

        $pvResults = collect(config('ui-demo.pvs', []))
            ->when($isChef, fn ($items) => $items->whereIn('filiere', $scopeFilieres))
            ->values();

        $etudiantResults = collect(config('ui-demo.etudiants', []))
            ->when($isChef, fn ($items) => $items->whereIn('filiere', $scopeFilieres))
            ->values();

        $decisionTones = [
            'Admis' => 'blue',
            'Rattrapage' => 'gold',
            'Ajourné' => 'red',
            'Exclu' => 'red',
        ];

        $decisionOptions = collect(config('ui-demo.resultats', []))
            ->pluck('decision')
            ->unique()
            ->values();

        $filterKeys = ['annee', 'filiere', 'niveau', 'session', 'decision'];

        $selectedOption = function (string $key, string $value): string {
            return (string) old($key, request($key, '')) === $value ? 'selected' : '';
        };

        $initialsOf = function (string $name): string {
            $words = preg_split('/\s+/', trim($name)) ?: [];

            return mb_strtoupper(mb_substr($words[0] ?? '', 0, 1).mb_substr($words[1] ?? '', 0, 1));
        };

        $filiereLabel = function (string $code) use ($filiereOptions): string {
            $filiere = $filiereOptions->firstWhere('code', $code);

            return is_array($filiere) ? $filiere['label'] : $code;
        };
    @endphp

    <x-admin.page-header
        title="Recherche"
        subtitle="Retrouvez un procès-verbal ou la fiche d'un étudiant de l'ENEAM."
        :breadcrumb="['Accueil', 'Recherche']"
    />

    <section class="admin-search-hero" aria-labelledby="shared-recherche-hero-title">
        <h2 id="shared-recherche-hero-title">Rechercher dans les archives</h2>
        <p>Saisissez un nom ou un matricule pour affiner la recherche.</p>

        <form method="GET" action="{{ $searchRoute }}" role="search">
            @foreach ($filterKeys as $filterKey)
                @if (old($filterKey, request($filterKey, '')) !== '')
                    <input type="hidden" name="{{ $filterKey }}" value="{{ old($filterKey, request($filterKey, '')) }}">
                @endif
            @endforeach

            @if ($isChef)
                <input type="hidden" name="departement" value="Informatique et Statistique">
            @endif

            <div class="admin-search-hero__field">
                <label class="admin-field__label" for="shared-recherche-q" hidden>Rechercher par nom ou matricule</label>
                <input
                    id="shared-recherche-q"
                    class="admin-input"
                    type="search"
                    name="q"
                    placeholder="Nom ou matricule"
                    value="{{ old('q', request('q', '')) }}"
                >
                @error('q')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
                <button type="submit" aria-label="Lancer la recherche"><x-admin.icon name="search" /></button>
            </div>
        </form>
    </section>

    <section class="admin-section" aria-labelledby="shared-recherche-filtres-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-recherche-filtres-title">Affiner les résultats</h2>
                <p class="admin-section__hint">Croisez les critères académiques pour limiter l'affichage.</p>
            </div>
        </div>

        <form class="admin-filters" method="GET" action="{{ $searchRoute }}">
            <input type="hidden" name="q" value="{{ old('q', request('q', '')) }}">
            @if ($isChef)
                <input type="hidden" name="departement" value="Informatique et Statistique">
            @endif

            <label class="admin-field admin-field--grow" for="shared-recherche-annee">
                <span class="admin-field__label">Année académique</span>
                <select class="admin-select" id="shared-recherche-annee" name="annee">
                    <option value="" @selected(old('annee', request('annee', '')) === '')>Toutes les années</option>
                    @foreach (config('ui-demo.annees', []) as $annee)
                        <option value="{{ $annee }}" @selected($selectedOption('annee', $annee))>{{ $annee }}</option>
                    @endforeach
                </select>
                @error('annee')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field admin-field--grow" for="shared-recherche-filiere">
                <span class="admin-field__label">Filière</span>
                <select class="admin-select" id="shared-recherche-filiere" name="filiere">
                    <option value="" @selected(old('filiere', request('filiere', '')) === '')>Toutes les filières</option>
                    @foreach ($filiereOptions as $filiere)
                        <option value="{{ $filiere['code'] }}" @selected($selectedOption('filiere', $filiere['code']))>{{ $filiere['label'] }}</option>
                    @endforeach
                </select>
                @error('filiere')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field" for="shared-recherche-niveau">
                <span class="admin-field__label">Niveau</span>
                <select class="admin-select" id="shared-recherche-niveau" name="niveau">
                    <option value="" @selected(old('niveau', request('niveau', '')) === '')>Tous les niveaux</option>
                    @foreach (config('ui-demo.niveaux', []) as $niveau)
                        <option value="{{ $niveau }}" @selected($selectedOption('niveau', $niveau))>{{ $niveau }}</option>
                    @endforeach
                </select>
                @error('niveau')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field" for="shared-recherche-session">
                <span class="admin-field__label">Session</span>
                <select class="admin-select" id="shared-recherche-session" name="session">
                    <option value="" @selected(old('session', request('session', '')) === '')>Toutes les sessions</option>
                    @foreach (config('ui-demo.sessions', []) as $session)
                        <option value="{{ $session }}" @selected($selectedOption('session', $session))>{{ $session }}</option>
                    @endforeach
                </select>
                @error('session')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field" for="shared-recherche-decision">
                <span class="admin-field__label">Décision</span>
                <select class="admin-select" id="shared-recherche-decision" name="decision">
                    <option value="" @selected(old('decision', request('decision', '')) === '')>Toutes les décisions</option>
                    @foreach ($decisionOptions as $decision)
                        <option value="{{ $decision }}" @selected($selectedOption('decision', $decision))>{{ $decision }}</option>
                    @endforeach
                </select>
                @error('decision')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            @if ($isChef)
                <div class="admin-field admin-field--grow">
                    <span class="admin-field__label">Département (périmètre fixe)</span>
                    <x-ui.badge tone="neutral">
                        <x-admin.icon name="lock" /> Département : Informatique et Statistique
                    </x-ui.badge>
                </div>
            @endif

            <x-ui.btn variant="compact" icon="filter" type="submit" label="Appliquer les filtres" />
            <x-ui.btn :href="$searchRoute" variant="compact" class="admin-button--secondary" icon="refresh" label="Réinitialiser" />
        </form>
    </section>

    <section class="admin-section" aria-labelledby="shared-recherche-resultats-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-recherche-resultats-title">Résultats de la recherche</h2>
                <p class="admin-section__hint">
                    {{ $pvResults->count() }} procès-verbaux et {{ $etudiantResults->count() }} étudiants correspondent aux critères affichés.
                </p>
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-tabs" role="tablist" aria-label="Résultats de la recherche">
                <button
                    class="admin-tabs__button"
                    id="shared-recherche-tab-pv"
                    type="button"
                    role="tab"
                    aria-selected="true"
                    aria-controls="shared-recherche-panel-pv"
                    aria-current="true"
                    data-tab="pv"
                    tabindex="0"
                >
                    PV <x-ui.badge :label="(string) $pvResults->count()" tone="neutral" />
                </button>
                <button
                    class="admin-tabs__button"
                    id="shared-recherche-tab-etudiants"
                    type="button"
                    role="tab"
                    aria-selected="false"
                    aria-controls="shared-recherche-panel-etudiants"
                    data-tab="etudiants"
                    tabindex="-1"
                >
                    Étudiants <x-ui.badge :label="(string) $etudiantResults->count()" tone="neutral" />
                </button>
            </div>

            <div id="shared-recherche-panel-pv" role="tabpanel" aria-labelledby="shared-recherche-tab-pv" data-tab-panel="pv">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>Procès-verbaux correspondant aux critères de recherche</caption>
                        <colgroup>
                            <col class="col-ref">
                            <col>
                            <col>
                            <col class="col-date">
                            <col>
                            <col class="col-actions">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col">Référence</th>
                                <th scope="col">Promotion</th>
                                <th scope="col">Session</th>
                                <th scope="col">Date de délibération</th>
                                <th scope="col">Statut</th>
                                <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pvResults as $pv)
                                @php
                                    $drawerId = 'apercu-'.preg_replace('/[^A-Za-z0-9_-]/', '', $pv['reference']);
                                @endphp
                                <tr class="is-hoverable-row">
                                    <td><x-ui.pv-reference :reference="$pv['reference']" /></td>
                                    <td>
                                        <span class="admin-table__primary">{{ $filiereLabel($pv['filiere']) }}</span>
                                        <span class="admin-table__subtext">{{ $pv['niveau'] }} · {{ $pv['annee'] }}</span>
                                    </td>
                                    <td>{{ $pv['session'] }}</td>
                                    <td class="admin-muted">{{ \Illuminate\Support\Carbon::parse($pv['date'])->format('d/m/Y') }}</td>
                                    <td>
                                        <x-ui.badge :label="$pv['status']" :tone="$pv['status'] === 'Archivé' ? 'dark' : 'gold'" />
                                    </td>
                                    <td>
                                        <div data-drawer-open data-target="{{ $drawerId }}">
                                            <x-ui.table-actions :reference="$pv['reference']" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="shared-recherche-panel-etudiants" role="tabpanel" aria-labelledby="shared-recherche-tab-etudiants" data-tab-panel="etudiants" hidden>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>Étudiants correspondant aux critères de recherche</caption>
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col class="col-actions">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col">Matricule</th>
                                <th scope="col">Nom et prénoms</th>
                                <th scope="col">Filière</th>
                                <th scope="col">Niveau</th>
                                <th scope="col">Décision</th>
                                <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($etudiantResults as $etudiant)
                                @php
                                    $decision = $etudiant['parcours'][0]['decision'] ?? 'Admis';
                                @endphp
                                <tr class="is-hoverable-row">
                                    <td class="admin-muted">{{ $etudiant['matricule'] }}</td>
                                    <td>
                                        <span class="admin-cell-user">
                                            <x-admin.avatar :initials="$initialsOf($etudiant['nom'])" size="sm" tone="soft" :label="$etudiant['nom']" />
                                            <span class="admin-cell-user__text">
                                                <span class="admin-table__primary">{{ $etudiant['nom'] }}</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td>{{ $filiereLabel($etudiant['filiere']) }}</td>
                                    <td>{{ $etudiant['niveau'] }}</td>
                                    <td>
                                        <x-ui.badge :label="$decision" :tone="$decisionTones[$decision] ?? 'neutral'" />
                                    </td>
                                    <td>
                                        <x-ui.btn
                                            :href="route($roleKey.'.etudiants.show', ['matricule' => $etudiant['matricule']])"
                                            variant="compact"
                                            icon="eye"
                                            label="Voir la fiche"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @foreach ($pvResults as $pv)
        @php
            $drawerId = 'apercu-'.preg_replace('/[^A-Za-z0-9_-]/', '', $pv['reference']);
        @endphp
        <x-ui.drawer
            :id="$drawerId"
            title="Aperçu du procès-verbal"
            :subtitle="$pv['reference'].' — '.$filiereLabel($pv['filiere'])"
        >
            <x-ui.viewer
                :reference="$pv['reference']"
                :pages="$pv['pages'] ?? 4"
                :can-download="! $isChef"
            />
        </x-ui.drawer>
    @endforeach
@endsection
