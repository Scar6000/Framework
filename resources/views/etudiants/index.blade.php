@extends('layouts.admin')

@section('title', 'Étudiants')

@section('page-title', 'Étudiants')
@section('page-subtitle', 'Fiches des étudiants présents dans les PV de votre périmètre')

@section('content')
    <x-admin.page-header title="Étudiants" subtitle="Recherchez un étudiant et consultez son parcours officiel." :breadcrumb="['Gestion des PV', 'Étudiants']" />

    <form class="admin-filters" method="GET" action="{{ route('etudiants.index') }}">
        <label class="admin-field admin-field--grow">
            <span class="admin-field__label">Nom, prénom ou matricule</span>
            <input class="admin-input" name="q" type="search" value="{{ $q }}" placeholder="Rechercher un étudiant">
        </label>
        <button class="admin-button admin-button--small" type="submit">
            <x-admin.icon name="search" /> Rechercher
        </button>
        <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('etudiants.index') }}">Réinitialiser</a>
    </form>

    <section class="admin-section" aria-labelledby="etudiants-list-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="etudiants-list-title">Étudiants</h2>
                <span class="admin-muted admin-tiny">{{ $etudiants->total() }} résultat(s)</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Liste des étudiants</caption>
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
                                <td>
                                    <span class="admin-cell-user">
                                        <x-admin.avatar :initials="mb_strtoupper(mb_substr($etudiant->nom, 0, 1).mb_substr($etudiant->prenoms, 0, 1))" size="sm" tone="soft" />
                                        <span class="admin-cell-user__text">
                                            <span class="admin-table__primary">{{ $etudiant->nom }} {{ $etudiant->prenoms }}</span>
                                            <span class="admin-table__subtext">{{ $etudiant->matricule }}</span>
                                        </span>
                                    </span>
                                </td>
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

        <div class="admin-pagination">{{ $etudiants->links() }}</div>
    </section>
@endsection
