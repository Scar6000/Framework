@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    @php
        $today = \Illuminate\Support\Carbon::now()->locale('fr')->translatedFormat('l d F Y');

        $statCards = [
            ['label' => 'PV archivés', 'value' => '96', 'meta' => 'Année 2025-2026', 'icon' => 'archive', 'tone' => 'blue'],
            ['label' => 'PV en brouillon', 'value' => '25', 'meta' => 'En attente de classement', 'icon' => 'pv', 'tone' => 'blue'],
            ['label' => 'PV attendus', 'value' => '14', 'meta' => 'Sur 240 prévus', 'icon' => 'clock', 'tone' => 'gold'],
            ['label' => 'Étudiants référencés', 'value' => '6 428', 'meta' => '+ 312 cette année', 'icon' => 'users', 'tone' => 'blue'],
            ['label' => 'Taux de réussite global', 'value' => '74,6 %', 'meta' => 'Toutes sessions', 'icon' => 'chart', 'tone' => 'gold'],
        ];

        $filieres = [
            ['name' => 'Informatique de gestion', 'short' => 'IG', 'done' => 34, 'expected' => 36],
            ['name' => 'Banque et finance', 'short' => 'BF', 'done' => 41, 'expected' => 44],
            ['name' => 'Comptabilité contrôle et audit', 'short' => 'CCA', 'done' => 38, 'expected' => 42],
            ['name' => 'Gestion des ressources humaines', 'short' => 'GRH', 'done' => 29, 'expected' => 34],
            ['name' => 'Marketing', 'short' => 'MK', 'done' => 36, 'expected' => 40],
            ['name' => 'Statistique', 'short' => 'ST', 'done' => 34, 'expected' => 44],
        ];

        $journalRows = [
            ['initials' => 'JA', 'name' => 'Dr Jean Théophile Agadamè', 'role' => 'Administrateur', 'action' => 'Modification', 'object' => 'PV', 'reference' => 'PV-2026-IG-L3-014', 'date' => '24/11/2026 — 09 h 42', 'type' => 'Modification', 'tone' => 'gold', 'highlight' => true],
            ['initials' => 'AK', 'name' => 'Aminata Koné', 'role' => 'Agent de scolarité', 'action' => 'Enregistrement', 'object' => 'PV', 'reference' => 'PV-2026-BF-L2-008', 'date' => '24/11/2026 — 08 h 57', 'type' => 'Modification', 'tone' => 'gold', 'highlight' => false],
            ['initials' => 'MD', 'name' => 'Mamadou Diop', 'role' => 'Chef de département', 'action' => 'Archivage', 'object' => 'PV', 'reference' => 'PV-2026-CCA-L3-021', 'date' => '23/11/2026 — 16 h 14', 'type' => 'Archivage', 'tone' => 'dark', 'highlight' => false],
            ['initials' => 'SN', 'name' => 'Sophie N' . "\u{00E9}" . 'nesson', 'role' => 'Direction', 'action' => 'Consultation', 'object' => 'Fiche étudiant / ENE-2023-1048', 'date' => '23/11/2026 — 14 h 03', 'type' => 'Consultation', 'tone' => 'slate', 'highlight' => false],
            ['initials' => 'YB', 'name' => 'Yaya Bathily', 'role' => 'Agent de scolarité', 'action' => 'Import de résultats', 'object' => 'Promotion / Marketing L3', 'date' => '23/11/2026 — 11 h 26', 'type' => 'Modification', 'tone' => 'gold', 'highlight' => false],
            ['initials' => 'FL', 'name' => 'Fatou Ly', 'role' => 'Administrateur', 'action' => 'Export', 'object' => 'Statistiques comparatives 2025-2026', 'date' => '22/11/2026 — 17 h 38', 'type' => 'Export', 'tone' => 'dark', 'highlight' => false],
        ];

        $latestPv = [
            ['reference' => 'PV-2026-IG-L3-014', 'promotion' => 'Informatique de gestion, L3', 'annee' => '2025-2026', 'session' => 'Normale', 'status' => 'Brouillon', 'tone' => 'slate'],
            ['reference' => 'PV-2026-BF-L2-008', 'promotion' => 'Banque et finance, L2', 'annee' => '2025-2026', 'session' => 'Normale', 'status' => 'Archivé', 'tone' => 'dark'],
            ['reference' => 'PV-2026-CCA-L3-021', 'promotion' => 'Comptabilité, L3', 'annee' => '2025-2026', 'session' => 'Rattrapage', 'status' => 'Brouillon', 'tone' => 'slate'],
            ['reference' => 'PV-2026-GRH-L1-006', 'promotion' => 'Ressources humaines, L1', 'annee' => '2025-2026', 'session' => 'Normale', 'status' => 'Archivé', 'tone' => 'dark'],
            ['reference' => 'PV-2026-MK-L3-012', 'promotion' => 'Marketing, L3', 'annee' => '2025-2026', 'session' => 'Normale', 'status' => 'Brouillon', 'tone' => 'slate'],
        ];

        $missingPv = [
            ['promotion' => 'Comptabilité, L3', 'session' => 'Normale', 'annee' => '2025-2026'],
            ['promotion' => 'Statistique, L3', 'session' => 'Rattrapage', 'annee' => '2025-2026'],
            ['promotion' => 'Marketing, L2', 'session' => 'Normale', 'annee' => '2025-2026'],
        ];
    @endphp

    <nav class="admin-breadcrumb" aria-label="Fil d'Ariane">
        <span>Administration</span>
        <x-admin.icon name="chevron-right" />
        <span>Tableau de bord</span>
    </nav>

    {{-- Bannière de bienvenue --}}
    <section class="admin-banner" aria-labelledby="admin-banner-title">
        <span class="admin-banner__surface" aria-hidden="true"></span>

        <div class="admin-banner__content">
            <span class="admin-banner__eyebrow"><x-admin.icon name="spark" /> Espace administrateur</span>
            <h1 class="admin-banner__title" id="admin-banner-title">Bonjour, Dr Agadamè</h1>
            <p class="admin-banner__text">Nous sommes le {{ $today }}. Il reste 14 PV à numériser pour l'année 2025-2026 et 3 alertes à examiner.</p>
            <x-ui.btn
                :href="route('admin.plan.index')"
                variant="compact"
                class="admin-button--gold"
                icon="arrow-right"
                label="Voir les alertes"
            />
        </div>

        <div class="admin-banner__brand">
            <x-ui.brand />
        </div>
    </section>

    {{-- Indicateurs clés --}}
    <section class="admin-section" aria-labelledby="admin-indicators-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-indicators-title">Indicateurs clés</h2>
                <p class="admin-section__hint">Situation de la numérisation pour l'année académique 2025-2026.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.btn variant="secondary" href="{{ route('admin.pv.index') }}">Tout voir</x-ui.btn>
            </div>
        </div>

        <div class="admin-stat-grid">
            @foreach ($statCards as $card)
                <article class="admin-stat-card">
                    <span class="admin-stat-card__tile{{ $card['tone'] === 'gold' ? ' admin-stat-card__tile--gold' : '' }}">
                        <x-admin.icon name="{{ $card['icon'] }}" />
                    </span>
                    <div>
                        <span class="admin-stat-card__value">{{ $card['value'] }}</span>
                        <span class="admin-stat-card__label">{{ $card['label'] }}</span>
                    </div>
                    <span class="admin-stat-card__meta">{{ $card['meta'] }}</span>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Taux de réussite --}}
    <section class="admin-section" aria-labelledby="admin-success-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-success-title">Taux de réussite par filière et par année</h2>
                <p class="admin-section__hint">Comparaison des sessions normales sur trois années académiques.</p>
            </div>
            <div class="admin-section__actions">
                <label class="admin-field">
                    <span class="admin-field__label" hidden>Session</span>
                    <select class="admin-select" aria-label="Filtrer par session">
                        <option>Toutes</option>
                        <option>Normale</option>
                        <option>Rattrapage</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="admin-card admin-chart">
            <div class="admin-chart__legend" aria-hidden="true">
                <span class="admin-chart__legend-item"><span class="admin-chart__legend-dot admin-chart__legend-dot--night"></span> 2023-2024</span>
                <span class="admin-chart__legend-item"><span class="admin-chart__legend-dot admin-chart__legend-dot--blue"></span> 2024-2025</span>
                <span class="admin-chart__legend-item"><span class="admin-chart__legend-dot admin-chart__legend-dot--gold"></span> 2025-2026</span>
            </div>

            <svg viewBox="0 0 760 260" width="760" height="260" role="img" aria-label="Graphique des taux de réussite par filière et par année">
                @foreach ([0, 25, 50, 75, 100] as $tick)
                    @php
                        $y = 210 - $tick * 1.8;
                    @endphp
                    <path d="M50 {{ $y }}H740" stroke="#DDE5FA" stroke-width="1"></path>
                    <text x="42" y="{{ $y + 4 }}" text-anchor="end" font-size="10" fill="#000000">{{ $tick }}</text>
                @endforeach

                @php
                    $series = [
                        ['name' => 'Informatique de gestion', 'values' => [68, 72, 78]],
                        ['name' => 'Banque et finance', 'values' => [61, 66, 71]],
                        ['name' => 'Comptabilité', 'values' => [70, 73, 76]],
                        ['name' => 'Ressources humaines', 'values' => [55, 62, 67]],
                        ['name' => 'Marketing', 'values' => [64, 69, 73]],
                        ['name' => 'Statistique', 'values' => [58, 65, 70]],
                    ];
                    $colors = ['#08236B', '#2F6BFF', '#F5B91E'];
                    $years = ['2023-2024', '2024-2025', '2025-2026'];
                    $centers = [106, 216, 326, 436, 546, 656];
                @endphp

                @foreach ($series as $index => $serie)
                    @foreach ($serie['values'] as $barIndex => $value)
                        @php
                            $x = $centers[$index] - 36 + $barIndex * 27;
                            $height = $value * 1.8;
                            $y = 210 - $height;
                            $tooltip = $serie['name'].' — '.$years[$barIndex].' : '.$value.' %';
                        @endphp
                        <rect class="admin-chart__bar" x="{{ $x }}" y="{{ $y }}" width="22" height="{{ $height }}" rx="4" fill="{{ $colors[$barIndex] }}" data-tooltip="{{ $tooltip }}">
                            <title>{{ $tooltip }}</title>
                        </rect>
                    @endforeach
                    <text x="{{ $centers[$index] }}" y="234" text-anchor="middle" font-size="10" fill="#09090b">{{ $serie['name'] }}</text>
                @endforeach
            </svg>
        </div>
    </section>

    {{-- Journal et derniers PV --}}
    <section class="admin-section" aria-labelledby="admin-activity-title">
        <div class="admin-section__head">
            <h2 class="admin-section__title" id="admin-activity-title">Activité récente</h2>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-tabs" role="tablist" aria-label="Activité récente">
                <button class="admin-tabs__button" id="admin-tab-journal" type="button" role="tab" aria-selected="true" aria-controls="admin-panel-journal" data-tab="journal">Journal récent</button>
                <button class="admin-tabs__button" id="admin-tab-pv" type="button" role="tab" aria-selected="false" aria-controls="admin-panel-pv" data-tab="pv" tabindex="-1">Derniers PV</button>
            </div>

            <div id="admin-panel-journal" role="tabpanel" aria-labelledby="admin-tab-journal" data-tab-panel="journal">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>6 dernières entrées du journal d'audit</caption>
                        <colgroup>
                            <col style="width: 230px;" />
                            <col style="width: 120px;" />
                            <col style="width: 250px;" />
                            <col style="width: 150px;" />
                            <col style="width: 100px;" />
                            <col class="col-actions" style="width: 120px;" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col">Utilisateur</th>
                                <th scope="col">Action</th>
                                <th scope="col">Objet</th>
                                <th scope="col">Date et heure</th>
                                <th scope="col">Type</th>
                                <th class="col-actions" scope="col"><span class="sr-only" hidden>Actions</span><button class="admin-table__filter" type="button" aria-label="Filtrer le journal"><x-admin.icon name="filter" /></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($journalRows as $index => $row)
                                <tr class="{{ $row['highlight'] ? 'is-highlighted' : '' }}">
                                    <td title="{{ $row['name'] }} — {{ $row['role'] }}">
                                        <span class="admin-cell-user">
                                            <x-admin.avatar :initials="$row['initials']" size="sm" />
                                            <span class="admin-cell-user__text">
                                                <span class="admin-table__primary">{{ $row['name'] }}</span>
                                                <span class="admin-table__subtext">{{ $row['role'] }}</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td>{{ $row['action'] }}</td>
                                    <td title="{{ $row['object'] }}">
                                        {{ $row['object'] }}
                                        @if (! empty($row['reference']))
                                            <x-ui.pv-reference :reference="$row['reference']" />
                                        @endif
                                    </td>
                                    <td class="admin-muted">{{ $row['date'] }}</td>
                                    <td><x-admin.status :label="$row['type']" :tone="$row['tone']" /></td>
                                    <td class="col-actions">
                                        <div class="admin-table__actions">
                                            <div class="admin-dropdown">
                                                <button class="admin-table__filter" type="button" data-dropdown aria-controls="admin-journal-menu-{{ $index }}" aria-expanded="false" aria-label="Actions pour {{ $row['name'] }}">
                                                    <x-admin.icon name="more" />
                                                </button>
                                                <div class="admin-dropdown__menu" id="admin-journal-menu-{{ $index }}" hidden>
                                                    <a class="admin-dropdown__item" href="{{ route('admin.journal.index') }}"><x-admin.icon name="eye" /> Voir le détail</a>
                                                    <a class="admin-dropdown__item" href="{{ route('admin.journal.index') }}"><x-admin.icon name="copy" /> Copier la référence</a>
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

            <div id="admin-panel-pv" role="tabpanel" aria-labelledby="admin-tab-pv" data-tab-panel="pv" hidden>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>5 derniers PV enregistrés</caption>
                        <colgroup>
                            <col class="col-ref" style="width: 178px;" />
                            <col style="width: 260px;" />
                            <col style="width: 150px;" />
                            <col style="width: 130px;" />
                            <col style="width: 120px;" />
                            <col class="col-actions" style="width: 120px;" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-ref" scope="col">Référence</th>
                                <th scope="col">Filière et niveau</th>
                                <th scope="col">Année académique</th>
                                <th scope="col">Session</th>
                                <th scope="col">Statut</th>
                                <th class="col-actions" scope="col"><span class="sr-only" hidden>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestPv as $index => $pv)
                                <tr class="{{ $index === 0 ? 'is-highlighted' : '' }}">
                                    <td class="col-ref"><x-ui.pv-reference :reference="$pv['reference']" /></td>
                                    <td title="{{ $pv['promotion'] }}">{{ $pv['promotion'] }}</td>
                                    <td class="admin-muted">{{ $pv['annee'] }}</td>
                                    <td>{{ $pv['session'] }}</td>
                                    <td><x-admin.status :label="$pv['status']" :tone="$pv['tone']" /></td>
                                    <td class="col-actions">
                                        <x-ui.table-actions :reference="$pv['reference']" :preview="'#'" :actions="[]" empty-message="Aucune action secondaire pour ce PV" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-pagination">
                <span>6 entrées affichées sur 248</span>
                <a class="admin-table__link" href="{{ route('admin.journal.index') }}">Ouvrir le journal complet</a>
            </div>
        </div>
    </section>
@endsection

@section('aside')
    <div class="admin-aside__profile">
        <div class="admin-aside__profile-actions">
            <a class="admin-icon-button admin-icon-button--plain" href="{{ route('admin.parametres.index') }}" aria-label="Paramètres"><x-admin.icon name="settings" /></a>
            @include('admin.partials.notifications', ['menuId' => 'admin-notifications-aside-menu', 'triggerId' => 'admin-notifications-aside-trigger'])
        </div>
        <div class="admin-aside__profile-text">
            <strong class="admin-aside__profile-name">Dr Jean Théophile Agadamè</strong>
            <span class="admin-aside__profile-role">Administrateur</span>
        </div>
        <x-admin.avatar initials="JA" size="sm" label="Profil de Dr Jean Théophile Agadamè" />
    </div>

    <section class="admin-widget" aria-labelledby="admin-year-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="admin-year-widget-title">Année académique</h2>
        </div>
        <div class="admin-year-picker">
            <button class="admin-year-picker__nav" type="button" aria-label="Année précédente"><x-admin.icon name="chevron-left" /></button>
            <span class="admin-year-picker__value">2025-2026</span>
            <button class="admin-year-picker__nav" type="button" aria-label="Année suivante"><x-admin.icon name="chevron-right" /></button>
        </div>
        <div class="admin-page-header__actions" role="group" aria-label="Sessions de l'année 2025-2026">
            <x-ui.btn variant="compact" type="button" aria-pressed="true">Normale</x-ui.btn>
            <x-ui.btn variant="compact" type="button" aria-pressed="false">Rattrapage</x-ui.btn>
        </div>
    </section>

    <section class="admin-widget" aria-labelledby="admin-progress-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="admin-progress-widget-title">Progression de la numérisation</h2>
            <x-ui.btn variant="secondary" href="{{ route('admin.plan.index') }}">Voir tout</x-ui.btn>
        </div>

        <div class="admin-progress-list">
            @foreach ($filieres as $filere)
                @php
                    $percentage = (int) round($filere['done'] / $filere['expected'] * 100);
                @endphp
                <div class="admin-progress-item">
                    <div class="admin-progress-item__head">
                        <span class="admin-progress-item__initials">{{ $filere['short'] }}</span>
                        <span class="admin-progress-item__text">
                            <span class="admin-progress-item__name">{{ $filere['name'] }}</span>
                            <span class="admin-progress-item__meta">{{ $filere['done'] }} / {{ $filere['expected'] }} PV</span>
                        </span>
                        <span class="admin-progress-item__value">{{ $percentage }} %</span>
                    </div>
                    <x-admin.progress :value="$percentage" :label="'Progression '.$filere['name']" />
                </div>
            @endforeach
        </div>
    </section>

    <section class="admin-widget" aria-labelledby="admin-missing-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="admin-missing-widget-title">PV manquants</h2>
            <x-ui.btn variant="secondary" href="{{ route('admin.plan.index') }}">Voir tout</x-ui.btn>
        </div>

        @foreach ($missingPv as $missing)
            <div class="admin-missing-card">
                <span>
                    <span class="admin-missing-card__title">PV manquant : {{ $missing['promotion'] }}, session {{ strtolower($missing['session']) }} {{ $missing['annee'] }}.</span>
                </span>
                <x-ui.btn variant="compact" href="{{ route('admin.plan.index') }}">Voir</x-ui.btn>
            </div>
        @endforeach
    </section>
@endsection
