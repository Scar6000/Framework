@extends('layouts.admin')

@section('title', 'Registre des PV')

@section('page-title', 'Registre des procès-verbaux')
@section('page-subtitle', 'Consultation et suivi des PV de votre périmètre')

@section('content')
    @php
        $tonStatut = fn (string $statut): string => match ($statut) {
            'valide' => 'blue',
            'archive' => 'dark',
            'soumis' => 'gold',
            default => 'slate',
        };
    @endphp

    <x-admin.page-header title="Registre des PV" subtitle="Recherchez et consultez les procès-verbaux de votre périmètre." :breadcrumb="['Gestion des PV', 'Registre']">
        @slot('actions')
            <a class="admin-button admin-button--small" href="{{ route('pv-manquants.index') }}">
                <x-admin.icon name="alert" /> PV manquants
            </a>
            @if (auth()->user()?->can('create', App\Models\Pv::class))
                <a class="admin-button admin-button--gold admin-button--small" href="{{ route('pv.create') }}">
                    <x-admin.icon name="plus" /> Nouveau PV
                </a>
            @endif
        @endslot
    </x-admin.page-header>

    <form class="admin-filters" method="GET" action="{{ route('pv.index') }}">
        <label class="admin-field admin-field--grow">
            <span class="admin-field__label">Référence ou étudiant</span>
            <input class="admin-input" name="q" type="search" value="{{ $filtres['q'] ?? '' }}" placeholder="PV-2026… ou nom de l'étudiant">
        </label>

        <label class="admin-field">
            <span class="admin-field__label">Année</span>
            <select class="admin-select" name="annee_id">
                <option value="">Toutes</option>
                @foreach ($references['annees'] as $annee)
                    <option value="{{ $annee->id }}" @selected(($filtres['annee_id'] ?? '') == $annee->id)>{{ $annee->libelle }}</option>
                @endforeach
            </select>
        </label>

        <label class="admin-field">
            <span class="admin-field__label">Filière</span>
            <select class="admin-select" name="filiere_id">
                <option value="">Toutes</option>
                @foreach ($references['filieres'] as $filiere)
                    <option value="{{ $filiere->id }}" @selected(($filtres['filiere_id'] ?? '') == $filiere->id)>{{ $filiere->nom }}</option>
                @endforeach
            </select>
        </label>

        <label class="admin-field">
            <span class="admin-field__label">Session</span>
            <select class="admin-select" name="session">
                <option value="">Toutes</option>
                @foreach ($sessions as $session)
                    <option value="{{ $session->value }}" @selected(($filtres['session'] ?? '') === $session->value)>{{ $session->label() }}</option>
                @endforeach
            </select>
        </label>

        <label class="admin-field">
            <span class="admin-field__label">Statut</span>
            <select class="admin-select" name="statut">
                <option value="">Tous</option>
                @foreach ($statuts as $statut)
                    <option value="{{ $statut->value }}" @selected(($filtres['statut'] ?? '') === $statut->value)>{{ $statut->label() }}</option>
                @endforeach
            </select>
        </label>

        <button class="admin-button admin-button--small" type="submit">
            <x-admin.icon name="search" /> Filtrer
        </button>
        <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('pv.index') }}">Réinitialiser</a>
    </form>

    <section class="admin-section" aria-labelledby="pv-list-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="pv-list-title">Procès-verbaux</h2>
                <span class="admin-muted admin-tiny">{{ $pvs->total() }} résultat(s)</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Liste des procès-verbaux</caption>
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Promotion</th>
                            <th scope="col">Session</th>
                            <th scope="col">Date</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Créé par</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pvs as $pv)
                            <tr>
                                <td><a class="admin-table__link" href="{{ route('pv.show', $pv) }}">{{ $pv->reference }}</a></td>
                                <td>
                                    <span class="admin-table__primary">
                                        {{ $pv->promotion?->filiere?->code }} — {{ $pv->promotion?->semestre?->niveau?->code }} {{ $pv->promotion?->semestre?->code }}
                                    </span>
                                    <span class="admin-table__subtext">{{ $pv->promotion?->annee?->libelle }}</span>
                                </td>
                                <td>{{ $pv->session?->label() }}</td>
                                <td>{{ $pv->date_deliberation?->format('d/m/Y') ?? '—' }}</td>
                                <td><x-admin.status :label="$pv->statut?->label()" :tone="$tonStatut($pv->statut?->value ?? 'brouillon')" /></td>
                                <td>{{ $pv->createur?->nom_complet ?? '—' }}</td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="admin-table__filter" href="{{ route('pv.show', $pv) }}" aria-label="Consulter {{ $pv->reference }}">
                                            <x-admin.icon name="eye" />
                                        </a>
                                        @if (auth()->user()?->can('valider', $pv))
                                            <form method="POST" action="{{ route('pv.valider', $pv) }}">
                                                @csrf
                                                <button class="admin-table__filter" type="submit" aria-label="Valider {{ $pv->reference }}">
                                                    <x-admin.icon name="check" />
                                                </button>
                                            </form>
                                        @endif
                                        @if (auth()->user()?->can('partager', $pv))
                                            <form method="POST" action="{{ route('pv.partage', $pv) }}">
                                                @csrf
                                                <button class="admin-table__filter" type="submit" aria-label="Partager {{ $pv->reference }}">
                                                    <x-admin.icon name="link" />
                                                </button>
                                            </form>
                                        @endif
                                        @if (config('pv.telechargement_autorise'))
                                            <a class="admin-table__filter" href="{{ route('pv.telecharger', $pv) }}" aria-label="Télécharger {{ $pv->reference }}">
                                                <x-admin.icon name="download" />
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"><span class="admin-empty">Aucun PV ne correspond à ces critères.</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-pagination">
            {{ $pvs->links() }}
        </div>
    </section>
@endsection
