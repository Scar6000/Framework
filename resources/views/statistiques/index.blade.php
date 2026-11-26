@extends('layouts.admin')

@section('title', 'Statistiques')

@section('page-title', 'Statistiques')
@section('page-subtitle', 'Indicateurs calculés sur les PV officiels de votre filière')

@section('content')
    @php
        $resume = $resume_pv ?? null;
        $decisions = [
            ['key' => 'admis', 'label' => 'Admis', 'icon' => 'check', 'tone' => 'blue'],
            ['key' => 'ajourne', 'label' => 'Ajournés', 'icon' => 'alert', 'tone' => 'gold'],
            ['key' => 'rattrapage', 'label' => 'Rattrapages', 'icon' => 'clock', 'tone' => 'gold'],
            ['key' => 'exclu', 'label' => 'Exclus', 'icon' => 'close', 'tone' => 'dark'],
            ['key' => 'absent', 'label' => 'Absents', 'icon' => 'user', 'tone' => 'slate'],
        ];
    @endphp

    <x-admin.page-header title="Statistiques" subtitle="Analyse des décisions issues des sessions normales officielles." :breadcrumb="['Gestion des PV', 'Statistiques']">
        @slot('actions')
            <a class="admin-button admin-button--small" href="{{ route('exports.statistiques') }}">
                <x-admin.icon name="download" /> Exporter
            </a>
        @endslot
    </x-admin.page-header>

    <form class="admin-filters" method="GET" action="{{ route('statistiques.index') }}">
        <label class="admin-field">
            <span class="admin-field__label">Année académique</span>
            <select class="admin-select" name="annee_id">
                <option value="">Toutes</option>
                @foreach ($annees as $annee)
                    <option value="{{ $annee->id }}" @selected(($filtres['annee_id'] ?? null) == $annee->id)>{{ $annee->libelle }}</option>
                @endforeach
            </select>
        </label>

        @if (auth()->user()?->isAdmin())
            <label class="admin-field">
                <span class="admin-field__label">Filière</span>
                <select class="admin-select" name="filiere_id">
                    <option value="">Toutes</option>
                    @foreach ($filieres as $filiere)
                        <option value="{{ $filiere->id }}" @selected(($filtres['filiere_id'] ?? null) == $filiere->id)>{{ $filiere->nom }}</option>
                    @endforeach
                </select>
            </label>
        @endif

        <button class="admin-button admin-button--small" type="submit">
            <x-admin.icon name="filter" /> Appliquer
        </button>
    </form>

    <section class="admin-section" aria-labelledby="stats-decisions-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="stats-decisions-title">Répartition des décisions</h2>
                <p class="admin-section__hint">Toutes les décisions confondues sur la période sélectionnée.</p>
            </div>
        </div>

        <div class="admin-stat-grid">
            @foreach ($decisions as $decision)
                <article class="admin-stat-card">
                    <span class="admin-stat-card__tile{{ $decision['tone'] === 'gold' ? ' admin-stat-card__tile--gold' : '' }}">
                        <x-admin.icon name="{{ $decision['icon'] }}" />
                    </span>
                    <div>
                        <span class="admin-stat-card__value">{{ $repartition_decisions[$decision['key']] ?? 0 }}</span>
                        <span class="admin-stat-card__label">{{ $decision['label'] }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if ($resume)
        <section class="admin-section" aria-labelledby="stats-numerisation-title">
            <div class="admin-section__head">
                <div>
                    <h2 class="admin-section__title" id="stats-numerisation-title">Numérisation — {{ $resume['annee'] }}</h2>
                    <p class="admin-section__hint">Couverture du plan de numérisation des PV.</p>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-progress-list">
                    <div class="admin-progress-item">
                        <span class="admin-progress-item__initials">PV</span>
                        <div class="admin-progress-item__text">
                            <span class="admin-progress-item__name">PV complets sur {{ $resume['attendus'] }} attendus</span>
                            <span class="admin-progress-item__meta">{{ $resume['manquants'] }} manquants · {{ $resume['en_retard'] }} en retard</span>
                            <x-admin.progress :value="$resume['taux_numerisation']" label="Taux de numérisation" tone="gold" large />
                        </div>
                        <span class="admin-progress-item__value">{{ $resume['taux_numerisation'] }} %</span>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="admin-section" aria-labelledby="stats-reussite-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="stats-reussite-title">Taux de réussite</h2>
                <p class="admin-section__hint">Admis rapportés au nombre de résultats des sessions normales.</p>
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Taux de réussite par filière et par année</caption>
                    <thead>
                        <tr>
                            <th scope="col">Année</th>
                            <th scope="col">Filière</th>
                            <th scope="col">Résultats</th>
                            <th scope="col">Admis</th>
                            <th scope="col">Taux</th>
                            <th scope="col">Progression</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($taux_reussite as $row)
                            <tr>
                                <td>{{ $row['annee'] }}</td>
                                <td>{{ $row['filiere'] }}</td>
                                <td>{{ $row['total'] }}</td>
                                <td>{{ $row['admis'] }}</td>
                                <td><strong>{{ $row['taux_reussite'] }} %</strong></td>
                                <td>
                                    <x-admin.progress :value="$row['taux_reussite']" :label="'Taux de réussite '.$row['filiere'].' '.$row['annee']" tone="blue" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"><span class="admin-empty">Aucune donnée de résultat officiel pour la période sélectionnée.</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
