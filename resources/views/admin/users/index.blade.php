@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    @php
        $roles = [
            ['key' => 'administrateur', 'label' => 'Administrateur'],
            ['key' => 'agent', 'label' => 'Agent de scolarité'],
            ['key' => 'chef', 'label' => 'Chef de département / Direction'],
        ];

        $users = [
            ['key' => 'agadame', 'initials' => 'JA', 'name' => 'Dr Jean Théophile Agadamè', 'email' => 'j.agadame@eneam.sn', 'role' => 'Administrateur', 'tone' => 'dark', 'attachment' => '—', 'last_login' => '24/11/2026 — 09 h 42', 'active' => true],
            ['key' => 'kone', 'initials' => 'AK', 'name' => 'Aminata Koné', 'email' => 'a.kone@eneam.sn', 'role' => 'Agent de scolarité', 'tone' => 'blue', 'attachment' => '—', 'last_login' => '24/11/2026 — 08 h 57', 'active' => true],
            ['key' => 'diop', 'initials' => 'MD', 'name' => 'Mamadou Diop', 'email' => 'm.diop@eneam.sn', 'role' => 'Chef de département / Direction', 'tone' => 'gold', 'attachment' => 'Département Informatique de gestion', 'last_login' => '23/11/2026 — 16 h 14', 'active' => true],
            ['key' => 'nnesson', 'initials' => 'SN', 'name' => 'Sophie Nénesson', 'email' => 's.nnesson@eneam.sn', 'role' => 'Chef de département / Direction', 'tone' => 'gold', 'attachment' => 'Direction des études', 'last_login' => '23/11/2026 — 14 h 03', 'active' => true],
            ['key' => 'bathily', 'initials' => 'YB', 'name' => 'Yaya Bathily', 'email' => 'y.bathily@eneam.sn', 'role' => 'Agent de scolarité', 'tone' => 'blue', 'attachment' => '—', 'last_login' => '23/11/2026 — 11 h 26', 'active' => true],
            ['key' => 'ly', 'initials' => 'FL', 'name' => 'Fatou Ly', 'email' => 'f.ly@eneam.sn', 'role' => 'Administrateur', 'tone' => 'dark', 'attachment' => '—', 'last_login' => '22/11/2026 — 17 h 38', 'active' => true],
            ['key' => 'sarr', 'initials' => 'MS', 'name' => 'Mariama Sarr', 'email' => 'm.sarr@eneam.sn', 'role' => 'Agent de scolarité', 'tone' => 'blue', 'attachment' => '—', 'last_login' => '12/10/2026 — 10 h 05', 'active' => false],
            ['key' => 'faye', 'initials' => 'OF', 'name' => 'Ousmane Faye', 'email' => 'o.faye@eneam.sn', 'role' => 'Chef de département / Direction', 'tone' => 'gold', 'attachment' => 'Département Banque et finance', 'last_login' => '08/10/2026 — 15 h 20', 'active' => false],
        ];
    @endphp

    <x-admin.page-header title="Utilisateurs" subtitle="Gestion des comptes et des accès à l'espace administrateur." :breadcrumb="['Accueil', 'Administration', 'Utilisateurs']">
        @slot('actions')
            <button class="admin-button" type="button" data-drawer-open data-target="admin-user-create-drawer">
                <x-admin.icon name="plus" /> Nouvel utilisateur
            </button>
        @endslot
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="admin-users-summary-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-users-summary-title">Vue d'ensemble des comptes</h2>
                <p class="admin-section__hint">Situation des accès au 24 novembre 2026.</p>
            </div>
        </div>

        <div class="admin-summary-grid">
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="users" /></span>
                <span>
                    <span class="admin-summary-card__value">24</span>
                    <span class="admin-summary-card__label">Total des comptes</span>
                </span>
            </article>
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="check" /></span>
                <span>
                    <span class="admin-summary-card__value">21</span>
                    <span class="admin-summary-card__label">Actifs</span>
                </span>
            </article>
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="lock" /></span>
                <span>
                    <span class="admin-summary-card__value">3</span>
                    <span class="admin-summary-card__label">Inactifs</span>
                </span>
            </article>
        </div>
    </section>

    <form class="admin-filters" method="GET" action="{{ route('admin.users.index') }}">
        <div class="admin-field admin-field--grow">
            <label class="admin-field__label" for="admin-user-search">Nom ou adresse e-mail</label>
            <input class="admin-input" id="admin-user-search" name="q" type="search" value="{{ old('q', request('q')) }}" placeholder="Rechercher un utilisateur" autocomplete="off">
        </div>

        <div class="admin-field">
            <label class="admin-field__label" for="admin-user-role-filter">Profil</label>
            <select class="admin-select" id="admin-user-role-filter" name="role">
                <option value="">Tous les profils</option>
                @foreach ($roles as $role)
                    <option value="{{ $role['key'] }}"{{ old('role', request('role')) === $role['key'] ? ' selected' : '' }}>{{ $role['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="admin-field">
            <label class="admin-field__label" for="admin-user-status-filter">Statut</label>
            <select class="admin-select" id="admin-user-status-filter" name="statut">
                <option value="">Tous les statuts</option>
                <option value="actif"{{ old('statut', request('statut')) === 'actif' ? ' selected' : '' }}>Actif</option>
                <option value="inactif"{{ old('statut', request('statut')) === 'inactif' ? ' selected' : '' }}>Inactif</option>
            </select>
        </div>

        <button class="admin-button admin-button--small" type="submit"><x-admin.icon name="search" /> Rechercher</button>
        <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.users.index') }}">Réinitialiser</a>
    </form>

    <section class="admin-section" aria-labelledby="admin-users-table-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="admin-users-table-title">Comptes utilisateurs</h2>
                <span class="admin-muted admin-tiny">8 comptes affichés sur 24</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Liste des comptes utilisateurs</caption>
                    <thead>
                        <tr>
                            <th scope="col">Utilisateur</th>
                            <th scope="col">Rôle</th>
                            <th scope="col">Rattachement</th>
                            <th scope="col">Dernière connexion</th>
                            <th scope="col">Statut</th>
                            <th scope="col"><span class="sr-only" hidden>Actions</span><button class="admin-table__filter" type="button" aria-label="Filtrer les utilisateurs"><x-admin.icon name="filter" /></button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr>
                                <td>
                                    <span class="admin-cell-user">
                                        <x-admin.avatar :initials="$user['initials']" size="sm" :label="'Profil de '.$user['name']" />
                                        <span class="admin-cell-user__text">
                                            <span class="admin-table__primary">{{ $user['name'] }}</span>
                                            <span class="admin-table__subtext">{{ $user['email'] }}</span>
                                        </span>
                                    </span>
                                </td>
                                <td><x-admin.status :label="$user['role']" :tone="$user['tone']" /></td>
                                <td class="{{ $user['attachment'] === '—' ? 'admin-muted' : '' }}">{{ $user['attachment'] }}</td>
                                <td class="admin-muted">{{ $user['last_login'] }}</td>
                                <td>
                                    <x-admin.toggle name="active_{{ $user['key'] }}" :checked="$user['active']" :label="'Activer ou désactiver le compte de '.$user['name']" />
                                </td>
                                <td>
                                    <div class="admin-table__actions">
                                        <div class="admin-dropdown">
                                            <button class="admin-table__filter" type="button" data-dropdown aria-controls="admin-user-menu-{{ $index }}" aria-expanded="false" aria-label="Actions pour {{ $user['name'] }}"><x-admin.icon name="more" /></button>
                                            <div class="admin-dropdown__menu" id="admin-user-menu-{{ $index }}" hidden>
                                                {{-- TODO: relier ces actions aux routes de gestion des utilisateurs --}}
                                                <button class="admin-dropdown__item" type="button"><x-admin.icon name="pencil" /> Modifier</button>
                                                <button class="admin-dropdown__item" type="button"><x-admin.icon name="key" /> Réinitialiser le mot de passe</button>
                                                <button class="admin-dropdown__item" type="button"><x-admin.icon name="lock" /> Désactiver</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-pagination">
                <span>1 à 8 sur 24 comptes</span>
                <div class="admin-pagination__list">
                    <a class="admin-pagination__link is-active" href="{{ route('admin.users.index') }}" aria-current="page">1</a>
                    {{-- TODO: remplacer par la pagination réelle côté serveur --}}
                    <a class="admin-pagination__link" href="#">2</a>
                    <a class="admin-pagination__link" href="#">3</a>
                    <a class="admin-pagination__link" href="#">Suivant</a>
                </div>
            </div>
        </div>
    </section>

    {{-- L'overlay est créé automatiquement par public/js/admin.js --}}
    <aside class="admin-drawer" id="admin-user-create-drawer" hidden aria-labelledby="admin-user-drawer-title">
        <div class="admin-drawer__head">
            <div>
                <h2 class="admin-drawer__title" id="admin-user-drawer-title">Nouvel utilisateur</h2>
                <p class="admin-drawer__subtitle">Créer un accès nominatif à l'espace administrateur.</p>
            </div>
            <button class="admin-icon-button admin-icon-button--plain" type="button" data-overlay-close aria-label="Fermer"><x-admin.icon name="close" /></button>
        </div>

        <div class="admin-drawer__body">
            {{-- TODO: relier à la route de création des utilisateurs --}}
            <form id="admin-user-create-form" action="#" method="POST">
                @csrf

                <div class="admin-form-grid">
                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('nom') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-user-nom">Nom et prénoms</label>
                        <input class="admin-input" id="admin-user-nom" name="nom" type="text" value="{{ old('nom') }}" placeholder="Ex. Aminata Koné" autocomplete="name" @error('nom') aria-invalid="true" aria-describedby="admin-user-nom-error" @else aria-invalid="false" @enderror>
                        @error('nom')
                            <p class="admin-field__error" id="admin-user-nom-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('email') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-user-email">E-mail institutionnel</label>
                        <input class="admin-input" id="admin-user-email" name="email" type="email" value="{{ old('email') }}" placeholder="prenom.nom@eneam.sn" autocomplete="email" @error('email') aria-invalid="true" aria-describedby="admin-user-email-error" @else aria-invalid="false" @enderror>
                        @error('email')
                            <p class="admin-field__error" id="admin-user-email-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('role') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-user-role">Profil</label>
                        <select class="admin-select" id="admin-user-role" name="role" @error('role') aria-invalid="true" aria-describedby="admin-user-role-error" @else aria-invalid="false" @enderror>
                            <option value="">Sélectionner un profil</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role['key'] }}"{{ old('role') === $role['key'] ? ' selected' : '' }}>{{ $role['label'] }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="admin-field__error" id="admin-user-role-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('rattachement') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-user-attachment">Rattachement</label>
                        <input class="admin-input" id="admin-user-attachment" name="rattachement" type="text" value="{{ old('rattachement') }}" placeholder="Département ou direction" @error('rattachement') aria-invalid="true" aria-describedby="admin-user-attachment-error" @else aria-invalid="false" @enderror>
                        <span class="admin-table__subtext">Requis uniquement pour le profil Chef de département / Direction.</span>
                        @error('rattachement')
                            <p class="admin-field__error" id="admin-user-attachment-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-notice admin-form-grid__full">
                        <x-admin.icon name="info" />
                        <span>Un mot de passe temporaire sera envoyé à l'adresse institutionnelle indiquée.</span>
                    </div>

                    <div class="admin-field admin-form-grid__full{{ isset($errors) && $errors->has('password') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="admin-user-password">Mot de passe temporaire</label>
                        <input class="admin-input" id="admin-user-password" name="password" type="password" value="{{ old('password') }}" placeholder="Au moins 12 caractères" autocomplete="new-password" @error('password') aria-invalid="true" aria-describedby="admin-user-password-error" @else aria-invalid="false" @enderror>
                        @error('password')
                            <p class="admin-field__error" id="admin-user-password-error"><x-admin.icon name="alert" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <label class="admin-checkbox admin-form-grid__full" for="admin-user-force-change">
                        <input id="admin-user-force-change" name="force_change" type="checkbox" value="1"{{ old('force_change') ? ' checked' : '' }}>
                        Imposer le changement à la première connexion
                    </label>
                </div>
            </form>
        </div>

        <div class="admin-drawer__footer">
            <div class="admin-form-actions">
                <button class="admin-button admin-button--secondary" type="button" data-overlay-close>Annuler</button>
                <button class="admin-button" type="submit" form="admin-user-create-form"><x-admin.icon name="check" /> Créer le compte</button>
            </div>
        </div>
    </aside>
@endsection
