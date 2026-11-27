@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    @php
        $today = \Illuminate\Support\Carbon::now()->locale('fr')->translatedFormat('l d F Y');
        $profile = $profile ?? config('ui-roles.roles.admin.profile');
        $pvs = collect($pvs ?? []);
        $stats = $stats ?? [
            'archives' => $pvs->where('status', 'Archivé')->count(),
            'brouillons' => $pvs->where('status', 'Brouillon')->count(),
            'a_numeriser' => 0,
            'etudiants' => 0,
        ];
        $filieres = $filieres ?? [];
        $journalRows = $journalRows ?? [];
        $latestPv = $latestPv ?? [];
        $missingPv = $missingPv ?? [];
        $chartSeries = $chartSeries ?? [];
        $chartYears = $chartYears ?? ['2023-2024', '2024-2025', '2025-2026'];
        $activeYear = $currentAnnee ?? ($chartYears[count($chartYears) - 1] ?? date('Y').'-'.(date('Y') + 1));

        $statCards = [
            ['label' => 'PV archivés', 'value' => (string) $stats['archives'], 'meta' => 'Registre global', 'icon' => 'archive', 'tone' => 'blue'],
            ['label' => 'PV en brouillon', 'value' => (string) $stats['brouillons'], 'meta' => 'En attente de classement', 'icon' => 'pv', 'tone' => 'blue'],
            ['label' => 'PV attendus', 'value' => (string) $stats['a_numeriser'], 'meta' => 'Promotions à numériser', 'icon' => 'clock', 'tone' => 'gold'],
            ['label' => 'Étudiants référencés', 'value' => number_format($stats['etudiants'], 0, ',', ' '), 'meta' => 'Toutes filières', 'icon' => 'users', 'tone' => 'blue'],
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
            <h1 class="admin-banner__title" id="admin-banner-title">Bonjour, {{ $profile['name'] }}</h1>
            <p class="admin-banner__text">Nous sommes le {{ $today }}. {{ $stats['a_numeriser'] }} PV restent à numériser et {{ $stats['brouillons'] }} brouillons attendent un classement.</p>
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
                <h2 class="admin-section__title" id="admin-success-title">PV enregistrés par filière et par année</h2>
                <p class="admin-section__hint">Comparaison des années académiques, toutes sessions confondues.</p>
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
                @foreach ($chartYears as $yearIndex => $year)
                    <span class="admin-chart__legend-item"><span class="admin-chart__legend-dot admin-chart__legend-dot--{{ ['night', 'blue', 'gold'][$yearIndex] ?? 'night' }}"></span> {{ $year }}</span>
                @endforeach
            </div>

            <svg viewBox="0 0 760 260" width="760" height="260" role="img" aria-label="Graphique des PV enregistrés par filière et par année">
                @foreach ([0, 25, 50, 75, 100] as $tick)
                    @php
                        $y = 210 - $tick * 1.8;
                    @endphp
                    <path d="M50 {{ $y }}H740" stroke="#DDE5FA" stroke-width="1"></path>
                    <text x="42" y="{{ $y + 4 }}" text-anchor="end" font-size="10" fill="#000000">{{ $tick }}</text>
                @endforeach

                @php
                    $series = $chartSeries;
                    $colors = ['#08236B', '#2F6BFF', '#F5B91E'];
                    $years = $chartYears;
                    $step = count($series) > 0 ? (int) (620 / count($series)) : 0;
                    $centers = collect($series)
                        ->keys()
                        ->map(fn (int $index): int => 80 + $index * $step + (int) ($step / 2))
                        ->all();
                @endphp

                @foreach ($series as $index => $serie)
                    @foreach ($serie['values'] as $barIndex => $value)
                        @php
                            $x = $centers[$index] - 36 + $barIndex * 27;
                            $height = $value * 1.8;
                            $y = 210 - $height;
                            $tooltip = $serie['name'].' — '.$years[$barIndex].' : '.$value.' PV';
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
                <span>{{ count($journalRows) }} entrées affichées</span>
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
            <strong class="admin-aside__profile-name">{{ $profile['name'] }}</strong>
            <span class="admin-aside__profile-role">{{ $profile['role'] }}</span>
        </div>
        <x-admin.avatar :initials="$profile['initials']" size="sm" label="Profil de {{ $profile['name'] }}" />
    </div>

    <section class="admin-widget" aria-labelledby="admin-year-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="admin-year-widget-title">Année académique</h2>
        </div>
        <div class="admin-year-picker">
            <button class="admin-year-picker__nav" type="button" aria-label="Année précédente"><x-admin.icon name="chevron-left" /></button>
            <span class="admin-year-picker__value">{{ $activeYear }}</span>
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
            @foreach ($filieres as $filiere)
                @php
                    $percentage = $filiere['expected'] > 0 ? (int) round($filiere['done'] / $filiere['expected'] * 100) : 0;
                @endphp
                <div class="admin-progress-item">
                    <div class="admin-progress-item__head">
                        <span class="admin-progress-item__initials">{{ $filiere['short'] }}</span>
                        <span class="admin-progress-item__text">
                            <span class="admin-progress-item__name">{{ $filiere['name'] }}</span>
                            <span class="admin-progress-item__meta">{{ $filiere['done'] }} / {{ $filiere['expected'] }} PV</span>
                        </span>
                        <span class="admin-progress-item__value">{{ $percentage }} %</span>
                    </div>
                    <x-admin.progress :value="$percentage" :label="'Progression '.$filiere['name']" />
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
