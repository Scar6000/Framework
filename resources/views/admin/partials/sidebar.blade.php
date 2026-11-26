<aside class="admin-sidebar" id="admin-sidebar" aria-label="Navigation principale">
    <div class="admin-sidebar__inner">
        <div class="admin-brand">
            <span class="admin-brand__badge">
                <img class="admin-brand__logo" src="{{ asset('images/logo-eneam.png') }}" alt="Logo ENEAM" width="36" height="36">
            </span>
            <span class="admin-brand__text">
                <span class="admin-brand__name">ENEAM</span>
                <span class="admin-brand__tagline">Gestion des PV</span>
            </span>
            <button class="admin-icon-button admin-sidebar__close" type="button" data-sidebar-close aria-label="Fermer la navigation">
                <x-admin.icon name="close" />
            </button>
        </div>

        @php
            $navItems = [
                ['label' => 'Tableau de bord', 'route' => 'admin.dashboard', 'patterns' => ['admin.dashboard'], 'icon' => 'dashboard'],
                ['label' => 'Utilisateurs', 'route' => 'admin.users.index', 'patterns' => ['admin.users.*'], 'icon' => 'users'],
                ['label' => 'Rôles et permissions', 'route' => 'admin.roles.index', 'patterns' => ['admin.roles.*'], 'icon' => 'roles'],
                ['label' => 'Référentiels', 'route' => 'admin.referentiels.index', 'patterns' => ['admin.referentiels.*'], 'icon' => 'referentiels'],
                ['label' => 'Plan de numérisation', 'route' => 'admin.plan.index', 'patterns' => ['admin.plan.*'], 'icon' => 'plan'],
                ['label' => 'Registre des PV', 'route' => 'admin.pv.index', 'patterns' => ['admin.pv.*'], 'icon' => 'pv'],
                ['label' => 'Recherche', 'route' => 'admin.recherche.index', 'patterns' => ['admin.recherche.*', 'admin.etudiants.*'], 'icon' => 'search'],
                ['label' => "Journal d'audit", 'route' => 'admin.journal.index', 'patterns' => ['admin.journal.*'], 'icon' => 'journal'],
                ['label' => 'Exports et partages', 'route' => 'admin.exports.index', 'patterns' => ['admin.exports.*'], 'icon' => 'exports'],
            ];
        @endphp

        <nav class="admin-nav" aria-label="Sections de l'administration">
            <span class="admin-nav__label">Administration</span>

            @foreach ($navItems as $item)
                @php
                    $isActive = collect($item['patterns'])->contains(fn (string $pattern) => request()->routeIs($pattern));
                @endphp

                <a class="admin-nav__link{{ $isActive ? ' is-active' : '' }}"
                   href="{{ route($item['route']) }}"
                   @if ($isActive) aria-current="page" @endif>
                    <span class="admin-nav__icon"><x-admin.icon name="{{ $item['icon'] }}" /></span>
                    <span class="admin-nav__text">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="admin-sidebar__footer">
            {{-- TODO: brancher la route POST /logout de l'authentification Laravel --}}
            <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : '#' }}">
                @csrf
                <button class="admin-logout" type="submit">
                    <x-admin.icon name="logout" />
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </div>
</aside>
