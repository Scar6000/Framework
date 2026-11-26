<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0B3394">
    <meta name="description" content="Espace administrateur — Gestion et recherche des PV de délibération de l'ENEAM.">
    <title>@yield('title', 'Tableau de bord') — Gestion des PV | ENEAM</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
    @php
        $admin = [
            'name' => 'Dr Jean Théophile Agadamè',
            'role' => 'Administrateur',
            'initials' => 'JA',
        ];

        $hasAside = \Illuminate\Support\Facades\View::hasSection('aside');
    @endphp

    <div class="admin-shell">
        <div class="admin-frame{{ $hasAside ? ' admin-frame--with-aside' : '' }}">
            @include('admin.partials.sidebar', ['admin' => $admin])

            <div class="admin-sidebar-backdrop" data-sidebar-backdrop hidden></div>

            <div class="admin-main-column">
                @include('admin.partials.topbar', ['admin' => $admin, 'showAccount' => ! $hasAside])

                <main class="admin-content" id="admin-content" tabindex="-1">
                    @yield('content')
                </main>
            </div>

            @if ($hasAside)
                <aside class="admin-aside" aria-label="Informations complémentaires">
                    @yield('aside')
                </aside>
            @endif
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
