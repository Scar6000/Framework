@extends('layouts.admin')

@section('title', 'PV manquants')

@section('page-title', 'Plan de numérisation des PV')
@section('page-subtitle', 'Suivi des promotions attendues et des délais')

@section('content')
    <x-admin.page-header title="PV manquants" subtitle="Identification des procès-verbaux attendus, en cours ou en retard." :breadcrumb="['Gestion des PV', 'PV manquants']">
        @slot('actions')
            <a class="admin-button admin-button--small" href="{{ route('exports.pv-manquants') }}">
                <x-admin.icon name="download" /> Exporter
            </a>
        @endslot
    </x-admin.page-header>

    @if (isset($message))
        <div class="admin-notice" role="status">
            <x-admin.icon name="info" />
            <span>{{ $message }}</span>
        </div>
    @endif

    @if ($resume)
        <section class="admin-section" aria-labelledby="pv-manquants-resume-title">
            <div class="admin-section__head">
                <div>
                    <h2 class="admin-section__title" id="pv-manquants-resume-title">Année {{ $resume['annee'] }}</h2>
                    <p class="admin-section__hint">Synthèse du plan de numérisation.</p>
                </div>
            </div>

            <div class="admin-summary-grid">
                <article class="admin-summary-card">
                    <span class="admin-summary-card__icon"><x-admin.icon name="layers" /></span>
                    <span class="admin-summary-card__value">{{ $resume['attendus'] }}</span>
                    <span class="admin-summary-card__label">PV attendus</span>
                </article>
                <article class="admin-summary-card">
                    <span class="admin-summary-card__icon"><x-admin.icon name="check" /></span>
                    <span class="admin-summary-card__value">{{ $resume['complets'] }}</span>
                    <span class="admin-summary-card__label">Complets</span>
                </article>
                <article class="admin-summary-card">
                    <span class="admin-summary-card__icon"><x-admin.icon name="clock" /></span>
                    <span class="admin-summary-card__value">{{ $resume['en_cours'] }}</span>
                    <span class="admin-summary-card__label">En cours</span>
                </article>
                <article class="admin-summary-card">
                    <span class="admin-summary-card__icon"><x-admin.icon name="alert" /></span>
                    <span class="admin-summary-card__value">{{ $resume['manquants'] }}</span>
                    <span class="admin-summary-card__label">Manquants</span>
                </article>
                <article class="admin-summary-card">
                    <span class="admin-summary-card__icon"><x-admin.icon name="chart" /></span>
                    <span class="admin-summary-card__value">{{ $resume['taux_numerisation'] }} %</span>
                    <span class="admin-summary-card__label">Taux de numérisation</span>
                </article>
            </div>
        </section>
    @endif

    <form class="admin-filters" method="GET" action="{{ route('pv-manquants.index') }}">
        <label class="admin-field">
            <span class="admin-field__label">Année académique</span>
            <select class="admin-select" name="annee_id">
                @foreach ($annees as $anneeOption)
                    <option value="{{ $anneeOption->id }}" @selected($annee?->id === $anneeOption->id)>{{ $anneeOption->libelle }}</option>
                @endforeach
            </select>
        </label>

        @if (auth()->user()?->isAdmin())
            <label class="admin-field">
                <span class="admin-field__label">Filière</span>
                <select class="admin-select" name="filiere_id">
                    <option value="">Toutes</option>
                    @foreach ($filieres as $filiere)
                        <option value="{{ $filiere->id }}" @selected(request('filiere_id') == $filiere->id)>{{ $filiere->nom }}</option>
                    @endforeach
                </select>
            </label>
        @endif

        <label class="admin-field">
            <span class="admin-field__label">État</span>
            <select class="admin-select" name="etat">
                <option value="">Tous</option>
                <option value="manquant" @selected(request('etat') === 'manquant')>Manquant</option>
                <option value="en_cours" @selected(request('etat') === 'en_cours')>En cours</option>
                <option value="complet" @selected(request('etat') === 'complet')>Complet</option>
            </select>
        </label>

        <label class="admin-checkbox">
            <input type="checkbox" name="en_retard" value="1" @checked(request('en_retard') === '1')>
            <span>Uniquement les promotions en retard</span>
        </label>

        <button class="admin-button admin-button--small" type="submit">
            <x-admin.icon name="filter" /> Filtrer
        </button>
    </form>

    <section class="admin-section" aria-labelledby="pv-manquants-list-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="pv-manquants-list-title">Promotions concernées</h2>
                <span class="admin-muted admin-tiny">{{ count($etats) }} ligne(s)</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>État de numérisation des promotions</caption>
                    <thead>
                        <tr>
                            <th scope="col">Promotion</th>
                            <th scope="col">État</th>
                            <th scope="col">PV normal</th>
                            <th scope="col">Rattrapage</th>
                            <th scope="col">Échéance</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($etats as $etat)
                            @php
                                $promotion = $etat['promotion'];
                                $tone = match ($etat['etat']) {
                                    'complet' => 'blue',
                                    'en_cours' => 'gold',
                                    default => 'red',
                                };
                            @endphp
                            <tr class="{{ $etat['en_retard'] ? 'is-highlighted' : '' }}">
                                <td>
                                    <span class="admin-table__primary">{{ $promotion->filiere->nom }}</span>
                                    <span class="admin-table__subtext">{{ $promotion->semestre->niveau->code }} — {{ $promotion->semestre->code }}</span>
                                </td>
                                <td><x-admin.status :label="$etat['etat_label']" :tone="$tone" /></td>
                                <td>{{ $etat['pv_normale']?->reference ?? '—' }}</td>
                                <td>
                                    @if ($etat['rattrapage_attendu'])
                                        {{ $etat['etat_rattrapage'] === 'complet' ? ($etat['pv_rattrapage']?->reference ?? 'Complet') : 'À numériser' }}
                                    @else
                                        <span class="admin-muted">Non requis</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $promotion->date_limite?->format('d/m/Y') ?? '—' }}
                                    @if ($etat['en_retard'])<span class="admin-table__subtext">En retard</span>@endif
                                </td>
                                <td>
                                    @if ($etat['pv_normale'])
                                        <a class="admin-table__filter" href="{{ route('pv.show', $etat['pv_normale']) }}" aria-label="Consulter le PV">
                                            <x-admin.icon name="eye" />
                                        </a>
                                    @else
                                        <span class="admin-muted admin-tiny">En attente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"><span class="admin-empty">Aucune promotion à afficher.</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
