@php
    $showAccount = $showAccount ?? true;
@endphp

<header class="admin-topbar">
    <button class="admin-icon-button admin-topbar__menu" type="button" data-sidebar-open aria-controls="admin-sidebar" aria-expanded="false" aria-label="Ouvrir la navigation">
        <x-admin.icon name="menu" />
    </button>

    {{-- TODO: brancher la recherche globale sur le contrôleur de recherche --}}
    <form class="admin-global-search" method="GET" action="{{ route('admin.recherche.index') }}" role="search">
        <label class="sr-only" for="admin-global-search" hidden>Recherche globale</label>
        <input class="admin-global-search__input" id="admin-global-search" name="q" type="search" placeholder="Rechercher un étudiant (nom ou matricule), un PV…" value="{{ request('q') }}">
        <button class="admin-global-search__button" type="submit" aria-label="Lancer la recherche">
            <x-admin.icon name="search" />
        </button>
    </form>

    <span class="admin-topbar__spacer"></span>

    <div class="admin-topbar__actions">
        <div class="admin-dropdown">
            <button class="admin-button" type="button" data-dropdown aria-controls="admin-add-menu" aria-expanded="false">
                <x-admin.icon name="plus" />
                Ajouter
                <x-admin.icon name="chevron-down" />
            </button>

            <div class="admin-dropdown__menu" id="admin-add-menu" hidden>
                <a class="admin-dropdown__item" href="{{ route('admin.users.index', ['creer' => 1]) }}">
                    <x-admin.icon name="users" /> Nouvel utilisateur
                </a>
                <a class="admin-dropdown__item" href="{{ route('admin.referentiels.index', ['onglet' => 'annees']) }}">
                    <x-admin.icon name="calendar" /> Nouvelle année académique
                </a>
                <a class="admin-dropdown__item" href="{{ route('admin.referentiels.index', ['onglet' => 'filieres']) }}">
                    <x-admin.icon name="graduation" /> Nouvelle filière
                </a>
            </div>
        </div>

        @if ($showAccount)
            <a class="admin-icon-button" href="{{ route('admin.parametres.index') }}" aria-label="Paramètres">
                <x-admin.icon name="settings" />
            </a>

            @include('admin.partials.notifications', [
                'menuId' => 'admin-notifications-topbar-menu',
                'triggerId' => 'admin-notifications-topbar-trigger',
            ])

            <a class="admin-cell-user" href="{{ route('admin.parametres.index') }}" aria-label="Profil de {{ $admin['name'] }}">
                <x-admin.avatar :initials="$admin['initials']" size="sm" />
                <span class="admin-topbar__profile-name">{{ $admin['name'] }}</span>
            </a>
        @endif
    </div>
</header>
