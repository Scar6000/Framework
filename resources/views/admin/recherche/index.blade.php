@extends('layouts.admin')

@section('title', 'Recherche')

@section('content')
    @php
        $selected = function (string $key, string $value): string {
            return old($key, request($key)) === $value ? 'selected' : '';
        };

        $pvResults = [
            ['reference' => 'PV-2026-IG-L3-014', 'promotion' => 'Informatique de gestion, L3', 'session' => 'Normale', 'date' => '20/11/2026', 'statut' => 'Brouillon', 'tone' => 'slate'],
            ['reference' => 'PV-2026-BF-L2-008', 'promotion' => 'Banque et finance, L2', 'session' => 'Normale', 'date' => '19/11/2026', 'statut' => 'Archivé', 'tone' => 'dark'],
            ['reference' => 'PV-2026-CCA-L3-021', 'promotion' => 'Comptabilité contrôle et audit, L3', 'session' => 'Rattrapage', 'date' => '18/11/2026', 'statut' => 'Brouillon', 'tone' => 'slate'],
            ['reference' => 'PV-2026-GRH-L1-006', 'promotion' => 'Ressources humaines, L1', 'session' => 'Normale', 'date' => '17/11/2026', 'statut' => 'Archivé', 'tone' => 'dark'],
        ];

        $studentResults = [
            ['matricule' => 'ENE-2023-1048', 'name' => 'Adjoa K. Koffi', 'initials' => 'AK', 'filiere' => 'Informatique de gestion', 'niveau' => 'L3', 'decision' => 'Admis', 'tone' => 'blue'],
            ['matricule' => 'ENE-2023-1092', 'name' => 'Mamadou S. Diallo', 'initials' => 'MD', 'filiere' => 'Banque et finance', 'niveau' => 'L3', 'decision' => 'Rattrapage', 'tone' => 'neutral'],
            ['matricule' => 'ENE-2024-1015', 'name' => 'Fatou A. Sow', 'initials' => 'FS', 'filiere' => 'Comptabilité contrôle et audit', 'niveau' => 'L2', 'decision' => 'Ajourné', 'tone' => 'gold'],
            ['matricule' => 'ENE-2024-1173', 'name' => 'Ibrahim B. Traoré', 'initials' => 'IT', 'filiere' => 'Marketing', 'niveau' => 'L2', 'decision' => 'Admis', 'tone' => 'blue'],
            ['matricule' => 'ENE-2025-1064', 'name' => 'Mariam C. Bamba', 'initials' => 'MB', 'filiere' => 'Statistique', 'niveau' => 'L1', 'decision' => 'Exclu', 'tone' => 'red'],
        ];
    @endphp

    <x-admin.page-header
        title="Recherche"
        subtitle="Retrouvez un procès-verbal ou la fiche d'un étudiant de l'ENEAM."
        :breadcrumb="['Accueil', 'Administration', 'Recherche']"
    />

    <section class="admin-search-hero" aria-labelledby="admin-recherche-hero-title">
        <h2 id="admin-recherche-hero-title">Rechercher dans les archives</h2>
        <p>Recherche par nom, par matricule ou par référence de procès-verbal.</p>

        <form method="GET" action="{{ route('admin.recherche.index') }}">
            @csrf
            <div class="admin-search-hero__field">
                <label class="admin-field__label" for="admin-recherche-q" hidden>Rechercher un étudiant (nom ou matricule)</label>
                <input
                    id="admin-recherche-q"
                    class="admin-input"
                    type="search"
                    name="q"
                    placeholder="Rechercher un étudiant (nom ou matricule)"
                    value="{{ old('q', request('q', '')) }}"
                >
                @error('q')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
                <button type="submit" aria-label="Lancer la recherche"><x-admin.icon name="search" /></button>
            </div>
        </form>
    </section>

    <section class="admin-section" aria-labelledby="admin-recherche-filtres-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-recherche-filtres-title">Affiner les résultats</h2>
                <p class="admin-section__hint">Croisez les critères académiques pour limiter l'affichage.</p>
            </div>
        </div>

        <form class="admin-filters" method="GET" action="{{ route('admin.recherche.index') }}">
            @csrf

            <label class="admin-field admin-field--grow" for="admin-recherche-nom">
                <span class="admin-field__label">Nom ou matricule</span>
                <input class="admin-input" id="admin-recherche-nom" name="q" type="search" placeholder="Nom ou matricule" value="{{ old('q', request('q', '')) }}">
            </label>

            <label class="admin-field admin-field--grow {{ $errors->has('annee') ? 'admin-field--invalid' : '' }}" for="admin-recherche-annee">
                <span class="admin-field__label">Année</span>
                <select class="admin-select" id="admin-recherche-annee" name="annee">
                    <option value="">Toutes les années</option>
                    <option value="2023-2024" {{ $selected('annee', '2023-2024') }}>2023-2024</option>
                    <option value="2024-2025" {{ $selected('annee', '2024-2025') }}>2024-2025</option>
                    <option value="2025-2026" {{ $selected('annee', '2025-2026') }}>2025-2026</option>
                </select>
                @error('annee')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field admin-field--grow {{ $errors->has('filiere') ? 'admin-field--invalid' : '' }}" for="admin-recherche-filiere">
                <span class="admin-field__label">Filière</span>
                <select class="admin-select" id="admin-recherche-filiere" name="filiere">
                    <option value="">Toutes les filières</option>
                    <option value="Informatique de gestion" {{ $selected('filiere', 'Informatique de gestion') }}>Informatique de gestion</option>
                    <option value="Banque et finance" {{ $selected('filiere', 'Banque et finance') }}>Banque et finance</option>
                    <option value="Comptabilité contrôle et audit" {{ $selected('filiere', 'Comptabilité contrôle et audit') }}>Comptabilité contrôle et audit</option>
                    <option value="Gestion des ressources humaines" {{ $selected('filiere', 'Gestion des ressources humaines') }}>Gestion des ressources humaines</option>
                    <option value="Marketing" {{ $selected('filiere', 'Marketing') }}>Marketing</option>
                    <option value="Statistique" {{ $selected('filiere', 'Statistique') }}>Statistique</option>
                </select>
                @error('filiere')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field {{ $errors->has('niveau') ? 'admin-field--invalid' : '' }}" for="admin-recherche-niveau">
                <span class="admin-field__label">Niveau</span>
                <select class="admin-select" id="admin-recherche-niveau" name="niveau">
                    <option value="">Tous les niveaux</option>
                    <option value="L1" {{ $selected('niveau', 'L1') }}>L1</option>
                    <option value="L2" {{ $selected('niveau', 'L2') }}>L2</option>
                    <option value="L3" {{ $selected('niveau', 'L3') }}>L3</option>
                </select>
                @error('niveau')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field {{ $errors->has('session') ? 'admin-field--invalid' : '' }}" for="admin-recherche-session">
                <span class="admin-field__label">Session</span>
                <select class="admin-select" id="admin-recherche-session" name="session">
                    <option value="">Toutes les sessions</option>
                    <option value="Normale" {{ $selected('session', 'Normale') }}>Normale</option>
                    <option value="Rattrapage" {{ $selected('session', 'Rattrapage') }}>Rattrapage</option>
                </select>
                @error('session')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field {{ $errors->has('decision') ? 'admin-field--invalid' : '' }}" for="admin-recherche-decision">
                <span class="admin-field__label">Décision</span>
                <select class="admin-select" id="admin-recherche-decision" name="decision">
                    <option value="">Toutes les décisions</option>
                    <option value="admis" {{ $selected('decision', 'admis') }}>Admis</option>
                    <option value="ajourné" {{ $selected('decision', 'ajourné') }}>Ajourné</option>
                    <option value="rattrapage" {{ $selected('decision', 'rattrapage') }}>Rattrapage</option>
                    <option value="exclu" {{ $selected('decision', 'exclu') }}>Exclu</option>
                </select>
                @error('decision')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <x-ui.btn variant="primary" type="submit" icon="filter">Appliquer les filtres</x-ui.btn>
            <x-ui.btn variant="secondary" href="{{ route('admin.recherche.index') }}" icon="refresh">Réinitialiser</x-ui.btn>
        </form>
    </section>

    <section class="admin-section" aria-labelledby="admin-recherche-resultats-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-recherche-resultats-title">Résultats de la recherche</h2>
                <p class="admin-section__hint">4 procès-verbaux et 5 étudiants correspondent aux critères affichés.</p>
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-tabs" role="tablist" aria-label="Résultats de la recherche">
                <button class="admin-tabs__button" id="admin-recherche-tab-pv" type="button" role="tab" aria-selected="true" aria-controls="admin-recherche-panel-pv" data-tab="pv" tabindex="0">
                    PV <x-ui.badge label="4" tone="neutral" />
                </button>
                <button class="admin-tabs__button" id="admin-recherche-tab-etudiants" type="button" role="tab" aria-selected="false" aria-controls="admin-recherche-panel-etudiants" data-tab="etudiants" tabindex="-1">
                    Étudiants <x-ui.badge label="5" tone="neutral" />
                </button>
            </div>

            <div id="admin-recherche-panel-pv" role="tabpanel" aria-labelledby="admin-recherche-tab-pv" data-tab-panel="pv">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>4 procès-verbaux correspondant à la recherche</caption>
                        <colgroup>
                            <col class="col-ref" style="width: 178px;" />
                            <col style="width: 260px;" />
                            <col style="width: 100px;" />
                            <col style="width: 110px;" />
                            <col style="width: 110px;" />
                            <col class="col-actions" style="width: 120px;" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-ref" scope="col">Référence</th>
                                <th scope="col">Filière et niveau</th>
                                <th scope="col">Session</th>
                                <th scope="col">Date</th>
                                <th scope="col">Statut</th>
                                <th class="col-actions" scope="col"><span class="sr-only" hidden>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pvResults as $pv)
                                <tr>
                                    <td class="col-ref">
                                        <a class="admin-table__link" href="{{ route('admin.pv.index', ['reference' => $pv['reference']]) }}">
                                            <x-ui.pv-reference :reference="$pv['reference']" />
                                        </a>
                                    </td>
                                    <td title="{{ $pv['promotion'] }}">{{ $pv['promotion'] }}</td>
                                    <td>{{ $pv['session'] }}</td>
                                    <td class="admin-muted">{{ $pv['date'] }}</td>
                                    <td><x-admin.status :label="$pv['statut']" :tone="$pv['tone']" /></td>
                                    <td class="col-actions">
                                        <x-ui.table-actions :reference="$pv['reference']" :preview="route('admin.pv.index', ['reference' => $pv['reference']])" :actions="[]" empty-message="Ouvrez le registre des PV" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="admin-recherche-panel-etudiants" role="tabpanel" aria-labelledby="admin-recherche-tab-etudiants" data-tab-panel="etudiants" hidden>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>5 étudiants correspondant à la recherche</caption>
                        <colgroup>
                            <col style="width: 150px;" />
                            <col style="width: 220px;" />
                            <col style="width: 240px;" />
                            <col style="width: 90px;" />
                            <col style="width: 110px;" />
                            <col class="col-actions" style="width: 120px;" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col">Matricule</th>
                                <th scope="col">Nom et prénoms</th>
                                <th scope="col">Filière</th>
                                <th scope="col">Niveau</th>
                                <th scope="col">Décision</th>
                                <th class="col-actions" scope="col"><span class="sr-only" hidden>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentResults as $student)
                                <tr>
                                    <td class="admin-muted">{{ $student['matricule'] }}</td>
                                    <td title="{{ $student['name'] }}">
                                        <span class="admin-cell-user">
                                            <x-admin.avatar :initials="$student['initials']" size="sm" tone="soft" :label="$student['name']" />
                                            <span class="admin-cell-user__text">
                                                <span class="admin-table__primary">{{ $student['name'] }}</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td title="{{ $student['filiere'] }}">{{ $student['filiere'] }}</td>
                                    <td>{{ $student['niveau'] }}</td>
                                    <td><x-admin.status :label="$student['decision']" :tone="$student['tone']" /></td>
                                    <td class="col-actions">
                                        <x-ui.table-actions :reference="$student['matricule']" :preview="route('admin.etudiants.show', ['matricule' => $student['matricule']])" :actions="[]" empty-message="Consultez la fiche de l'étudiant" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
