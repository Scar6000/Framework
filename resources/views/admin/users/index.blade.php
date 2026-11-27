@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    @php
        $roles = [
            ['key' => 'administrateur', 'label' => 'Administrateur'],
            ['key' => 'agent', 'label' => 'Agent de scolarité'],
            ['key' => 'chef', 'label' => 'Chef de département'],
        ];

        $users = [
            ['key' => 'agadame', 'initials' => 'JA', 'name' => 'Dr Jean Théophile Agadamè', 'email' => 'j.agadame@eneam.sn', 'role' => 'Administrateur', 'tone' => 'dark', 'attachment' => '—', 'last_login' => '24/11/2026 — 09 h 42', 'active' => true],
            ['key' => 'kone', 'initials' => 'AK', 'name' => 'Aminata Koné', 'email' => 'a.kone@eneam.sn', 'role' => 'Agent de scolarité', 'tone' => 'blue', 'attachment' => '—', 'last_login' => '24/11/2026 — 08 h 57', 'active' => true],
            ['key' => 'diop', 'initials' => 'MD', 'name' => 'Mamadou Diop', 'email' => 'm.diop@eneam.sn', 'role' => 'Chef de département', 'tone' => 'gold', 'attachment' => 'Département Informatique de gestion', 'last_login' => '23/11/2026 — 16 h 14', 'active' => true],
            ['key' => 'nnesson', 'initials' => 'SN', 'name' => 'Sophie Nénesson', 'email' => 's.nnesson@eneam.sn', 'role' => 'Chef de département', 'tone' => 'gold', 'attachment' => 'Département Statistique', 'last_login' => '23/11/2026 — 14 h 03', 'active' => true],
            ['key' => 'bathily', 'initials' => 'YB', 'name' => 'Yaya Bathily', 'email' => 'y.bathily@eneam.sn', 'role' => 'Agent de scolarité', 'tone' => 'blue', 'attachment' => '—', 'last_login' => '23/11/2026 — 11 h 26', 'active' => true],
            ['key' => 'ly', 'initials' => 'FL', 'name' => 'Fatou Ly', 'email' => 'f.ly@eneam.sn', 'role' => 'Administrateur', 'tone' => 'dark', 'attachment' => '—', 'last_login' => '22/11/2026 — 17 h 38', 'active' => true],
            ['key' => 'sarr', 'initials' => 'MS', 'name' => 'Mariama Sarr', 'email' => 'm.sarr@eneam.sn', 'role' => 'Agent de scolarité', 'tone' => 'blue', 'attachment' => '—', 'last_login' => '12/10/2026 — 10 h 05', 'active' => false],
            ['key' => 'faye', 'initials' => 'OF', 'name' => 'Ousmane Faye', 'email' => 'o.faye@eneam.sn', 'role' => 'Chef de département', 'tone' => 'gold', 'attachment' => 'Département Banque et finance', 'last_login' => '08/10/2026 — 15 h 20', 'active' => false],
        ];
    @endphp

    <x-admin.page-header title="Utilisateurs" subtitle="Gestion des comptes et des accès à l'espace administrateur." :breadcrumb="['Accueil', 'Administration', 'Utilisateurs']">
        @slot('actions')
            <x-ui.btn variant="primary" icon="plus" data-drawer-open="#admin-user-create-drawer">Nouvel utilisateur</x-ui.btn>
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

        <x-ui.btn variant="primary" type="submit" icon="search">Rechercher</x-ui.btn>
        <x-ui.btn variant="secondary" :href="route('admin.users.index')">Réinitialiser</x-ui.btn>
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
                    <colgroup>
                        <col class="col-ref">
                        <col width="16%">
                        <col width="22%">
                        <col width="20%">
                        <col width="10%">
                        <col class="col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col" class="col-ref">Utilisateur</th>
                            <th scope="col">Rôle</th>
                            <th scope="col">Rattachement</th>
                            <th scope="col">Dernière connexion</th>
                            <th scope="col">Statut</th>
                            <th scope="col" class="col-actions"><span class="sr-only" hidden>Actions</span><button class="admin-table__filter" type="button" aria-label="Filtrer les utilisateurs"><x-admin.icon name="filter" /></button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                            @php
                                $rowActions = [
                                    ['label' => 'Modifier', 'icon' => 'pencil', 'url' => '#'],
                                    ['label' => 'Réinitialiser le mot de passe', 'icon' => 'key', 'url' => '#'],
                                    ['label' => 'Désactiver', 'icon' => 'lock', 'url' => '#'],
                                ];
                            @endphp
                            <tr>
                                <td class="col-ref">
                                    <span class="admin-cell-user">
                                        <x-admin.avatar :initials="$user['initials']" size="sm" :label="'Profil de '.$user['name']" />
                                        <span class="admin-cell-user__text">
                                            <span class="admin-table__primary" title="{{ $user['name'] }}">{{ $user['name'] }}</span>
                                            <span class="admin-table__subtext" title="{{ $user['email'] }}">{{ $user['email'] }}</span>
                                        </span>
                                    </span>
                                </td>
                                <td><x-admin.status :label="$user['role']" :tone="$user['tone']" /></td>
                                <td class="{{ $user['attachment'] === '—' ? 'admin-muted' : '' }}" title="{{ $user['attachment'] }}">{{ $user['attachment'] }}</td>
                                <td class="admin-muted">{{ $user['last_login'] }}</td>
                                <td>
                                    <x-admin.toggle name="active_{{ $user['key'] }}" :checked="$user['active']" :label="'Activer ou désactiver le compte de '.$user['name']" />
                                </td>
                                <td class="col-actions">
                                    {{-- TODO: relier ces actions aux routes de gestion des utilisateurs --}}
                                    <x-ui.table-actions :reference="$user['name']" :preview="route('admin.users.index')" :actions="$rowActions" />
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

    <x-ui.drawer id="admin-user-create-drawer" title="Nouvel utilisateur" subtitle="Créer un accès nominatif à l'espace administrateur.">
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
                        <label class="admin-field__label" for="admin-user-attachment">Département</label>
                        <input class="admin-input" id="admin-user-attachment" name="rattachement" type="text" value="{{ old('rattachement') }}" placeholder="Ex. Département Informatique" @required(old('role') === 'chef') @error('rattachement') aria-invalid="true" aria-describedby="admin-user-attachment-error" @else aria-invalid="false" @enderror>
                        <span class="admin-table__subtext">Requis pour le profil Chef de département.</span>
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

            <x-slot:footer>
                <div class="admin-form-actions">
                    <x-ui.btn variant="secondary">Annuler</x-ui.btn>
                    <x-ui.btn variant="primary" type="submit" form="admin-user-create-form" icon="check">Créer le compte</x-ui.btn>
                </div>
            </x-slot:footer>
    </x-ui.drawer>
@endsection
