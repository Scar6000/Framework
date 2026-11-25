@extends('layouts.admin')

@section('title', 'Recherche')

@section('content')
    @php
        $selected = function (string $key, string $value): string {
            return old($key, request($key)) === $value ? 'selected' : '';
        };

        $pvResults = [
            ['reference' => 'PV-2026-IG-L3-S2-014', 'promotion' => 'Informatique de gestion, L3', 'session' => 'Normale', 'date' => '20/11/2026', 'statut' => 'Validé', 'tone' => 'blue'],
            ['reference' => 'PV-2026-BF-L2-S1-008', 'promotion' => 'Banque et finance, L2', 'session' => 'Normale', 'date' => '19/11/2026', 'statut' => 'Brouillon', 'tone' => 'slate'],
            ['reference' => 'PV-2026-CCA-L3-S1-021', 'promotion' => 'Comptabilité contrôle et audit, L3', 'session' => 'Rattrapage', 'date' => '18/11/2026', 'statut' => 'Validé', 'tone' => 'blue'],
            ['reference' => 'PV-2026-GRH-L1-S2-006', 'promotion' => 'Ressources humaines, L1', 'session' => 'Normale', 'date' => '17/11/2026', 'statut' => 'Archivé', 'tone' => 'dark'],
        ];

        $studentResults = [
            ['matricule' => 'ENE-2023-1048', 'name' => 'Adjoa K. Koffi', 'initials' => 'AK', 'filiere' => 'Informatique de gestion', 'niveau' => 'L3', 'decision' => 'Admis', 'tone' => 'blue'],
            ['matricule' => 'ENE-2023-1092', 'name' => 'Mamadou S. Diallo', 'initials' => 'MD', 'filiere' => 'Banque et finance', 'niveau' => 'L3', 'decision' => 'Rattrapage', 'tone' => 'lightgold'],
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
            <input type="hidden" name="q" value="{{ old('q', request('q', '')) }}">

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

            <label class="admin-field {{ $errors->has('semestre') ? 'admin-field--invalid' : '' }}" for="admin-recherche-semestre">
                <span class="admin-field__label">Semestre</span>
                <select class="admin-select" id="admin-recherche-semestre" name="semestre">
                    <option value="">Tous les semestres</option>
                    <option value="S1" {{ $selected('semestre', 'S1') }}>S1</option>
                    <option value="S2" {{ $selected('semestre', 'S2') }}>S2</option>
                </select>
                @error('semestre')
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

            <button class="admin-button admin-button--small" type="submit">
                <x-admin.icon name="filter" /> Appliquer les filtres
            </button>
            <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.recherche.index') }}">
                <x-admin.icon name="refresh" /> Réinitialiser
            </a>
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
                    PV <span class="admin-tiny">(4)</span>
                </button>
                <button class="admin-tabs__button" id="admin-recherche-tab-etudiants" type="button" role="tab" aria-selected="false" aria-controls="admin-recherche-panel-etudiants" data-tab="etudiants" tabindex="-1">
                    Étudiants <span class="admin-tiny">(5)</span>
                </button>
            </div>

            <div id="admin-recherche-panel-pv" role="tabpanel" aria-labelledby="admin-recherche-tab-pv" data-tab-panel="pv">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>4 procès-verbaux correspondant à la recherche</caption>
                        <thead>
                            <tr>
                                <th scope="col">Référence</th>
                                <th scope="col">Promotion</th>
                                <th scope="col">Session</th>
                                <th scope="col">Date</th>
                                <th scope="col">Statut</th>
                                <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pvResults as $pv)
                                <tr>
                                    <td><a class="admin-table__link" href="{{ route('admin.pv.index') }}">{{ $pv['reference'] }}</a></td>
                                    <td>{{ $pv['promotion'] }}</td>
                                    <td>{{ $pv['session'] }}</td>
                                    <td class="admin-muted">{{ $pv['date'] }}</td>
                                    <td><x-admin.status :label="$pv['statut']" :tone="$pv['tone']" /></td>
                                    <td>
                                        <div class="admin-table__actions">
                                            <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.pv.index') }}">
                                                <x-admin.icon name="eye" /> Ouvrir le scan
                                            </a>
                                        </div>
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
                            @foreach ($studentResults as $student)
                                <tr>
                                    <td class="admin-muted">{{ $student['matricule'] }}</td>
                                    <td>
                                        <span class="admin-cell-user">
                                            <x-admin.avatar :initials="$student['initials']" size="sm" tone="soft" :label="$student['name']" />
                                            <span class="admin-cell-user__text">
                                                <span class="admin-table__primary">{{ $student['name'] }}</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td>{{ $student['filiere'] }}</td>
                                    <td>{{ $student['niveau'] }}</td>
                                    <td><x-admin.status :label="$student['decision']" :tone="$student['tone']" /></td>
                                    <td>
                                        <div class="admin-table__actions">
                                            <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.etudiants.show', ['matricule' => 'ENE-2023-1048']) }}">
                                                <x-admin.icon name="eye" /> Voir la fiche
                                            </a>
                                        </div>
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
