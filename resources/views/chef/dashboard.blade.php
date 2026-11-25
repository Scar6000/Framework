@extends('layouts.admin', ['roleKey' => $roleKey ?? 'chef'])

@section('title', 'Tableau de bord')

@section('content')
    @php
        $roleKey = $roleKey ?? 'chef';
        $profile = config('ui-roles.roles.chef.profile');
        $scopeFilieres = $profile['filieres'] ?? ['IG', 'STAT'];

        $filieres = collect(config('ui-demo.filieres', []))
            ->whereIn('code', $scopeFilieres)
            ->keyBy('code');

        $filiereLabel = function (string $code) use ($filieres): string {
            $filiere = $filieres->get($code);

            return is_array($filiere) ? $filiere['label'] : $code;
        };

        $pvs = collect(config('ui-demo.pvs', []))
            ->whereIn('filiere', $scopeFilieres)
            ->sortByDesc('date')
            ->values();

        $students = collect(config('ui-demo.etudiants', []))
            ->whereIn('filiere', $scopeFilieres)
            ->values();

        $promotionsCoverage = $pvs
            ->map(fn ($pv) => $pv['filiere'].'-'.$pv['niveau'])
            ->unique()
            ->count();

        $statCards = [
            ['label' => 'PV disponibles', 'value' => '38', 'meta' => 'Année 2025-2026', 'icon' => 'pv', 'tone' => 'blue'],
            ['label' => 'Étudiants référencés', 'value' => (string) $students->count(), 'meta' => 'Fiches IG et STAT', 'icon' => 'users', 'tone' => 'blue'],
            ['label' => 'Taux de réussite du département', 'value' => '82,4 %', 'meta' => 'Toutes sessions', 'icon' => 'chart', 'tone' => 'gold'],
            ['label' => 'Promotions couvertes', 'value' => (string) $promotionsCoverage, 'meta' => 'Filières IG et STAT', 'icon' => 'layers', 'tone' => 'blue'],
        ];

        $chartSeries = [
            ['code' => 'IG', 'name' => 'Informatique de gestion', 'values' => [68, 72, 78]],
            ['code' => 'STAT', 'name' => 'Statistique', 'values' => [62, 66, 74]],
        ];

        $chartYears = ['2023-2024', '2024-2025', '2025-2026'];
        $chartColors = ['#0b3394', '#2f6bff', '#f5b91e'];
        $chartCenters = [170, 400];

        $activeYear = old('annee', request('annee', '2025-2026'));
        $activeSession = old('session', request('session', 'Normale'));

        $coverage = [
            ['code' => 'IG', 'label' => 'Informatique de gestion', 'done' => 3, 'expected' => 4],
            ['code' => 'STAT', 'label' => 'Statistique', 'done' => 2, 'expected' => 2],
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
            <h1 class="admin-banner__title" id="chef-banner-title">Bonjour, M. Hounton</h1>
            <p class="admin-banner__text">Département Informatique et Statistique : 38 PV archivés pour l'année 2025-2026.</p>
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
                <p class="admin-section__hint">Périmètre : filières IG et STAT, année académique 2025-2026.</p>
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
                <h2 class="admin-section__title" id="chef-chart-title">Taux de réussite par filière et par année</h2>
                <p class="admin-section__hint">Comparaison des trois dernières années académiques, toutes sessions confondues.</p>
            </div>
        </div>

        <div class="admin-card admin-chart">
            <div class="admin-chart__legend" aria-hidden="true">
                <span class="admin-chart__legend-item"><span class="admin-chart__legend-dot admin-chart__legend-dot--night"></span> 2023-2024</span>
                <span class="admin-chart__legend-item"><span class="admin-chart__legend-dot admin-chart__legend-dot--blue"></span> 2024-2025</span>
                <span class="admin-chart__legend-item"><span class="admin-chart__legend-dot admin-chart__legend-dot--gold"></span> 2025-2026</span>
            </div>

            <svg
                width="560"
                height="260"
                viewBox="0 0 560 260"
                role="img"
                aria-label="Taux de réussite des filières IG et STAT sur trois années académiques"
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
                            $tooltip = $serie['name'].' — '.$chartYears[$barIndex].' : '.$value.' %';
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

                <text x="52" y="254" font-size="10" fill="#000000">Taux en pourcentage</text>
            </svg>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="chef-pv-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="chef-pv-title">Derniers PV du département</h2>
                <p class="admin-section__hint">Consultation des procès-verbaux des filières IG et STAT.</p>
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
                @foreach (config('ui-demo.annees', []) as $annee)
                    <option value="{{ $annee }}" @selected($annee === $activeYear)>{{ $annee }}</option>
                @endforeach
            </select>
        </div>
        <div role="group" aria-label="Sessions de l'année {{ $activeYear }}">
            @foreach (config('ui-demo.sessions', []) as $session)
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
        <p class="admin-tiny admin-muted">5 / 6 PV · 83 %</p>

        @foreach ($coverage as $item)
            @php
                $percentage = (int) round($item['done'] / $item['expected'] * 100);
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
