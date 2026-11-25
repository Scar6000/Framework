@php
    $showAccount = $showAccount ?? true;
    $profile = $profile ?? config('ui-roles.roles.admin.profile');
    $roleKey = $roleKey ?? 'admin';
    $roleConfig = $roleConfig ?? config('ui-roles.roles.admin');
    $searchRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.recherche.index')
        ? route($roleKey.'.recherche.index')
        : route('admin.recherche.index');
    $profileRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.profil') ? route($roleKey.'.profil') : '#';
    $addMenu = $roleConfig['addMenu'] ?? [];
@endphp

<header class="admin-topbar">
    <x-ui.brand class="admin-topbar__brand" />

    <button class="admin-icon-button admin-topbar__menu" type="button" data-sidebar-open aria-controls="admin-sidebar" aria-expanded="false" aria-label="Ouvrir la navigation">
        <x-admin.icon name="menu" />
    </button>

    {{-- TODO: brancher la recherche globale sur le contrôleur de recherche --}}
    <form class="admin-global-search" method="GET" action="{{ $searchRoute }}" role="search">
        <label class="sr-only" for="admin-global-search" hidden>Recherche globale</label>
        <input class="admin-global-search__input" id="admin-global-search" name="q" type="search" placeholder="Rechercher un étudiant (nom ou matricule), un PV…" value="{{ request('q') }}">
        <button class="admin-global-search__button" type="submit" aria-label="Lancer la recherche">
            <x-admin.icon name="search" />
        </button>
    </form>

    <span class="admin-topbar__spacer"></span>

    <div class="admin-topbar__actions">
        @if (! empty($roleConfig['restricted']))
            <x-ui.badge label="Consultation restreinte" tone="neutral" class="admin-topbar__restricted" />
        @endif

        @if (count($addMenu) > 0)
            <div class="admin-dropdown">
                <button class="admin-button" type="button" data-dropdown aria-controls="admin-add-menu" aria-expanded="false">
                    <x-admin.icon name="plus" />
                    Ajouter
                    <x-admin.icon name="chevron-down" />
                </button>

                <div class="admin-dropdown__menu" id="admin-add-menu" hidden>
                    @foreach ($addMenu as $item)
                        @php
                            $itemUrl = route($item['route'], $item['query'] ?? []);
                        @endphp
                        <a class="admin-dropdown__item" href="{{ $itemUrl }}">
                            <x-admin.icon name="{{ $item['icon'] }}" /> {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($showAccount)
            @if ($roleKey === 'admin')
                <a class="admin-icon-button" href="{{ route('admin.parametres.index') }}" aria-label="Paramètres">
                    <x-admin.icon name="settings" />
                </a>

                @include('admin.partials.notifications', [
                    'menuId' => 'admin-notifications-topbar-menu',
                    'triggerId' => 'admin-notifications-topbar-trigger',
                ])
            @endif

            <div class="admin-dropdown">
                <button class="admin-icon-button admin-profile-trigger" type="button" data-dropdown aria-controls="admin-profile-menu" aria-expanded="false" aria-label="Menu du profil de {{ $profile['name'] }}">
                    <x-admin.avatar :initials="$profile['initials']" size="sm" />
                </button>
                <div class="admin-dropdown__menu" id="admin-profile-menu" hidden>
                    <a class="admin-dropdown__item" href="{{ $profileRoute }}">
                        <x-admin.icon name="user" /> Mon profil
                    </a>
                    <div class="admin-dropdown__divider"></div>
                    {{-- TODO: brancher la route POST /logout de l'authentification Laravel --}}
                    <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : '#' }}">
                        @csrf
                        <button class="admin-dropdown__item" type="submit">
                            <x-admin.icon name="logout" /> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</header>
