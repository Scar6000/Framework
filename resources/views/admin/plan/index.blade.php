@extends('layouts.admin')

@section('title', 'Plan de numérisation')

@section('content')
    @php
        $filieres = [
            ['key' => 'ig', 'name' => 'Informatique de gestion', 'level' => 'L1'],
            ['key' => 'bf', 'name' => 'Banque et finance', 'level' => 'L2'],
            ['key' => 'cca', 'name' => 'Comptabilité contrôle et audit', 'level' => 'L3'],
            ['key' => 'grh', 'name' => 'Gestion des ressources humaines', 'level' => 'L1'],
            ['key' => 'mk', 'name' => 'Marketing', 'level' => 'L2'],
            ['key' => 'st', 'name' => 'Statistique', 'level' => 'L3'],
        ];

        $sessions = [
            ['key' => 's1-normale', 'label' => 'S1 Normale'],
            ['key' => 's1-rattrapage', 'label' => 'S1 Rattrapage'],
            ['key' => 's2-normale', 'label' => 'S2 Normale'],
            ['key' => 's2-rattrapage', 'label' => 'S2 Rattrapage'],
            ['key' => 's3-normale', 'label' => 'S3 Normale'],
            ['key' => 's3-rattrapage', 'label' => 'S3 Rattrapage'],
        ];

        $tracking = [
            ['done', 'done', 'draft', 'missing', 'none', 'done'],
            ['done', 'missing', 'done', 'draft', 'none', 'done'],
            ['draft', 'done', 'missing', 'done', 'none', 'draft'],
            ['done', 'done', 'done', 'missing', 'none', 'done'],
            ['missing', 'done', 'draft', 'done', 'none', 'missing'],
            ['done', 'draft', 'done', 'missing', 'none', 'done'],
        ];

        $summaryCards = [
            ['label' => 'PV attendus', 'value' => '240', 'icon' => 'pv'],
            ['label' => 'PV numérisés', 'value' => '212', 'icon' => 'check'],
            ['label' => 'PV manquants', 'value' => '14', 'icon' => 'alert'],
            ['label' => 'PV en brouillon', 'value' => '25', 'icon' => 'clock'],
        ];
    @endphp

    <x-admin.page-header
        title="Plan de numérisation des PV"
        subtitle="Suivi des procès-verbaux attendus par promotion, semestre et session pour l'année 2025-2026."
        :breadcrumb="['Accueil', 'Plan de numérisation']"
    >
        <x-slot:actions>
            <x-admin.status label="14 manquants" tone="red" />
            <x-admin.status label="25 brouillons" tone="gold" />
            <button class="admin-button admin-button--secondary admin-button--small" type="button" data-drawer-open data-target="plan-config-drawer">
                <x-admin.icon name="sliders" />
                Configurer les PV attendus
            </button>
            <a class="admin-button admin-button--small" href="{{ route('admin.exports.index') }}">
                <x-admin.icon name="download" />
                Exporter
            </a>
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
                    @foreach ($filieres as $filiere)
                        <option value="{{ $filiere['key'] }}" @selected(old('filiere', '') === $filiere['key'])>
                            {{ $filiere['name'] }} — {{ $filiere['level'] }}
                        </option>
                    @endforeach
                </select>
            </label>

            <button class="admin-button admin-button--small" type="submit">
                <x-admin.icon name="filter" />
                Appliquer
            </button>
            <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.plan.index') }}">Réinitialiser</a>
        </form>
    </section>

    <section class="admin-section" aria-labelledby="plan-summary-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="plan-summary-title">Indicateurs de la campagne</h2>
                <p class="admin-section__hint">Situation arrêtée au 24 novembre 2026, toutes filières confondues.</p>
            </div>
            <div class="admin-section__actions">
                <x-admin.status label="Campagne en cours" tone="blue" />
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
                    <x-admin.status label="88 %" tone="blue" />
                </div>
                <div class="admin-card__body">
                    <div class="admin-progress-item__head">
                        <span class="admin-progress-item__text">
                            <span class="admin-progress-item__name">212 PV numérisés sur 240 attendus</span>
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
                <p class="admin-section__hint">Une case par semestre et par session. Passez le curseur sur une case manquante pour voir le détail.</p>
            </div>
            <div class="admin-section__actions">
                <button class="admin-button admin-button--secondary admin-button--small" type="button" data-drawer-open data-target="plan-config-drawer">
                    <x-admin.icon name="sliders" />
                    Configurer les PV attendus
                </button>
                <a class="admin-button admin-button--small" href="{{ route('admin.exports.index') }}">
                    <x-admin.icon name="download" />
                    Exporter
                </a>
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
                    <thead>
                        <tr>
                            <th class="admin-plan-table__promotion" rowspan="2" scope="col">Promotion</th>
                            <th colspan="2" scope="colgroup">Semestre 1</th>
                            <th colspan="2" scope="colgroup">Semestre 2</th>
                            <th colspan="2" scope="colgroup">Semestre 3</th>
                        </tr>
                        <tr>
                            @foreach ($sessions as $session)
                                <th scope="col">{{ $session['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filieres as $rowIndex => $filiere)
                            <tr>
                                <th class="admin-plan-table__promotion" scope="row">
                                    {{ $filiere['name'] }}
                                    <span class="admin-tiny admin-muted">{{ $filiere['level'] }}</span>
                                </th>
                                @foreach ($sessions as $colIndex => $session)
                                    @php
                                        $state = $tracking[$rowIndex][$colIndex];
                                    @endphp
                                    <td>
                                        @if ($state === 'done')
                                            <span class="admin-plan-cell" role="img" aria-label="Numérisé : {{ $filiere['name'] }} {{ $filiere['level'] }}, {{ $session['label'] }}">
                                                <x-admin.icon name="check" />
                                            </span>
                                        @elseif ($state === 'draft')
                                            <span class="admin-plan-cell admin-plan-cell--draft" role="img" aria-label="Brouillon : {{ $filiere['name'] }} {{ $filiere['level'] }}, {{ $session['label'] }}">
                                                <x-admin.icon name="clock" />
                                            </span>
                                        @elseif ($state === 'missing')
                                            <span
                                                class="admin-plan-cell admin-plan-cell--missing"
                                                role="img"
                                                tabindex="0"
                                                aria-label="Manquant : {{ $filiere['name'] }} {{ $filiere['level'] }}, {{ $session['label'] }}"
                                                data-tooltip="PV manquant : {{ $filiere['name'] }} {{ $filiere['level'] }} — {{ $session['label'] }}"
                                            >
                                                <x-admin.icon name="circle" />
                                            </span>
                                        @else
                                            <span class="admin-plan-cell admin-plan-cell--none" role="img" aria-label="Non attendu : {{ $filiere['name'] }} {{ $filiere['level'] }}, {{ $session['label'] }}">
                                                <x-admin.icon name="minus" />
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-card__body">
                <div class="admin-legend" aria-label="Légende de la grille de suivi">
                    <span class="admin-legend__item"><span class="admin-plan-cell"><x-admin.icon name="check" /></span> Numérisé</span>
                    <span class="admin-legend__item"><span class="admin-plan-cell admin-plan-cell--draft"><x-admin.icon name="clock" /></span> Brouillon</span>
                    <span class="admin-legend__item"><span class="admin-plan-cell admin-plan-cell--missing"><x-admin.icon name="circle" /></span> Manquant</span>
                    <span class="admin-legend__item"><span class="admin-plan-cell admin-plan-cell--none"><x-admin.icon name="minus" /></span> Non attendu</span>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-drawer" id="plan-config-drawer" hidden role="dialog" aria-modal="true" aria-labelledby="plan-config-title">
        <div class="admin-drawer__head">
            <div>
                <h2 class="admin-drawer__title" id="plan-config-title">Configurer les PV attendus</h2>
                <p class="admin-drawer__subtitle">Année 2025-2026 — sélectionnez les sessions à suivre pour chaque promotion.</p>
            </div>
            <button class="admin-icon-button" type="button" data-overlay-close aria-label="Fermer le tiroir de configuration">
                <x-admin.icon name="close" />
            </button>
        </div>

        <div class="admin-drawer__body">
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
                        @foreach ($filieres as $filiere)
                            <div class="admin-field admin-form-grid__full">
                                <span class="admin-field__label">{{ $filiere['name'] }} — {{ $filiere['level'] }}</span>
                                <div class="admin-form-grid">
                                    @foreach ($sessions as $session)
                                        <label class="admin-checkbox">
                                            <input
                                                type="checkbox"
                                                name="attendus[{{ $filiere['key'] }}][{{ $session['key'] }}]"
                                                value="1"
                                                @checked(old('attendus.'.$filiere['key'].'.'.$session['key'], true))
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
                    <button class="admin-button admin-button--small" type="submit">Enregistrer la configuration</button>
                </div>
            </form>
        </div>

        <div class="admin-drawer__footer">
            <button class="admin-button admin-button--secondary admin-button--small" type="button" data-overlay-close>Annuler</button>
        </div>
    </div>
@endsection
