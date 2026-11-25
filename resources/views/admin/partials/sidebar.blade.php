@php
    $profile = $profile ?? config('ui-roles.roles.admin.profile');
    $navigation = $navigation ?? config('ui-roles.roles.admin.sidebar');
    $roleKey = $roleKey ?? 'admin';
    $profileRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.profil') ? route($roleKey.'.profil') : '#';
@endphp

<aside class="admin-sidebar" id="admin-sidebar" aria-label="Navigation principale">
    <div class="admin-sidebar__inner">
        <x-ui.brand class="admin-brand" />

        <nav class="admin-nav" aria-label="Sections de l'espace {{ $profile['role'] }}">
            <span class="admin-nav__label">{{ $profile['role'] }}</span>

            @foreach ($navigation as $item)
                @php
                    $isActive = collect($item['patterns'])->contains(fn (string $pattern) => request()->routeIs($pattern));
                    $itemUrl = route($item['route']);
                @endphp

                <a class="admin-nav__link{{ $isActive ? ' is-active' : '' }}"
                   href="{{ $itemUrl }}"
                   @if ($isActive) aria-current="page" @endif>
                    <span class="admin-nav__icon"><x-admin.icon name="{{ $item['icon'] }}" /></span>
                    <span class="admin-nav__text">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="admin-sidebar__footer">
            <a class="admin-logout" href="{{ $profileRoute }}">
                <x-admin.icon name="user" />
                <span>Mon profil</span>
            </a>
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
