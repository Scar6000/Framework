@extends('layouts.admin')

@section('title', 'Recherche')

@section('page-title', 'Recherche multicritère')
@section('page-subtitle', 'Recherche dans les PV et les étudiants de votre périmètre')

@section('content')
    @php
        $tonStatut = fn (string $statut): string => match ($statut) {
            'valide' => 'blue',
            'archive' => 'dark',
            'soumis' => 'gold',
            default => 'slate',
        };
    @endphp

    <x-admin.page-header title="Recherche" subtitle="Combinez les critères pour retrouver un PV ou un étudiant." :breadcrumb="['Gestion des PV', 'Recherche']" />

    <form class="admin-filters" method="GET" action="{{ route('recherche.index') }}">
        <label class="admin-field admin-field--grow">
            <span class="admin-field__label">Étudiant</span>
            <input class="admin-input" name="etudiant" type="search" value="{{ $filtres['etudiant'] ?? '' }}" placeholder="Nom, prénom ou matricule">
        </label>

        <label class="admin-field">
            <span class="admin-field__label">Référence du PV</span>
            <input class="admin-input" name="q" type="search" value="{{ $filtres['q'] ?? '' }}" placeholder="PV-2026…">
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
            <span class="admin-field__label">Niveau</span>
            <select class="admin-select" name="niveau_id">
                <option value="">Tous</option>
                @foreach ($references['niveaux'] as $niveau)
                    <option value="{{ $niveau->id }}" @selected(($filtres['niveau_id'] ?? '') == $niveau->id)>{{ $niveau->code }}</option>
                @endforeach
            </select>
        </label>

        <label class="admin-field">
            <span class="admin-field__label">Semestre</span>
            <select class="admin-select" name="semestre_id">
                <option value="">Tous</option>
                @foreach ($references['semestres'] as $semestre)
                    <option value="{{ $semestre->id }}" @selected(($filtres['semestre_id'] ?? '') == $semestre->id)>{{ $semestre->code }}</option>
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

        <label class="admin-field">
            <span class="admin-field__label">Décision</span>
            <select class="admin-select" name="decision">
                <option value="">Toutes</option>
                @foreach ($decisions as $decision)
                    <option value="{{ $decision->value }}" @selected(($filtres['decision'] ?? '') === $decision->value)>{{ $decision->label() }}</option>
                @endforeach
            </select>
        </label>

        <button class="admin-button admin-button--small" type="submit">
            <x-admin.icon name="search" /> Rechercher
        </button>
        <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('recherche.index') }}">Réinitialiser</a>
    </form>

    <section class="admin-section" aria-labelledby="recherche-pv-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="recherche-pv-title">Procès-verbaux</h2>
                <span class="admin-muted admin-tiny">{{ $pvs->total() }} résultat(s)</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Procès-verbaux correspondant à la recherche</caption>
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Promotion</th>
                            <th scope="col">Session</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pvs as $pv)
                            <tr>
                                <td><a class="admin-table__link" href="{{ route('pv.show', $pv) }}">{{ $pv->reference }}</a></td>
                                <td>
                                    <span class="admin-table__primary">{{ $pv->promotion?->filiere?->code }} — {{ $pv->promotion?->semestre?->niveau?->code }} {{ $pv->promotion?->semestre?->code }}</span>
                                    <span class="admin-table__subtext">{{ $pv->promotion?->annee?->libelle }}</span>
                                </td>
                                <td>{{ $pv->session?->label() }}</td>
                                <td><x-admin.status :label="$pv->statut?->label()" :tone="$tonStatut($pv->statut?->value ?? 'brouillon')" /></td>
                                <td>
                                    <a class="admin-table__filter" href="{{ route('pv.show', $pv) }}" aria-label="Consulter {{ $pv->reference }}">
                                        <x-admin.icon name="eye" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><span class="admin-empty">Aucun PV ne correspond à la recherche.</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-pagination">{{ $pvs->links() }}</div>
    </section>

    @if ($etudiants !== null)
        <section class="admin-section" aria-labelledby="recherche-etudiants-title">
            <div class="admin-card admin-card--flush">
                <div class="admin-card__head">
                    <h2 class="admin-card__title" id="recherche-etudiants-title">Étudiants</h2>
                    <span class="admin-muted admin-tiny">{{ $etudiants->total() }} résultat(s)</span>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <caption class="sr-only" hidden>Étudiants correspondant à la recherche</caption>
                        <thead>
                            <tr>
                                <th scope="col">Matricule</th>
                                <th scope="col">Nom et prénoms</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($etudiants as $etudiant)
                                <tr>
                                    <td>{{ $etudiant->matricule }}</td>
                                    <td>{{ $etudiant->nom }} {{ $etudiant->prenoms }}</td>
                                    <td>
                                        <a class="admin-table__filter" href="{{ route('etudiants.show', $etudiant) }}" aria-label="Voir la fiche de {{ $etudiant->nom }}">
                                            <x-admin.icon name="eye" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"><span class="admin-empty">Aucun étudiant ne correspond à la recherche.</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-pagination">{{ $etudiants->links('pagination::simple-tailwind') }}</div>
        </section>
    @endif
@endsection
