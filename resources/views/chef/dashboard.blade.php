@extends('layouts.admin', ['roleKey' => $roleKey ?? 'chef'])

@section('title', 'Tableau de bord')

@section('content')
    @php
        $roleKey = $roleKey ?? 'chef';
        $profile = $profile ?? config('ui-roles.roles.chef.profile');
        $scopeFilieres = $scopeFilieres ?? config('ui-roles.roles.chef.profile.filieres', []);
        $filieres = collect($filieres ?? config('ui-demo.filieres', []))->keyBy('code');
        $pvs = collect($pvs ?? []);
        $stats = $stats ?? [
            'archives' => $pvs->where('status', 'Archivé')->count(),
            'brouillons' => $pvs->where('status', 'Brouillon')->count(),
            'a_numeriser' => 0,
            'etudiants' => 0,
        ];
        $chartSeries = $chartSeries ?? [];
        $chartYears = $chartYears ?? ['2023-2024', '2024-2025', '2025-2026'];
        $coverage = $coverage ?? [];
        $chartColors = ['#0b3394', '#2f6bff', '#f5b91e'];
        $chartCenters = count($chartSeries) > 0
            ? array_map(fn (int $index): int => 170 + $index * 230, array_keys($chartSeries))
            : [170];
        $activeYear = $currentAnnee ?? old('annee', request('annee', date('Y').'-'.(date('Y') + 1)));
        $activeSession = old('session', request('session', 'Normale'));
        $scopeLabel = $scopeFilieres !== [] ? implode(' et ', $scopeFilieres) : 'votre département';

        $filiereLabel = function (string $code) use ($filieres): string {
            $filiere = $filieres->get($code);

            return is_array($filiere) ? $filiere['label'] : $code;
        };

        $promotionsCoverage = $pvs
            ->map(fn ($pv) => $pv['filiere'].'-'.$pv['niveau'])
            ->unique()
            ->count();

        $statCards = [
            ['label' => 'PV disponibles', 'value' => (string) $pvs->count(), 'meta' => 'Périmètre '.$scopeLabel, 'icon' => 'pv', 'tone' => 'blue'],
            ['label' => 'Étudiants référencés', 'value' => number_format($stats['etudiants'], 0, ',', ' '), 'meta' => 'Fiches '.$scopeLabel, 'icon' => 'users', 'tone' => 'blue'],
            ['label' => 'PV archivés', 'value' => (string) $stats['archives'], 'meta' => 'Toutes sessions', 'icon' => 'archive', 'tone' => 'gold'],
            ['label' => 'Promotions couvertes', 'value' => (string) $promotionsCoverage, 'meta' => 'Filières '.$scopeLabel, 'icon' => 'layers', 'tone' => 'blue'],
        ];

        $drawerIdFor = function (string $reference): string {
            return 'apercu-'.preg_replace('/[^A-Za-z0-9_-]/', '', $reference);
        };
    @endphp

    <nav class="admin-breadcrumb" aria-label="Fil d'Ariane">
        <span>{{ $profile['role'] }}</span>
        <x-admin.icon name="chevron-right" />
        <span>Tableau de bord</span>
    </nav>

    <section class="admin-banner" aria-labelledby="chef-banner-title">
        <span class="admin-banner__surface" aria-hidden="true"></span>

        <div class="admin-banner__content">
            <span class="admin-banner__eyebrow"><x-admin.icon name="shield" /> {{ $profile['department'] }}</span>
            <h1 class="admin-banner__title" id="chef-banner-title">Bonjour, {{ $profile['name'] }}</h1>
            <p class="admin-banner__text">{{ $profile['department'] }} : {{ $stats['archives'] }} PV archivés sur {{ $pvs->count() }} PV accessibles.</p>
            <x-ui.btn
                :href="route('chef.pv.index')"
                variant="compact"
                class="admin-button--secondary"
                icon="eye"
                label="Ouvrir le registre"
            />
        </div>

        <div class="admin-banner__brand">
            <x-ui.brand />
        </div>
    </section>

    <section class="admin-section" aria-labelledby="chef-indicators-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="chef-indicators-title">Indicateurs du département</h2>
                <p class="admin-section__hint">Périmètre : filières {{ $scopeLabel }}, année académique {{ $activeYear }}.</p>
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

    <section class="admin-section" aria-labelledby="chef-chart-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="chef-chart-title">PV enregistrés par filière et par année</h2>
                <p class="admin-section__hint">Comparaison des années académiques, toutes sessions confondues.</p>
            </div>
        </div>

        <div class="admin-card admin-chart">
            <div class="admin-chart__legend" aria-hidden="true">
                @foreach ($chartYears as $yearIndex => $year)
                    <span class="admin-chart__legend-item"><span class="admin-chart__legend-dot admin-chart__legend-dot--{{ ['night', 'blue', 'gold'][$yearIndex] ?? 'night' }}"></span> {{ $year }}</span>
                @endforeach
            </div>

            <svg
                width="560"
                height="260"
                viewBox="0 0 560 260"
                role="img"
                aria-label="Nombre de PV des filières du département par année académique"
            >
                @foreach ([0, 25, 50, 75, 100] as $tick)
                    @php
                        $y = 210 - $tick * 1.8;
                    @endphp
                    <path d="M52 {{ $y }}H534" stroke="#DDE5FA" stroke-width="1"></path>
                    <text x="44" y="{{ $y + 4 }}" text-anchor="end" font-size="10" fill="#000000">{{ $tick }}</text>
                @endforeach

                @foreach ($chartSeries as $seriesIndex => $serie)
                    @php
                        $center = $chartCenters[$seriesIndex];
                    @endphp
                    @foreach ($serie['values'] as $barIndex => $value)
                        @php
                            $x = $center + ($barIndex - 1) * 38 - 15;
                            $height = $value * 1.8;
                            $y = 210 - $height;
                            $tooltip = $serie['name'].' — '.$chartYears[$barIndex].' : '.$value.' PV';
                        @endphp
                        <rect
                            class="admin-chart__bar"
                            x="{{ $x }}"
                            y="{{ $y }}"
                            width="30"
                            height="{{ $height }}"
                            rx="3"
                            fill="{{ $chartColors[$barIndex] }}"
                            data-tooltip="{{ $tooltip }}"
                        >
                            <title>{{ $tooltip }}</title>
                        </rect>
                    @endforeach
                    <text x="{{ $center }}" y="236" text-anchor="middle" font-size="11" fill="#09090b">{{ $serie['name'] }}</text>
                @endforeach

                <text x="52" y="254" font-size="10" fill="#000000">Nombre de PV</text>
            </svg>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="chef-pv-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="chef-pv-title">Derniers PV du département</h2>
                <p class="admin-section__hint">Consultation des procès-verbaux des filières {{ $scopeLabel }}.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.badge label="Consultation" tone="neutral" />
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h3 class="admin-card__title">Registre départemental</h3>
                <span class="admin-tiny admin-muted">{{ $pvs->count() }} PV accessibles</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Derniers procès-verbaux du département Informatique et Statistique</caption>
                    <colgroup>
                        <col class="col-ref">
                        <col>
                        <col class="col-date">
                        <col>
                        <col class="col-date">
                        <col class="col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Filière et niveau</th>
                            <th scope="col">Année académique</th>
                            <th scope="col">Session</th>
                            <th scope="col">Date de délibération</th>
                            <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pvs->take(5) as $pv)
                            @php
                                $drawerId = $drawerIdFor($pv['reference']);
                            @endphp
                            <tr class="is-hoverable-row">
                                <td><x-ui.pv-reference :reference="$pv['reference']" /></td>
                                <td>
                                    <span class="admin-table__primary">{{ $filiereLabel($pv['filiere']) }}</span>
                                    <span class="admin-table__subtext">{{ $pv['niveau'] }}</span>
                                </td>
                                <td class="admin-muted">{{ $pv['annee'] }}</td>
                                <td>{{ $pv['session'] }}</td>
                                <td class="admin-muted">{{ \Illuminate\Support\Carbon::parse($pv['date'])->format('d/m/Y') }}</td>
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
    </section>

    @foreach ($pvs->take(5) as $pv)
        <x-ui.drawer
            :id="$drawerIdFor($pv['reference'])"
            title="Aperçu du procès-verbal"
            :subtitle="$pv['reference'].' — '.$filiereLabel($pv['filiere'])"
            :wide="true"
        >
            <x-ui.viewer
                :reference="$pv['reference']"
                :pages="$pv['pages'] ?? 4"
                :can-download="false"
            />
        </x-ui.drawer>
    @endforeach
@endsection

@section('aside')
    <div class="admin-aside__profile">
        <div class="admin-aside__profile-actions">
            <a class="admin-icon-button admin-icon-button--plain" href="{{ route('chef.profil') }}" aria-label="Mon profil">
                <x-admin.icon name="user" />
            </a>
        </div>
        <div class="admin-aside__profile-text">
            <strong class="admin-aside__profile-name">{{ $profile['name'] }}</strong>
            <span class="admin-aside__profile-role">{{ $profile['role'] }}</span>
        </div>
        <x-admin.avatar :initials="$profile['initials']" size="sm" label="Profil de {{ $profile['name'] }}" />
    </div>

    <section class="admin-widget" aria-labelledby="chef-year-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="chef-year-widget-title">Année académique</h2>
        </div>
        <div class="admin-year-picker">
            <label class="sr-only" for="chef-annee" hidden>Année académique</label>
            <select class="admin-select" id="chef-annee" name="annee" aria-label="Année académique">
                @foreach ($annees ?? config('ui-demo.annees', []) as $annee)
                    <option value="{{ $annee }}" @selected($annee === $activeYear)>{{ $annee }}</option>
                @endforeach
            </select>
        </div>
        <div role="group" aria-label="Sessions de l'année {{ $activeYear }}">
            @foreach ($sessions ?? config('ui-demo.sessions', []) as $session)
                @php
                    $isActive = $session === $activeSession;
                @endphp
                <x-ui.btn
                    variant="compact"
                    class="{{ $isActive ? '' : 'admin-button--secondary' }}"
                    :aria-pressed="$isActive ? 'true' : 'false'"
                    :aria-current="$isActive ? 'true' : null"
                >
                    <x-ui.badge :label="$session" :tone="$isActive ? 'dark' : 'neutral'" />
                </x-ui.btn>
            @endforeach
        </div>
    </section>

    <section class="admin-widget" aria-labelledby="chef-coverage-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="chef-coverage-widget-title">Couverture des PV</h2>
        </div>
        @php
            $coverageDone = collect($coverage)->sum('done');
            $coverageExpected = collect($coverage)->sum('expected');
        @endphp
        <p class="admin-tiny admin-muted">{{ $coverageDone }} / {{ $coverageExpected }} PV · {{ $coverageExpected > 0 ? (int) round($coverageDone / $coverageExpected * 100) : 0 }} %</p>

        @foreach ($coverage as $item)
            @php
                $percentage = $item['expected'] > 0 ? (int) round($item['done'] / $item['expected'] * 100) : 0;
            @endphp
            <div class="admin-progress-item">
                <div class="admin-progress-item__head">
                    <span class="admin-progress-item__initials">{{ $item['code'] }}</span>
                    <span class="admin-progress-item__text">
                        <span class="admin-progress-item__name">{{ $item['label'] }}</span>
                        <span class="admin-progress-item__meta">{{ $item['done'] }} / {{ $item['expected'] }} PV</span>
                    </span>
                    <span class="admin-progress-item__value">{{ $percentage }} %</span>
                </div>
                <div
                    class="ui-progress-line"
                    role="progressbar"
                    aria-label="Couverture {{ $item['label'] }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-valuenow="{{ $percentage }}"
                >
                    <span style="width: {{ $percentage }}%"></span>
                </div>
            </div>
        @endforeach

        <p class="admin-tiny admin-muted">Indicateur informatif, périmètre IG et STAT.</p>
    </section>
@endsection
