<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0B3394">
    <meta name="description" content="Espace de gestion — Gestion et recherche des PV de délibération de l'ENEAM.">
    <title>@yield('title', 'Tableau de bord') — Gestion des PV | ENEAM</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
    @php
        $utilisateur = auth()->user();
        $nomAffiche = trim($utilisateur?->nom_complet ?? 'Utilisateur');
        $roleAffiche = $utilisateur?->role?->label() ?? 'Visiteur';
        $filiereAffichee = $utilisateur?->filiere?->nom;
        $initiales = collect(preg_split('/\s+/', $nomAffiche) ?: [])
            ->filter()
            ->map(fn (string $partie) => mb_strtoupper(mb_substr($partie, 0, 1)))
            ->take(2)
            ->implode('');

        $admin = [
            'name' => $nomAffiche,
            'role' => $roleAffiche,
            'filiere' => $filiereAffichee,
            'initials' => $initiales,
        ];

        $pageTitle = trim($__env->yieldContent('page-title')) ?: 'Administration';
        $pageSubtitle = trim($__env->yieldContent('page-subtitle')) ?: "Gestion des procès-verbaux de délibération";
        $hasAside = \Illuminate\Support\Facades\View::hasSection('aside');
    @endphp

    <div class="admin-shell">
        <div class="admin-frame{{ $hasAside ? ' admin-frame--with-aside' : '' }}">
            @include('admin.partials.sidebar', ['admin' => $admin])

            <div class="admin-sidebar-backdrop" data-sidebar-backdrop hidden></div>

            <div class="admin-main-column">
                @include('admin.partials.topbar', [
                    'admin' => $admin,
                    'showAccount' => ! $hasAside,
                    'pageTitle' => $pageTitle,
                    'pageSubtitle' => $pageSubtitle,
                ])

                <main class="admin-content" id="admin-content" tabindex="-1">
                    @if (session('success'))
                        <div class="admin-notice" role="status">
                            <x-admin.icon name="check" />
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="admin-notice admin-notice--error" role="alert">
                            <x-admin.icon name="alert" />
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('lien_partage'))
                        <div class="admin-notice" role="status">
                            <x-admin.icon name="link" />
                            <span>Lien de partage : {{ session('lien_partage') }}</span>
                        </div>
                    @endif

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
