@extends('layouts.admin', ['roleKey' => $roleKey ?? 'agent'])

@section('title', 'Tableau de bord')

@section('content')
    @php
        $roleKey = $roleKey ?? 'agent';
        $profile = config('ui-roles.roles.agent.profile');

        $filieres = collect(config('ui-demo.filieres', []))->keyBy('code');

        $filiereLabel = function (string $code) use ($filieres): string {
            $filiere = $filieres->get($code);

            return is_array($filiere) ? $filiere['label'] : $code;
        };

        $pvs = collect(config('ui-demo.pvs', []))
            ->where('enregistre_par', $profile['name'])
            ->sortByDesc('date_enregistrement')
            ->values();

        $drafts = $pvs->where('status', 'Brouillon')->values();
        $lastRecords = $pvs->take(5);

        $activeYear = old('annee', request('annee', '2025-2026'));
        $activeSession = old('session', request('session', 'Normale'));

        $statCards = [
            ['label' => 'PV archivés', 'value' => '96', 'meta' => 'Année 2025-2026', 'icon' => 'archive', 'tone' => 'blue'],
            ['label' => 'Brouillons à compléter', 'value' => '6', 'meta' => 'Action requise', 'icon' => 'clock', 'tone' => 'gold'],
            ['label' => 'PV à numériser', 'value' => '14', 'meta' => 'Plan 2025-2026', 'icon' => 'file', 'tone' => 'blue'],
            ['label' => 'Étudiants référencés', 'value' => '6 428', 'meta' => 'Toutes filières', 'icon' => 'users', 'tone' => 'blue'],
        ];

        $drawerIdFor = function (string $reference): string {
            return 'apercu-'.preg_replace('/[^A-Za-z0-9_-]/', '', $reference);
        };
    @endphp

    <nav class="admin-breadcrumb" aria-label="Fil d'Ariane">
        <span>{{ $profile['role'] }}</span>
        <x-admin.icon name="chevron-right" />
        <span>Tableau de bord</span>
    </nav>

    <section class="admin-banner" aria-labelledby="agent-banner-title">
        <span class="admin-banner__surface" aria-hidden="true"></span>

        <div class="admin-banner__content">
            <span class="admin-banner__eyebrow"><x-admin.icon name="pv" /> Espace agent de scolarité</span>
            <h1 class="admin-banner__title" id="agent-banner-title">Bonjour, Mme Dossou</h1>
            <p class="admin-banner__text">Il reste 14 PV à numériser pour l'année 2025-2026 et 6 brouillons à compléter.</p>
            <x-ui.btn
                :href="route('agent.pv.index', ['statut' => 'brouillon'])"
                variant="compact"
                class="admin-button--gold"
                icon="arrow-right"
                label="Voir les brouillons"
            />
        </div>

        <div class="admin-banner__brand">
            <x-ui.brand />
        </div>
    </section>

    <section class="admin-section" aria-labelledby="agent-indicators-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="agent-indicators-title">Indicateurs clés</h2>
                <p class="admin-section__hint">Situation de l'enregistrement des PV pour l'année 2025-2026.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.badge label="Espace agent" tone="dark" />
            </div>
        </div>

        <div class="admin-stat-grid">
            @foreach ($statCards as $card)
                <article class="admin-stat-card">
                    <span class="admin-stat-card__tile{{ $card['tone'] === 'gold' ? ' admin-stat-card__tile--gold' : '' }}">
                        <x-admin.icon name="{{ $card['icon'] }}" />
                    </span>
                    <div>
                        <span class="admin-stat-card__value">{{ $card['value'] }}</span>
                        <span class="admin-stat-card__label">{{ $card['label'] }}</span>
                    </div>
                    <span class="admin-stat-card__meta">{{ $card['meta'] }}</span>
                </article>
            @endforeach
        </div>
    </section>

    <section class="admin-section" aria-labelledby="agent-records-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="agent-records-title">Mes derniers enregistrements</h2>
                <p class="admin-section__hint">PV enregistrés par {{ $profile['name'] }}, toutes sessions confondues.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.btn :href="route('agent.pv.index')" variant="compact" class="admin-button--secondary" icon="arrow-right" label="Ouvrir le registre" />
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h3 class="admin-card__title">Enregistrements récents</h3>
                <span class="admin-tiny admin-muted">{{ $pvs->count() }} PV</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Derniers enregistrements de procès-verbaux</caption>
                    <colgroup>
                        <col class="col-ref">
                        <col>
                        <col class="col-date">
                        <col>
                        <col>
                        <col class="col-date">
                        <col class="col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Filière et niveau</th>
                            <th scope="col">Année académique</th>
                            <th scope="col">Session</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Date d'enregistrement</th>
                            <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lastRecords as $pv)
                            @php
                                $drawerId = $drawerIdFor($pv['reference']);
                            @endphp
                            <tr class="is-hoverable-row">
                                <td><x-ui.pv-reference :reference="$pv['reference']" /></td>
                                <td>
                                    <span class="admin-table__primary">{{ $filiereLabel($pv['filiere']) }}</span>
                                    <span class="admin-table__subtext">{{ $pv['niveau'] }}</span>
                                </td>
                                <td class="admin-muted">{{ $pv['annee'] }}</td>
                                <td>{{ $pv['session'] }}</td>
                                <td>
                                    <x-ui.badge :label="$pv['status']" :tone="$pv['status'] === 'Archivé' ? 'dark' : 'gold'" />
                                </td>
                                <td class="admin-muted">{{ \Illuminate\Support\Carbon::parse($pv['date_enregistrement'])->format('d/m/Y') }}</td>
                                <td>
                                    <div data-drawer-open data-target="{{ $drawerId }}">
                                        <x-ui.table-actions :reference="$pv['reference']" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @foreach ($lastRecords as $pv)
        <x-ui.drawer
            :id="$drawerIdFor($pv['reference'])"
            title="Aperçu du procès-verbal"
            :subtitle="$pv['reference'].' — '.$filiereLabel($pv['filiere'])"
            :wide="true"
        >
            <x-ui.viewer
                :reference="$pv['reference']"
                :pages="$pv['pages'] ?? 4"
                :can-download="true"
            />
        </x-ui.drawer>
    @endforeach
@endsection

@section('aside')
    <div class="admin-aside__profile">
        <div class="admin-aside__profile-actions">
            <a class="admin-icon-button admin-icon-button--plain" href="{{ route('agent.profil') }}" aria-label="Mon profil">
                <x-admin.icon name="user" />
            </a>
        </div>
        <div class="admin-aside__profile-text">
            <strong class="admin-aside__profile-name">{{ $profile['name'] }}</strong>
            <span class="admin-aside__profile-role">{{ $profile['role'] }}</span>
        </div>
        <x-admin.avatar :initials="$profile['initials']" size="sm" label="Profil de {{ $profile['name'] }}" />
    </div>

    <section class="admin-widget" aria-labelledby="agent-year-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="agent-year-widget-title">Année académique</h2>
        </div>
        <div class="admin-year-picker">
            <label class="sr-only" for="agent-annee" hidden>Année académique</label>
            <select class="admin-select" id="agent-annee" name="annee" aria-label="Année académique">
                @foreach (config('ui-demo.annees', []) as $annee)
                    <option value="{{ $annee }}" @selected($annee === $activeYear)>{{ $annee }}</option>
                @endforeach
            </select>
        </div>
        <div role="group" aria-label="Sessions de l'année {{ $activeYear }}">
            @foreach (config('ui-demo.sessions', []) as $session)
                @php
                    $isActive = $session === $activeSession;
                @endphp
                <x-ui.btn
                    variant="compact"
                    class="{{ $isActive ? '' : 'admin-button--secondary' }}"
                    :aria-pressed="$isActive ? 'true' : 'false'"
                    :aria-current="$isActive ? 'true' : null"
                >
                    <x-ui.badge :label="$session" :tone="$isActive ? 'dark' : 'neutral'" />
                </x-ui.btn>
            @endforeach
        </div>
    </section>

    <section class="admin-widget" aria-labelledby="agent-scan-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="agent-scan-widget-title">PV à numériser</h2>
            <span class="admin-tiny admin-muted">14</span>
        </div>

        @foreach ($drafts as $pv)
            <div class="admin-missing-card">
                <span>
                    <span class="admin-missing-card__title">{{ $filiereLabel($pv['filiere']) }}, {{ $pv['niveau'] }}</span>
                    <span class="admin-missing-card__meta">{{ $pv['annee'] }} · Session {{ $pv['session'] }}</span>
                </span>
                <x-ui.btn
                    :href="route('agent.pv.create')"
                    variant="compact"
                    icon="plus"
                    label="Enregistrer"
                    aria-label="Enregistrer le PV {{ $filiereLabel($pv['filiere']) }} {{ $pv['niveau'] }}"
                />
            </div>
        @endforeach
    </section>

    <section class="admin-widget" aria-labelledby="agent-draft-widget-title">
        <div class="admin-widget__head">
            <h2 class="admin-widget__title" id="agent-draft-widget-title">Brouillons à compléter</h2>
            <span class="admin-tiny admin-muted">6</span>
        </div>

        @foreach ($drafts as $pv)
            <div class="admin-missing-card">
                <span>
                    <span class="admin-missing-card__title"><x-ui.pv-reference :reference="$pv['reference']" /></span>
                    <span class="admin-missing-card__meta">{{ $filiereLabel($pv['filiere']) }} · {{ $pv['niveau'] }}</span>
                </span>
                <x-ui.btn
                    :href="route('agent.pv.edit', ['reference' => $pv['reference']])"
                    variant="compact"
                    icon="pencil"
                    label="Compléter"
                    aria-label="Compléter le PV {{ $pv['reference'] }}"
                />
            </div>
        @endforeach
    </section>
@endsection
