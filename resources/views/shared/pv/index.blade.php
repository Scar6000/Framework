@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Registre des PV')

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $canManage = in_array($roleKey, ['admin', 'agent'], true);
        $isChef = $roleKey === 'chef';
        $scopeFilieres = $scopeFilieres ?? null;
        $pvs = collect($pvs ?? []);
        $filters = [
            'annee' => trim((string) request('annee', '')),
            'filiere' => trim((string) request('filiere', '')),
            'niveau' => trim((string) request('niveau', '')),
            'session' => trim((string) request('session', '')),
            'statut' => trim((string) request('statut', '')),
            'reference' => trim((string) request('reference', '')),
        ];
        $annees = $annees ?? [];
        $niveaux = $niveaux ?? [];
        $sessions = $sessions ?? ['Normale', 'Rattrapage'];
        $filiereOptions = collect($filieres ?? [])
            ->when($isChef && is_array($scopeFilieres), fn ($items) => $items->whereIn('code', $scopeFilieres))
            ->values();
        $indexRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.pv.index')
            ? route($roleKey.'.pv.index')
            : route('admin.pv.index');
        $drafts = $pvs->where('status', 'Brouillon')->count();
        $archives = $pvs->where('status', 'Archivé')->count();
        $scopeLabel = is_array($scopeFilieres) && $scopeFilieres !== []
            ? implode(', ', $scopeFilieres)
            : 'votre département';
    @endphp

    <x-admin.page-header
        title="Registre des PV"
        subtitle="Consultez et gérez les procès-verbaux annuels de délibération enregistrés dans l'application."
        :breadcrumb="['Accueil', 'PV de délibération', 'Registre des PV']"
    >
        <x-slot:actions>
            @if ($canManage)
                <x-ui.btn variant="primary" icon="plus" data-modal-open data-target="#pv-choice-modal">Nouveau PV</x-ui.btn>
                <x-ui.btn :href="route($roleKey.'.pv.import')" variant="secondary" icon="upload">Import en masse</x-ui.btn>
            @else
                <x-ui.badge label="Lecture seule · {{ $scopeLabel }}" tone="neutral" />
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    @if (session('success'))
        <div class="ui-notice ui-notice--success" role="status">
            <x-admin.icon name="check" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="ui-notice" role="alert">
            <x-admin.icon name="alert" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

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
                    @foreach ($annees as $annee)
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
                    @foreach ($niveaux as $niveau)
                        <option value="{{ $niveau }}" @selected($filters['niveau'] === $niveau)>{{ $niveau }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label class="admin-field__label" for="shared-pv-session">Session</label>
                <select class="admin-select" id="shared-pv-session" name="session">
                    <option value="">Toutes</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session }}" @selected(strtolower($filters['session']) === strtolower($session))>{{ $session }}</option>
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

    <section class="admin-section" aria-labelledby="shared-pv-summary-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-pv-summary-title">Aperçu du registre</h2>
                <p class="admin-section__hint">Volumes du périmètre accessible à votre profil.</p>
            </div>
        </div>

        <div class="admin-summary-grid">
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="pv" /></span>
                <div>
                    <span class="admin-summary-card__value">{{ $pvs->count() }}</span>
                    <span class="admin-summary-card__label">PV accessibles</span>
                </div>
            </article>
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="clock" /></span>
                <div>
                    <span class="admin-summary-card__value">{{ $drafts }}</span>
                    <span class="admin-summary-card__label">En brouillon</span>
                </div>
            </article>
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="archive" /></span>
                <div>
                    <span class="admin-summary-card__value">{{ $archives }}</span>
                    <span class="admin-summary-card__label">Archivés</span>
                </div>
            </article>
        </div>
    </section>

    @include('shared.pv._pv-table', [
        'pvs' => $pvs->all(),
        'roleKey' => $roleKey,
        'indexRoute' => $indexRoute,
        'totalPvs' => $totalPvs ?? $pvs->count(),
        'filieres' => $filiereOptions->all(),
    ])

    @foreach ($pvs as $pvIndex => $pv)
        @include('shared.pv._pv-detail', [
            'pv' => $pv,
            'pvIndex' => $pvIndex,
            'roleKey' => $roleKey,
            'filieres' => $filiereOptions->all(),
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
