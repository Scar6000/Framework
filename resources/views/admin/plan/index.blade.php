@extends('layouts.admin')

@section('title', 'Plan de numérisation')

@section('content')
    @php
        $promotions = [
            ['key' => 'ig-l1', 'name' => 'Informatique de gestion', 'level' => 'L1'],
            ['key' => 'bf-l2', 'name' => 'Banque et finance', 'level' => 'L2'],
            ['key' => 'cca-l3', 'name' => 'Comptabilité contrôle et audit', 'level' => 'L3'],
            ['key' => 'grh-l1', 'name' => 'Gestion des ressources humaines', 'level' => 'L1'],
            ['key' => 'mk-l2', 'name' => 'Marketing', 'level' => 'L2'],
            ['key' => 'st-l3', 'name' => 'Statistique', 'level' => 'L3'],
        ];

        $sessions = [
            ['key' => 'normale', 'label' => 'Session normale'],
            ['key' => 'rattrapage', 'label' => 'Session de rattrapage'],
        ];

        $tracking = [
            ['archived', 'draft'],
            ['archived', 'missing'],
            ['draft', 'none'],
            ['archived', 'archived'],
            ['missing', 'draft'],
            ['archived', 'missing'],
        ];

        $summaryCards = [
            ['label' => 'PV attendus', 'value' => '240', 'icon' => 'pv'],
            ['label' => 'PV archivés', 'value' => '212', 'icon' => 'check'],
            ['label' => 'PV manquants', 'value' => '14', 'icon' => 'alert'],
            ['label' => 'PV en brouillon', 'value' => '25', 'icon' => 'clock'],
        ];
    @endphp

    <x-admin.page-header
        title="Plan de numérisation des PV"
        subtitle="Suivi des procès-verbaux attendus par promotion et par session pour l'année 2025-2026."
        :breadcrumb="['Accueil', 'Plan de numérisation']"
    >
        <x-slot:actions>
            <x-ui.badge label="14 manquants" tone="red" />
            <x-ui.badge label="25 brouillons" tone="gold" />
            <x-ui.btn variant="secondary" icon="sliders" data-drawer-open="#plan-config-drawer">Configurer les PV attendus</x-ui.btn>
            <x-ui.btn variant="primary" :href="route('admin.exports.index')" icon="download">Exporter</x-ui.btn>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="plan-filters-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="plan-filters-title">Période et filière</h2>
                <p class="admin-section__hint">Ajustez le périmètre du suivi de la numérisation.</p>
            </div>
        </div>

        <form class="admin-filters" method="GET" action="{{ route('admin.plan.index') }}">
            <label class="admin-field" for="plan-annee">
                <span class="admin-field__label">Année académique</span>
                <select class="admin-select" id="plan-annee" name="annee">
                    <option value="2024-2025" @selected(old('annee', '2025-2026') === '2024-2025')>2024-2025</option>
                    <option value="2025-2026" @selected(old('annee', '2025-2026') === '2025-2026')>2025-2026</option>
                    <option value="2026-2027" @selected(old('annee', '2025-2026') === '2026-2027')>2026-2027</option>
                </select>
            </label>

            <label class="admin-field admin-field--grow" for="plan-filiere">
                <span class="admin-field__label">Filière</span>
                <select class="admin-select" id="plan-filiere" name="filiere">
                    <option value="" @selected(old('filiere', '') === '')>Toutes les filières</option>
                    @foreach ($promotions as $promotion)
                        <option value="{{ $promotion['key'] }}" @selected(old('filiere', '') === $promotion['key'])>
                            {{ $promotion['name'] }} — {{ $promotion['level'] }}
                        </option>
                    @endforeach
                </select>
            </label>

            <x-ui.btn variant="primary" type="submit" icon="filter">Appliquer</x-ui.btn>
            <x-ui.btn variant="secondary" :href="route('admin.plan.index')">Réinitialiser</x-ui.btn>
        </form>
    </section>

    <section class="admin-section" aria-labelledby="plan-summary-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="plan-summary-title">Indicateurs de la campagne</h2>
                <p class="admin-section__hint">Situation arrêtée au 24 novembre 2026, toutes filières confondues.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.badge label="Campagne en cours" tone="blue" />
            </div>
        </div>

        <div class="admin-summary-grid">
            @foreach ($summaryCards as $card)
                <article class="admin-summary-card">
                    <span class="admin-summary-card__icon">
                        <x-admin.icon name="{{ $card['icon'] }}" />
                    </span>
                    <div>
                        <span class="admin-summary-card__value">{{ $card['value'] }}</span>
                        <span class="admin-summary-card__label">{{ $card['label'] }}</span>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="admin-section">
            <div class="admin-card">
                <div class="admin-card__head">
                    <h3 class="admin-card__title">Couverture globale de la numérisation</h3>
                    <x-ui.badge label="88 %" tone="blue" />
                </div>
                <div class="admin-card__body">
                    <div class="admin-progress-item__head">
                        <span class="admin-progress-item__text">
                            <span class="admin-progress-item__name">212 PV archivés sur 240 attendus</span>
                            <span class="admin-progress-item__meta">Reste 14 PV manquants et 25 PV en brouillon à traiter.</span>
                        </span>
                        <span class="admin-progress-item__value">88 %</span>
                    </div>
                    <x-admin.progress :value="88" label="Couverture de la numérisation des PV pour 2025-2026" large />
                </div>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="plan-grid-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="plan-grid-title">Grille de suivi par promotion</h2>
                <p class="admin-section__hint">Une case par promotion et par session pour l'année 2025-2026. Passez le curseur sur une case manquante pour voir le détail.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.btn variant="secondary" icon="sliders" data-drawer-open="#plan-config-drawer">Configurer les PV attendus</x-ui.btn>
                <x-ui.btn variant="primary" :href="route('admin.exports.index')" icon="download">Exporter</x-ui.btn>
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h3 class="admin-card__title">PV attendus et numérisés</h3>
                <span class="admin-tiny admin-muted">Année 2025-2026 — 6 promotions affichées</span>
            </div>

            <div class="admin-plan-scroll">
                <table class="admin-plan-table">
                    <caption class="sr-only" hidden>Grille de suivi de la numérisation des PV par promotion et par session</caption>
                    <colgroup>
                        <col class="admin-plan-table__promotion" width="30%">
                        <col width="35%">
                        <col width="35%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="admin-plan-table__promotion" scope="col">Promotion</th>
                            @foreach ($sessions as $session)
                                <th scope="col">{{ $session['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promotions as $rowIndex => $promotion)
                            <tr>
                                <th class="admin-plan-table__promotion" scope="row" title="{{ $promotion['name'] }} — {{ $promotion['level'] }}">
                                    {{ $promotion['name'] }}
                                    <span class="admin-tiny admin-muted">{{ $promotion['level'] }}</span>
                                </th>
                                @foreach ($sessions as $colIndex => $session)
                                    @php
                                        $state = $tracking[$rowIndex][$colIndex];
                                        $stateLabel = $state === 'archived' ? 'Archivé' : ($state === 'draft' ? 'Brouillon' : ($state === 'missing' ? 'Manquant' : 'Non attendu'));
                                        $stateDescription = $stateLabel.' : '.$promotion['name'].' '.$promotion['level'].', '.$session['label'];
                                    @endphp
                                    <td>
                                        @if ($state === 'archived')
                                            <span class="admin-plan-cell" role="img" aria-label="{{ $stateDescription }}" title="{{ $stateDescription }}">
                                                <x-admin.icon name="check" />
                                            </span>
                                        @elseif ($state === 'draft')
                                            <span class="admin-plan-cell admin-plan-cell--draft" role="img" aria-label="{{ $stateDescription }}" title="{{ $stateDescription }}">
                                                <x-admin.icon name="clock" />
                                            </span>
                                        @elseif ($state === 'missing')
                                            <span
                                                class="admin-plan-cell admin-plan-cell--missing"
                                                role="img"
                                                tabindex="0"
                                                aria-label="{{ $stateDescription }}"
                                                title="{{ $stateDescription }}"
                                                data-tooltip="PV manquant : {{ $promotion['name'] }} {{ $promotion['level'] }} — {{ $session['label'] }}"
                                            >
                                                <x-admin.icon name="circle" />
                                            </span>
                                        @else
                                            <span class="admin-plan-cell admin-plan-cell--none" role="img" aria-label="{{ $stateDescription }}" title="{{ $stateDescription }}">
                                                <x-admin.icon name="minus" />
                                            </span>
                                        @endif
                                        <span class="admin-tiny admin-muted">{{ $stateLabel }}</span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-card__body">
                <div class="admin-legend" aria-label="Légende de la grille de suivi">
                    <span class="admin-legend__item"><span class="admin-plan-cell"><x-admin.icon name="check" /></span> Archivé</span>
                    <span class="admin-legend__item"><span class="admin-plan-cell admin-plan-cell--draft"><x-admin.icon name="clock" /></span> Brouillon</span>
                    <span class="admin-legend__item"><span class="admin-plan-cell admin-plan-cell--missing"><x-admin.icon name="circle" /></span> Manquant</span>
                    <span class="admin-legend__item"><span class="admin-plan-cell admin-plan-cell--none"><x-admin.icon name="minus" /></span> Non attendu</span>
                </div>
            </div>
        </div>
    </section>

    <x-ui.drawer id="plan-config-drawer" title="Configurer les PV attendus" subtitle="Année 2025-2026 — sélectionnez les sessions à suivre pour chaque promotion.">
            <form id="plan-config-form" action="{{ route('admin.plan.index') }}" method="POST">
                @csrf
                @method('PUT')

                <p class="admin-tiny admin-muted" id="plan-config-help">Les sessions non cochées apparaîtront comme « Non attendu » dans la grille de suivi.</p>

                <div class="admin-section">
                    <label class="admin-checkbox">
                        <input type="checkbox" data-select-all="plan-config-options" />
                        Tout cocher pour l'année 2025-2026
                    </label>
                </div>

                @error('attendus')
                    <p class="admin-field__error" id="plan-config-error">
                        <x-admin.icon name="alert" />
                        {{ $message }}
                    </p>
                @enderror

                <div class="admin-section" id="plan-config-options">
                    <div class="admin-form-grid">
                        @foreach ($promotions as $promotion)
                            <div class="admin-field admin-form-grid__full">
                                <span class="admin-field__label">{{ $promotion['name'] }} — {{ $promotion['level'] }}</span>
                                <div class="admin-form-grid">
                                    @foreach ($sessions as $session)
                                        <label class="admin-checkbox">
                                            <input
                                                type="checkbox"
                                                name="attendus[{{ $promotion['key'] }}][{{ $session['key'] }}]"
                                                value="1"
                                                @checked(old('attendus.'.$promotion['key'].'.'.$session['key'], true))
                                                aria-invalid="@error('attendus') true @else false @enderror"
                                                aria-describedby="plan-config-help @error('attendus') plan-config-error @enderror"
                                            />
                                            {{ $session['label'] }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="admin-form-actions">
                    <x-ui.btn variant="primary" type="submit">Enregistrer la configuration</x-ui.btn>
                </div>
            </form>

            <x-slot:footer>
                <x-ui.btn variant="secondary">Annuler</x-ui.btn>
            </x-slot:footer>
    </x-ui.drawer>
@endsection
