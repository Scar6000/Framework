@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Registre des PV')

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $canManage = in_array($roleKey, ['admin', 'agent'], true);
        $isChef = $roleKey === 'chef';
        $scopeFilieres = $isChef ? config('ui-roles.roles.chef.profile.filieres', ['IG', 'STAT']) : null;
        $filterKeys = ['annee', 'filiere', 'niveau', 'session', 'statut', 'reference'];
        $filters = [];

        foreach ($filterKeys as $filterKey) {
            $filters[$filterKey] = trim((string) old($filterKey, request($filterKey, '')));
        }

        $pvFilterMap = [
            'annee' => 'annee',
            'filiere' => 'filiere',
            'niveau' => 'niveau',
            'session' => 'session',
            'statut' => 'status',
        ];

        $filiereOptions = collect(config('ui-demo.filieres', []))
            ->when($isChef, fn ($items) => $items->whereIn('code', $scopeFilieres))
            ->values();

        $allPvs = collect(config('ui-demo.pvs', []))
            ->when($isChef, fn ($items) => $items->whereIn('filiere', $scopeFilieres))
            ->values();

        $pvs = $allPvs
            ->filter(function (array $pv) use ($filters, $pvFilterMap): bool {
                foreach ($pvFilterMap as $filterKey => $pvKey) {
                    if ($filters[$filterKey] !== '' && ($pv[$pvKey] ?? '') !== $filters[$filterKey]) {
                        return false;
                    }
                }

                if ($filters['reference'] !== '' && mb_stripos($pv['reference'], $filters['reference']) === false) {
                    return false;
                }

                return true;
            })
            ->values()
            ->all();

        $indexRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.pv.index')
            ? route($roleKey.'.pv.index')
            : route('admin.pv.index');
    @endphp

    <x-admin.page-header
        title="Registre des PV"
        subtitle="Consultez, corrigez et archivez les procès-verbaux de délibération enregistrés."
        :breadcrumb="['Accueil', 'PV de délibération', 'Registre des PV']"
    >
        <x-slot:actions>
            @if ($canManage)
                <x-ui.btn :href="route($roleKey.'.pv.create')" variant="primary" icon="plus">Nouveau PV</x-ui.btn>
                <x-ui.btn :href="route($roleKey.'.pv.import')" variant="secondary" icon="upload">Import en masse</x-ui.btn>
            @else
                <x-ui.badge label="Lecture seule · IG et STAT" tone="neutral" />
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="shared-pv-filters-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-pv-filters-title">Recherche et filtres</h2>
                <p class="admin-section__hint">Affinez la liste par année, filière, niveau, session, statut et référence.</p>
            </div>
        </div>

        <form class="admin-filters" method="GET" action="{{ $indexRoute }}" role="search">
            <div class="admin-field">
                <label class="admin-field__label" for="shared-pv-annee">Année académique</label>
                <select class="admin-select" id="shared-pv-annee" name="annee">
                    <option value="">Toutes les années</option>
                    @foreach (config('ui-demo.annees', []) as $annee)
                        <option value="{{ $annee }}" @selected($filters['annee'] === $annee)>{{ $annee }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label class="admin-field__label" for="shared-pv-filiere">Filière</label>
                <select class="admin-select" id="shared-pv-filiere" name="filiere">
                    <option value="">Toutes</option>
                    @foreach ($filiereOptions as $filiere)
                        <option value="{{ $filiere['code'] }}" @selected($filters['filiere'] === $filiere['code'])>
                            {{ $filiere['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label class="admin-field__label" for="shared-pv-niveau">Niveau</label>
                <select class="admin-select" id="shared-pv-niveau" name="niveau">
                    <option value="">Tous</option>
                    @foreach (config('ui-demo.niveaux', []) as $niveau)
                        <option value="{{ $niveau }}" @selected($filters['niveau'] === $niveau)>{{ $niveau }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label class="admin-field__label" for="shared-pv-session">Session</label>
                <select class="admin-select" id="shared-pv-session" name="session">
                    <option value="">Toutes</option>
                    @foreach (config('ui-demo.sessions', []) as $session)
                        <option value="{{ $session }}" @selected($filters['session'] === $session)>{{ $session }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label class="admin-field__label" for="shared-pv-statut">Statut</label>
                <select class="admin-select" id="shared-pv-statut" name="statut">
                    <option value="">Tous</option>
                    <option value="Brouillon" @selected($filters['statut'] === 'Brouillon')>Brouillon</option>
                    <option value="Archivé" @selected($filters['statut'] === 'Archivé')>Archivé</option>
                </select>
            </div>

            <div class="admin-field admin-field--grow">
                <label class="admin-field__label" for="shared-pv-reference">Référence</label>
                <input class="admin-input" id="shared-pv-reference" name="reference" type="search"
                       placeholder="PV-2026-IG-L3-014" value="{{ $filters['reference'] }}">
            </div>

            <button class="admin-button admin-button--small" type="submit">
                <x-admin.icon name="search" />
                Filtrer
            </button>
            <a class="admin-button admin-button--secondary admin-button--small" href="{{ $indexRoute }}">Réinitialiser</a>
        </form>
    </section>

    @include('shared.pv._pv-table', [
        'pvs' => $pvs,
        'roleKey' => $roleKey,
        'indexRoute' => $indexRoute,
        'totalPvs' => $allPvs->count(),
    ])

    @foreach ($pvs as $pvIndex => $pv)
        @include('shared.pv._pv-detail', [
            'pv' => $pv,
            'pvIndex' => $pvIndex,
            'roleKey' => $roleKey,
        ])
    @endforeach

    @foreach ($pvs as $pvIndex => $pv)
        @include('shared.pv._pv-modals', [
            'pv' => $pv,
            'pvIndex' => $pvIndex,
            'roleKey' => $roleKey,
            'indexRoute' => $indexRoute,
        ])
    @endforeach
@endsection

@pushOnce('scripts')
    <script>
        (function () {
            document.addEventListener('click', function (event) {
                var link = event.target.closest('td.col-actions a[href^="#pv-detail-"]');

                if (!link) {
                    return;
                }

                event.preventDefault();
                var trigger = document.querySelector('[data-pv-detail-trigger="' + link.getAttribute('href').slice(1) + '"]');

                if (trigger) {
                    trigger.click();
                }
            }, true);

            var compactQuery = window.matchMedia('(max-width: 767px)');

            function applyCompactTable() {
                document.querySelectorAll('[data-pv-mobile-hide]').forEach(function (node) {
                    if (node.tagName === 'COL') {
                        node.style.width = compactQuery.matches ? '0px' : '';
                    } else {
                        node.hidden = compactQuery.matches;
                    }
                });
            }

            applyCompactTable();

            if (typeof compactQuery.addEventListener === 'function') {
                compactQuery.addEventListener('change', applyCompactTable);
            } else if (typeof compactQuery.addListener === 'function') {
                compactQuery.addListener(applyCompactTable);
            }
        })();
    </script>
@endPushOnce
