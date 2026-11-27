@extends('layouts.admin')

@section('title', 'Référentiels')

@section('content')
    @php
        $years = [
            ['label' => '2026-2027', 'period' => 'Septembre 2026 — Août 2027', 'status' => 'En préparation', 'tone' => 'blue', 'promotions' => 0, 'current' => false],
            ['label' => '2025-2026', 'period' => 'Septembre 2025 — Août 2026', 'status' => 'En cours', 'tone' => 'gold', 'promotions' => 42, 'current' => true],
            ['label' => '2024-2025', 'period' => 'Septembre 2024 — Août 2025', 'status' => 'Clôturée', 'tone' => 'dark', 'promotions' => 39, 'current' => false],
            ['label' => '2023-2024', 'period' => 'Septembre 2023 — Août 2024', 'status' => 'Archivée', 'tone' => 'slate', 'promotions' => 36, 'current' => false],
            ['label' => '2022-2023', 'period' => 'Septembre 2022 — Août 2023', 'status' => 'Archivée', 'tone' => 'slate', 'promotions' => 34, 'current' => false],
        ];

        $filieres = [
            ['code' => 'IG', 'name' => 'Informatique de gestion', 'department' => 'Département Informatique', 'promotions' => 8, 'effectif' => 312, 'status' => 'Active', 'tone' => 'blue'],
            ['code' => 'BF', 'name' => 'Banque et finance', 'department' => 'Département Banque et finance', 'promotions' => 7, 'effectif' => 268, 'status' => 'Active', 'tone' => 'blue'],
            ['code' => 'CCA', 'name' => 'Comptabilité contrôle et audit', 'department' => 'Département Comptabilité', 'promotions' => 6, 'effectif' => 241, 'status' => 'Active', 'tone' => 'blue'],
            ['code' => 'GRH', 'name' => 'Gestion des ressources humaines', 'department' => 'Département Ressources humaines', 'promotions' => 5, 'effectif' => 196, 'status' => 'Active', 'tone' => 'blue'],
            ['code' => 'MK', 'name' => 'Marketing', 'department' => 'Département Marketing', 'promotions' => 5, 'effectif' => 184, 'status' => 'Active', 'tone' => 'blue'],
            ['code' => 'ST', 'name' => 'Statistique', 'department' => 'Département Statistique', 'promotions' => 4, 'effectif' => 152, 'status' => 'Active', 'tone' => 'blue'],
            ['code' => 'PL', 'name' => 'Planification', 'department' => 'Département Planification', 'promotions' => 2, 'effectif' => 96, 'status' => 'En révision', 'tone' => 'gold'],
        ];

        $levels = [
            ['code' => 'L1', 'label' => 'Licence 1', 'cycle' => 'Licence', 'duration' => '1 an', 'promotions' => 8],
            ['code' => 'L2', 'label' => 'Licence 2', 'cycle' => 'Licence', 'duration' => '1 an', 'promotions' => 8],
            ['code' => 'L3', 'label' => 'Licence 3', 'cycle' => 'Licence', 'duration' => '1 an', 'promotions' => 7],
            ['code' => 'M1', 'label' => 'Master 1', 'cycle' => 'Master', 'duration' => '1 an', 'promotions' => 6],
            ['code' => 'M2', 'label' => 'Master 2', 'cycle' => 'Master', 'duration' => '1 an', 'promotions' => 5],
        ];

        $promotions = [
            ['label' => 'IG L1 2025-2026', 'code' => 'IG-L1-2025', 'year' => '2025-2026', 'filiere' => 'Informatique de gestion', 'level' => 'L1', 'effectif' => 48, 'status' => 'Active', 'tone' => 'blue'],
            ['label' => 'BF L2 2025-2026', 'code' => 'BF-L2-2025', 'year' => '2025-2026', 'filiere' => 'Banque et finance', 'level' => 'L2', 'effectif' => 44, 'status' => 'Active', 'tone' => 'blue'],
            ['label' => 'CCA L3 2025-2026', 'code' => 'CCA-L3-2025', 'year' => '2025-2026', 'filiere' => 'Comptabilité contrôle et audit', 'level' => 'L3', 'effectif' => 39, 'status' => 'Active', 'tone' => 'blue'],
            ['label' => 'GRH L1 2025-2026', 'code' => 'GRH-L1-2025', 'year' => '2025-2026', 'filiere' => 'Gestion des ressources humaines', 'level' => 'L1', 'effectif' => 36, 'status' => 'Active', 'tone' => 'blue'],
            ['label' => 'MK L3 2025-2026', 'code' => 'MK-L3-2025', 'year' => '2025-2026', 'filiere' => 'Marketing', 'level' => 'L3', 'effectif' => 35, 'status' => 'Active', 'tone' => 'blue'],
            ['label' => 'ST L2 2025-2026', 'code' => 'ST-L2-2025', 'year' => '2025-2026', 'filiere' => 'Statistique', 'level' => 'L2', 'effectif' => 31, 'status' => 'Active', 'tone' => 'blue'],
            ['label' => 'PL L1 2022-2023', 'code' => 'PL-L1-2022', 'year' => '2022-2023', 'filiere' => 'Planification', 'level' => 'L1', 'effectif' => 0, 'status' => 'Suspendue', 'tone' => 'red'],
        ];
    @endphp

    <x-admin.page-header title="Référentiels" subtitle="Années académiques, filières, niveaux et promotions de l'établissement." :breadcrumb="['Accueil', 'Administration', 'Référentiels']">
        @slot('actions')
            <x-ui.btn variant="secondary" :href="route('admin.referentiels.index')" icon="refresh">Actualiser</x-ui.btn>
        @endslot
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="admin-ref-tabs-title">
        <h2 class="admin-section__title" id="admin-ref-tabs-title" hidden>Référentiels</h2>

        <div class="admin-card admin-card--flush">
            <div class="admin-tabs" role="tablist" aria-label="Référentiels">
                <button class="admin-tabs__button" id="admin-ref-tab-annees" type="button" role="tab" aria-selected="true" aria-controls="admin-ref-panel-annees" data-tab="annees">Années académiques</button>
                <button class="admin-tabs__button" id="admin-ref-tab-filieres" type="button" role="tab" aria-selected="false" aria-controls="admin-ref-panel-filieres" data-tab="filieres" tabindex="-1">Filières</button>
                <button class="admin-tabs__button" id="admin-ref-tab-niveaux" type="button" role="tab" aria-selected="false" aria-controls="admin-ref-panel-niveaux" data-tab="niveaux" tabindex="-1">Niveaux</button>
                <button class="admin-tabs__button" id="admin-ref-tab-promotions" type="button" role="tab" aria-selected="false" aria-controls="admin-ref-panel-promotions" data-tab="promotions" tabindex="-1">Promotions</button>
            </div>

            <div id="admin-ref-panel-annees" role="tabpanel" aria-labelledby="admin-ref-tab-annees" data-tab-panel="annees">
                <div class="admin-card__head">
                    <h3 class="admin-card__title">Années académiques</h3>
                    <x-ui.btn variant="compact" icon="plus" data-drawer-open="#admin-ref-create-drawer">Ajouter</x-ui.btn>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>Années académiques enregistrées</caption>
                        <colgroup>
                            <col class="col-ref">
                            <col width="26%">
                            <col width="14%">
                            <col width="12%">
                            <col class="col-actions">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col" class="col-ref">Année</th>
                                <th scope="col">Période</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Promotions</th>
                                <th scope="col" class="col-actions"><span class="sr-only" hidden>Actions</span><button class="admin-table__filter" type="button" aria-label="Filtrer les années académiques"><x-admin.icon name="filter" /></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($years as $index => $year)
                                <tr>
                                    <td title="{{ $year['label'] }}"><span class="admin-table__primary">{{ $year['label'] }}</span></td>
                                    <td class="admin-muted" title="{{ $year['period'] }}">{{ $year['period'] }}</td>
                                    <td><x-admin.status :label="$year['status']" :tone="$year['tone']" /></td>
                                    <td>{{ $year['promotions'] }}</td>
                                    <td>
                                        <div class="admin-table__actions">
                                            @if (! $year['current'])
                                                {{-- TODO: relier à la route de définition de l'année en cours --}}
                                                <x-ui.btn variant="secondary">Définir comme année en cours</x-ui.btn>
                                            @endif
                                            <div class="admin-dropdown">
                                                <button class="admin-table__filter" type="button" data-dropdown aria-controls="admin-ref-annees-menu-{{ $index }}" aria-expanded="false" aria-label="Actions pour {{ $year['label'] }}"><x-admin.icon name="more" /></button>
                                                <div class="admin-dropdown__menu" id="admin-ref-annees-menu-{{ $index }}" hidden>
                                                    {{-- TODO: relier aux routes de gestion des référentiels --}}
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="pencil" /> Modifier</button>
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="copy" /> Dupliquer</button>
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="archive" /> Archiver</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="admin-ref-panel-filieres" role="tabpanel" aria-labelledby="admin-ref-tab-filieres" data-tab-panel="filieres" hidden>
                <div class="admin-card__head">
                    <h3 class="admin-card__title">Filières</h3>
                    <x-ui.btn variant="compact" icon="plus" data-drawer-open="#admin-ref-create-drawer">Ajouter</x-ui.btn>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>Filières enregistrées</caption>
                        <colgroup>
                            <col class="col-ref">
                            <col width="24%">
                            <col width="24%">
                            <col width="10%">
                            <col width="10%">
                            <col width="12%">
                            <col class="col-actions">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col" class="col-ref">Code</th>
                                <th scope="col">Filière</th>
                                <th scope="col">Département</th>
                                <th scope="col">Promotions</th>
                                <th scope="col">Effectif</th>
                                <th scope="col">Statut</th>
                                <th scope="col" class="col-actions"><span class="sr-only" hidden>Actions</span><button class="admin-table__filter" type="button" aria-label="Filtrer les filières"><x-admin.icon name="filter" /></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filieres as $index => $filere)
                                <tr>
                                    <td class="col-ref"><x-ui.badge :label="$filere['code']" tone="neutral" /></td>
                                    <td title="{{ $filere['name'] }}"><span class="admin-table__primary">{{ $filere['name'] }}</span></td>
                                    <td class="admin-muted" title="{{ $filere['department'] }}">{{ $filere['department'] }}</td>
                                    <td>{{ $filere['promotions'] }}</td>
                                    <td>{{ $filere['effectif'] }}</td>
                                    <td><x-admin.status :label="$filere['status']" :tone="$filere['tone']" /></td>
                                    <td>
                                        <div class="admin-table__actions">
                                            <div class="admin-dropdown">
                                                <button class="admin-table__filter" type="button" data-dropdown aria-controls="admin-ref-filieres-menu-{{ $index }}" aria-expanded="false" aria-label="Actions pour {{ $filere['name'] }}"><x-admin.icon name="more" /></button>
                                                <div class="admin-dropdown__menu" id="admin-ref-filieres-menu-{{ $index }}" hidden>
                                                    {{-- TODO: relier aux routes de gestion des référentiels --}}
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="pencil" /> Modifier</button>
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="copy" /> Dupliquer</button>
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="archive" /> Archiver</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="admin-ref-panel-niveaux" role="tabpanel" aria-labelledby="admin-ref-tab-niveaux" data-tab-panel="niveaux" hidden>
                <div class="admin-card__head">
                    <h3 class="admin-card__title">Niveaux</h3>
                    <x-ui.btn variant="compact" icon="plus" data-drawer-open="#admin-ref-create-drawer">Ajouter</x-ui.btn>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>Niveaux d'étude enregistrés</caption>
                        <colgroup>
                            <col class="col-ref">
                            <col width="26%">
                            <col width="20%">
                            <col width="14%">
                            <col width="14%">
                            <col class="col-actions">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col" class="col-ref">Code</th>
                                <th scope="col">Libellé</th>
                                <th scope="col">Cycle</th>
                                <th scope="col">Durée</th>
                                <th scope="col">Promotions</th>
                                <th scope="col" class="col-actions"><span class="sr-only" hidden>Actions</span><button class="admin-table__filter" type="button" aria-label="Filtrer les niveaux"><x-admin.icon name="filter" /></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($levels as $index => $level)
                                <tr>
                                    <td class="col-ref"><x-ui.badge :label="$level['code']" tone="blue" /></td>
                                    <td title="{{ $level['label'] }}"><span class="admin-table__primary">{{ $level['label'] }}</span></td>
                                    <td class="admin-muted">{{ $level['cycle'] }}</td>
                                    <td>{{ $level['duration'] }}</td>
                                    <td>{{ $level['promotions'] }}</td>
                                    <td>
                                        <div class="admin-table__actions">
                                            <div class="admin-dropdown">
                                                <button class="admin-table__filter" type="button" data-dropdown aria-controls="admin-ref-niveaux-menu-{{ $index }}" aria-expanded="false" aria-label="Actions pour {{ $level['label'] }}"><x-admin.icon name="more" /></button>
                                                <div class="admin-dropdown__menu" id="admin-ref-niveaux-menu-{{ $index }}" hidden>
                                                    {{-- TODO: relier aux routes de gestion des référentiels --}}
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="pencil" /> Modifier</button>
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="copy" /> Dupliquer</button>
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="archive" /> Archiver</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="admin-ref-panel-promotions" role="tabpanel" aria-labelledby="admin-ref-tab-promotions" data-tab-panel="promotions" hidden>
                <div class="admin-card__head">
                    <h3 class="admin-card__title">Promotions</h3>
                    <x-ui.btn variant="compact" icon="plus" data-drawer-open="#admin-ref-create-drawer">Ajouter</x-ui.btn>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>Promotions enregistrées</caption>
                        <colgroup>
                            <col class="col-ref">
                            <col width="16%">
                            <col width="24%">
                            <col width="10%">
                            <col width="10%">
                            <col width="12%">
                            <col class="col-actions">
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col" class="col-ref">Promotion</th>
                                <th scope="col">Année académique</th>
                                <th scope="col">Filière</th>
                                <th scope="col">Niveau</th>
                                <th scope="col">Effectif</th>
                                <th scope="col">Statut</th>
                                <th scope="col" class="col-actions"><span class="sr-only" hidden>Actions</span><button class="admin-table__filter" type="button" aria-label="Filtrer les promotions"><x-admin.icon name="filter" /></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promotions as $index => $promotion)
                                <tr>
                                    <td class="col-ref" title="{{ $promotion['label'] }} — {{ $promotion['code'] }}">
                                        <span class="admin-table__primary">{{ $promotion['label'] }}</span>
                                        <span class="admin-table__subtext">{{ $promotion['code'] }}</span>
                                    </td>
                                    <td class="admin-muted">{{ $promotion['year'] }}</td>
                                    <td title="{{ $promotion['filiere'] }}">{{ $promotion['filiere'] }}</td>
                                    <td>{{ $promotion['level'] }}</td>
                                    <td>{{ $promotion['effectif'] }}</td>
                                    <td><x-admin.status :label="$promotion['status']" :tone="$promotion['tone']" /></td>
                                    <td>
                                        <div class="admin-table__actions">
                                            <div class="admin-dropdown">
                                                <button class="admin-table__filter" type="button" data-dropdown aria-controls="admin-ref-promotions-menu-{{ $index }}" aria-expanded="false" aria-label="Actions pour {{ $promotion['label'] }}"><x-admin.icon name="more" /></button>
                                                <div class="admin-dropdown__menu" id="admin-ref-promotions-menu-{{ $index }}" hidden>
                                                    {{-- TODO: relier aux routes de gestion des référentiels --}}
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="pencil" /> Modifier</button>
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="copy" /> Dupliquer</button>
                                                    <button class="admin-dropdown__item" type="button"><x-admin.icon name="archive" /> Archiver</button>
                                                </div>
                                            </div>
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

    <x-ui.drawer id="admin-ref-create-drawer" title="Ajouter un référentiel" subtitle="Enregistrer une nouvelle valeur dans l'un des référentiels.">
            {{-- TODO: relier à la route de création des référentiels --}}
            <form id="admin-ref-create-form" action="#" method="POST">
                @csrf

                <div class="admin-form-grid">
                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('libelle') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-ref-libelle">Libellé</label>
                        <input class="admin-input" id="admin-ref-libelle" name="libelle" type="text" value="{{ old('libelle') }}" placeholder="Ex. 2026-2027" @error('libelle') aria-invalid="true" aria-describedby="admin-ref-libelle-error" @else aria-invalid="false" @enderror>
                        @error('libelle')
                            <p class="admin-field__error" id="admin-ref-libelle-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('type') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-ref-type">Type de référentiel</label>
                        <select class="admin-select" id="admin-ref-type" name="type" @error('type') aria-invalid="true" aria-describedby="admin-ref-type-error" @else aria-invalid="false" @enderror>
                            <option value="">Sélectionner un type</option>
                            <option value="annee"{{ old('type') === 'annee' ? ' selected' : '' }}>Année académique</option>
                            <option value="filiere"{{ old('type') === 'filiere' ? ' selected' : '' }}>Filière</option>
                            <option value="niveau"{{ old('type') === 'niveau' ? ' selected' : '' }}>Niveau</option>
                            <option value="promotion"{{ old('type') === 'promotion' ? ' selected' : '' }}>Promotion</option>
                        </select>
                        @error('type')
                            <p class="admin-field__error" id="admin-ref-type-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('annee') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-ref-annee">Année académique de rattachement</label>
                        <select class="admin-select" id="admin-ref-annee" name="annee" @error('annee') aria-invalid="true" aria-describedby="admin-ref-annee-error" @else aria-invalid="false" @enderror>
                            <option value="">Aucune</option>
                            <option value="2025-2026"{{ old('annee') === '2025-2026' ? ' selected' : '' }}>2025-2026</option>
                            <option value="2024-2025"{{ old('annee') === '2024-2025' ? ' selected' : '' }}>2024-2025</option>
                            <option value="2023-2024"{{ old('annee') === '2023-2024' ? ' selected' : '' }}>2023-2024</option>
                        </select>
                        @error('annee')
                            <p class="admin-field__error" id="admin-ref-annee-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('statut') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-ref-statut">Statut</label>
                        <select class="admin-select" id="admin-ref-statut" name="statut" @error('statut') aria-invalid="true" aria-describedby="admin-ref-statut-error" @else aria-invalid="false" @enderror>
                            <option value="actif"{{ old('statut') === 'actif' ? ' selected' : '' }}>Actif</option>
                            <option value="preparation"{{ old('statut') === 'preparation' ? ' selected' : '' }}>En préparation</option>
                            <option value="archive"{{ old('statut') === 'archive' ? ' selected' : '' }}>Archivé</option>
                        </select>
                        @error('statut')
                            <p class="admin-field__error" id="admin-ref-statut-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-notice admin-form-grid__full">
                        <x-admin.icon name="info" />
                        <span>Une année académique ne peut être active que si aucune autre année n'est déjà en cours.</span>
                    </div>
                </div>
            </form>

            <x-slot:footer>
                <div class="admin-form-actions">
                    <x-ui.btn variant="secondary">Annuler</x-ui.btn>
                    <x-ui.btn variant="primary" type="submit" form="admin-ref-create-form" icon="check">Ajouter</x-ui.btn>
                </div>
            </x-slot:footer>
    </x-ui.drawer>
@endsection
