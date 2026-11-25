@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    @php
        $today = \Illuminate\Support\Carbon::now()->locale('fr')->translatedFormat('l d F Y');

        $statCards = [
            ['label' => 'PV numérisés', 'value' => '212 / 240', 'meta' => '88,3 % de couverture', 'icon' => 'pv', 'tone' => 'blue'],
            ['label' => 'PV validés', 'value' => '187', 'meta' => 'En attente : 25', 'icon' => 'shield', 'tone' => 'blue'],
            ['label' => 'PV archivés', 'value' => '96', 'meta' => 'Année 2025-2026', 'icon' => 'archive', 'tone' => 'blue'],
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
            ['initials' => 'JA', 'name' => 'Dr Jean Théophile Agadamè', 'role' => 'Administrateur', 'action' => 'Modification', 'object' => 'PV / PV-2026-IG-L3-S2-014', 'date' => '24/11/2026 — 09 h 42', 'type' => 'Modification', 'tone' => 'gold', 'highlight' => true],
            ['initials' => 'AK', 'name' => 'Aminata Koné', 'role' => 'Agent de scolarité', 'action' => 'Enregistrement', 'object' => 'PV / PV-2026-BF-L2-S1-008', 'date' => '24/11/2026 — 08 h 57', 'type' => 'Modification', 'tone' => 'gold', 'highlight' => false],
            ['initials' => 'MD', 'name' => 'Mamadou Diop', 'role' => 'Chef de département', 'action' => 'Validation', 'object' => 'PV / PV-2026-CCA-L3-S1-021', 'date' => '23/11/2026 — 16 h 14', 'type' => 'Validation', 'tone' => 'blue', 'highlight' => false],
            ['initials' => 'SN', 'name' => 'Sophie N' . "\u{00E9}" . 'nesson', 'role' => 'Direction', 'action' => 'Consultation', 'object' => 'Fiche étudiant / ENE-2023-1048', 'date' => '23/11/2026 — 14 h 03', 'type' => 'Consultation', 'tone' => 'slate', 'highlight' => false],
            ['initials' => 'YB', 'name' => 'Yaya Bathily', 'role' => 'Agent de scolarité', 'action' => 'Import de résultats', 'object' => 'Promotion / Marketing L3', 'date' => '23/11/2026 — 11 h 26', 'type' => 'Modification', 'tone' => 'gold', 'highlight' => false],
            ['initials' => 'FL', 'name' => 'Fatou Ly', 'role' => 'Administrateur', 'action' => 'Export', 'object' => 'Statistiques comparatives 2025-2026', 'date' => '22/11/2026 — 17 h 38', 'type' => 'Export', 'tone' => 'dark', 'highlight' => false],
        ];

        $latestPv = [
            ['reference' => 'PV-2026-IG-L3-S2-014', 'promotion' => 'Informatique de gestion, L3', 'session' => 'Normale', 'date' => '20/11/2026', 'status' => 'Validé', 'tone' => 'blue'],
            ['reference' => 'PV-2026-BF-L2-S1-008', 'promotion' => 'Banque et finance, L2', 'session' => 'Normale', 'date' => '19/11/2026', 'status' => 'Brouillon', 'tone' => 'slate'],
            ['reference' => 'PV-2026-CCA-L3-S1-021', 'promotion' => 'Comptabilité, L3', 'session' => 'Rattrapage', 'date' => '18/11/2026', 'status' => 'Validé', 'tone' => 'blue'],
            ['reference' => 'PV-2026-GRH-L1-S2-006', 'promotion' => 'Ressources humaines, L1', 'session' => 'Normale', 'date' => '17/11/2026', 'status' => 'Archivé', 'tone' => 'dark'],
            ['reference' => 'PV-2026-MK-L3-S1-012', 'promotion' => 'Marketing, L3', 'session' => 'Normale', 'date' => '16/11/2026', 'status' => 'Validé', 'tone' => 'blue'],
        ];

        $missingPv = [
            ['promotion' => 'Comptabilité, L3, S2', 'session' => 'Normale'],
            ['promotion' => 'Statistique, L3, S2', 'session' => 'Rattrapage'],
            ['promotion' => 'Marketing, L2, S1', 'session' => 'Normale'],
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
            <a class="admin-button admin-button--gold admin-button--small" href="{{ route('admin.plan.index') }}">
                Voir les alertes
                <x-admin.icon name="arrow-right" />
            </a>
        </div>

        <svg class="admin-banner__art" viewBox="0 0 300 190" role="img" aria-label="Illustration : archivage et consultation sécurisée des PV">
            <defs>
                <linearGradient id="admin-banner-paper" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#FFFFFF"></stop>
                    <stop offset="100%" stop-color="#E8EFFF"></stop>
                </linearGradient>
            </defs>

            <ellipse cx="168" cy="177" rx="112" ry="10" fill="#08236B" opacity="0.16"></ellipse>

            <path d="M214 28 C238 34 250 58 242 80 C236 97 216 103 206 119 C198 100 200 78 208 62 C215 48 216 38 214 28 Z" fill="#DCE7FF" opacity="0.75"></path>
            <path d="M211 34 C205 62 205 84 214 105" fill="none" stroke="#FFFFFF" stroke-width="2" opacity="0.7"></path>

            <g transform="translate(52 44)">
                <rect x="0" y="0" width="118" height="86" rx="10" fill="url(#admin-banner-paper)" stroke="#DCE7FF" stroke-width="2"></rect>
                <rect x="12" y="13" width="48" height="7" rx="3.5" fill="#0B3394"></rect>
                <rect x="12" y="28" width="92" height="4" rx="2" fill="#DCE7FF"></rect>
                <rect x="12" y="39" width="72" height="4" rx="2" fill="#DCE7FF"></rect>
                <path d="M12 54H102M12 66H102" stroke="#DCE7FF" stroke-width="3" stroke-linecap="round"></path>
                <circle cx="20" cy="61" r="3" fill="#2F6BFF"></circle>
                <circle cx="20" cy="73" r="3" fill="#F5B91E"></circle>
            </g>

            <g transform="translate(142 92)">
                <rect x="0" y="0" width="72" height="54" rx="7" fill="#08236B"></rect>
                <rect x="6" y="7" width="60" height="40" rx="4" fill="#E8EFFF"></rect>
                <path d="M14 18H38M14 27H54M14 36H44" stroke="#2F6BFF" stroke-width="3" stroke-linecap="round"></path>
                <circle cx="52" cy="18" r="4" fill="#F5B91E"></circle>
            </g>

            <g transform="translate(112 48)">
                <circle cx="28" cy="18" r="17" fill="#0B3394"></circle>
                <path d="M6 72 C6 49 16 39 28 39 C40 39 50 49 50 72" fill="#2F6BFF"></path>
                <path d="M8 58 -6 74M48 58 66 76" stroke="#0B3394" stroke-width="9" stroke-linecap="round"></path>
            </g>

            <g transform="translate(24 106)">
                <path d="M0 -22 L19 -14 V3 C19 16 11 24 0 29 C-11 24 -19 16 -19 3 V-14 Z" fill="#F5B91E"></path>
                <path d="m-8 3 6 6 11-12" fill="none" stroke="#08236B" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>

            <circle cx="278" cy="120" r="7" fill="#FFD45C" opacity="0.9"></circle>
            <circle cx="30" cy="42" r="5" fill="#FFD45C" opacity="0.85"></circle>
            <path d="M266 56 L270 66 L280 70 L270 74 L266 84 L262 74 L252 70 L262 66 Z" fill="#FFD45C" opacity="0.9"></path>
        </svg>
    </section>

    {{-- Indicateurs clés --}}
    <section class="admin-section" aria-labelledby="admin-indicators-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-indicators-title">Indicateurs clés</h2>
                <p class="admin-section__hint">Situation de la numérisation pour l'année académique 2025-2026.</p>
            </div>
            <div class="admin-section__actions">
                <a class="admin-button admin-button--small" href="{{ route('admin.pv.index') }}">Tout voir</a>
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

            <svg viewBox="0 0 760 260" role="img" aria-label="Graphique des taux de réussite par filière et par année">
                @foreach ([0, 25, 50, 75, 100] as $tick)
                    @php
                        $y = 210 - $tick * 1.8;
                    @endphp
                    <path d="M50 {{ $y }}H740" stroke="#DDE5FA" stroke-width="1"></path>
                    <text x="42" y="{{ $y + 4 }}" text-anchor="end" font-size="10" fill="#5B6B94">{{ $tick }}</text>
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
                    <text x="{{ $centers[$index] }}" y="234" text-anchor="middle" font-size="10" fill="#0F1B3D">{{ $serie['name'] }}</text>
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
                        <thead>
                            <tr>
                                <th scope="col">Utilisateur</th>
                                <th scope="col">Action</th>
                                <th scope="col">Objet</th>
                                <th scope="col">Date et heure</th>
                                <th scope="col">Type</th>
                                <th scope="col"><span class="sr-only" hidden>Actions</span><button class="admin-table__filter" type="button" aria-label="Filtrer le journal"><x-admin.icon name="filter" /></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($journalRows as $index => $row)
                                <tr class="{{ $row['highlight'] ? 'is-highlighted' : '' }}">
                                    <td>
                                        <span class="admin-cell-user">
                                            <x-admin.avatar :initials="$row['initials']" size="sm" />
                                            <span class="admin-cell-user__text">
                                                <span class="admin-table__primary">{{ $row['name'] }}</span>
                                                <span class="admin-table__subtext">{{ $row['role'] }}</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td>{{ $row['action'] }}</td>
                                    <td>{{ $row['object'] }}</td>
                                    <td class="admin-muted">{{ $row['date'] }}</td>
                                    <td><x-admin.status :label="$row['type']" :tone="$row['tone']" /></td>
                                    <td>
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
                            @foreach ($latestPv as $index => $pv)
                                <tr class="{{ $index === 0 ? 'is-highlighted' : '' }}">
                                    <td><a class="admin-table__link" href="{{ route('admin.pv.index') }}">{{ $pv['reference'] }}</a></td>
                                    <td>{{ $pv['promotion'] }}</td>
                                    <td>{{ $pv['session'] }}</td>
                                    <td class="admin-muted">{{ $pv['date'] }}</td>
                                    <td><x-admin.status :label="$pv['status']" :tone="$pv['tone']" /></td>
                                    <td><div class="admin-table__actions"><a class="admin-table__filter" href="{{ route('admin.pv.index') }}" aria-label="Ouvrir {{ $pv['reference'] }}"><x-admin.icon name="eye" /></a></div></td>
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
        <div class="admin-semester-row" role="group" aria-label="Semestres de l'année 2025-2026">
            @foreach (['S1', 'S2', 'S3', 'S4', 'S5'] as $index => $semester)
                <button class="admin-semester {{ $index === 1 ? 'is-active' : '' }}" type="button" @if ($index === 1) aria-pressed="true" @else aria-pressed="false" @endif>
                    {{ $semester }}
                    @if ($index === 1)<span class="admin-semester__dot" aria-hidden="true"></span>@endif
                </button>
            @endforeach
        </div>
    </section>

    <section class="admin-widget" aria-labelledby="admin-progress-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="admin-progress-widget-title">Progression de la numérisation</h2>
            <a class="admin-button admin-button--small" href="{{ route('admin.plan.index') }}">Voir tout</a>
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
            <a class="admin-button admin-button--small" href="{{ route('admin.plan.index') }}">Voir tout</a>
        </div>

        @foreach ($missingPv as $missing)
            <div class="admin-missing-card">
                <span>
                    <span class="admin-missing-card__title">{{ $missing['promotion'] }}</span>
                    <span class="admin-missing-card__meta">Session {{ $missing['session'] }}</span>
                </span>
                <a class="admin-button admin-button--small" href="{{ route('admin.plan.index') }}">Voir</a>
            </div>
        @endforeach
    </section>
@endsection
