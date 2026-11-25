@extends('layouts.admin')

@section('title', 'Fiche étudiant : Adjoa K. Koffi')

@section('content')
    @php
        $parcours = [
            ['annee' => '2023-2024', 'filiere' => 'Informatique de gestion', 'niveau' => 'L1', 'session' => 'Normale', 'moyenne' => '11,86', 'credits' => 28, 'decision' => 'Admis', 'tone' => 'blue', 'reference' => 'PV-2024-IG-L1-011', 'pages' => 4],
            ['annee' => '2024-2025', 'filiere' => 'Informatique de gestion', 'niveau' => 'L2', 'session' => 'Normale', 'moyenne' => '10,15', 'credits' => 26, 'decision' => 'Rattrapage', 'tone' => 'neutral', 'reference' => 'PV-2025-IG-L2-008', 'pages' => 3],
            ['annee' => '2024-2025', 'filiere' => 'Informatique de gestion', 'niveau' => 'L2', 'session' => 'Rattrapage', 'moyenne' => '13,04', 'credits' => 30, 'decision' => 'Admis', 'tone' => 'blue', 'reference' => 'PV-2025-IG-L2-018', 'pages' => 4],
            ['annee' => '2025-2026', 'filiere' => 'Informatique de gestion', 'niveau' => 'L2', 'session' => 'Normale', 'moyenne' => '12,10', 'credits' => 30, 'decision' => 'Ajourné', 'tone' => 'gold', 'redoublement' => true, 'reference' => 'PV-2026-IG-L2-017', 'pages' => 4],
            ['annee' => '2025-2026', 'filiere' => 'Informatique de gestion', 'niveau' => 'L3', 'session' => 'Normale', 'moyenne' => '14,32', 'credits' => 36, 'decision' => 'Admis', 'tone' => 'blue', 'reference' => 'PV-2026-IG-L3-014', 'pages' => 5],
        ];

        $studentPv = [
            ['reference' => 'PV-2026-IG-L3-014', 'promotion' => 'Informatique de gestion, L3', 'pages' => 5],
            ['reference' => 'PV-2026-IG-L2-017', 'promotion' => 'Informatique de gestion, L2', 'pages' => 4],
            ['reference' => 'PV-2025-IG-L2-008', 'promotion' => 'Informatique de gestion, L2', 'pages' => 3],
        ];
    @endphp

    <header class="admin-student-head">
        <x-admin.avatar initials="AK" size="lg" tone="gold" label="Initiales d'Adjoa K. Koffi" />
        <div>
            <h1 class="admin-student-head__name">Adjoa K. Koffi</h1>
            <p class="admin-student-head__meta">Matricule ENE-2023-1048 · Informatique de gestion, L3</p>
        </div>
    </header>

    <section class="admin-section" aria-labelledby="admin-etudiant-synthese-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-etudiant-synthese-title">Vue d'ensemble</h2>
                <p class="admin-section__hint">Situation académique au 24 novembre 2026.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.btn variant="secondary" href="{{ route('admin.recherche.index') }}" icon="chevron-left">Retour à la recherche</x-ui.btn>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <h3 class="admin-card__title" id="admin-etudiant-synthese-card-title">Synthèse</h3>
                <span class="admin-tiny admin-muted">Année 2025-2026 · Session normale</span>
            </div>
            <div class="admin-card__body">
                <dl class="admin-info-list">
                    <div class="admin-info-row">
                        <dt>Crédits obtenus</dt>
                        <dd>150 / 180</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Moyenne annuelle</dt>
                        <dd>13,18 / 20</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Décision finale</dt>
                        <dd><x-admin.status label="Admis" tone="blue" /></dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="admin-etudiant-parcours-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="admin-etudiant-parcours-title">Parcours</h2>
                <span class="admin-tiny admin-muted">5 parcours annuels enregistrés</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Parcours annuel d'Adjoa K. Koffi</caption>
                    <colgroup>
                        <col style="width: 120px;" />
                        <col style="width: 200px;" />
                        <col style="width: 70px;" />
                        <col style="width: 100px;" />
                        <col style="width: 120px;" />
                        <col style="width: 110px;" />
                        <col style="width: 190px;" />
                        <col class="col-ref" style="width: 178px;" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">Année académique</th>
                            <th scope="col">Filière</th>
                            <th scope="col">Niveau</th>
                            <th scope="col">Session</th>
                            <th scope="col">Moyenne annuelle</th>
                            <th scope="col">Crédits obtenus</th>
                            <th scope="col">Décision</th>
                            <th class="col-ref" scope="col">PV</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parcours as $index => $etape)
                            <tr>
                                <td>{{ $etape['annee'] }}</td>
                                <td title="{{ $etape['filiere'] }}">{{ $etape['filiere'] }}</td>
                                <td>{{ $etape['niveau'] }}</td>
                                <td>{{ $etape['session'] }}</td>
                                <td>{{ $etape['moyenne'] }} / 20</td>
                                <td>{{ $etape['credits'] }}</td>
                                <td>
                                    <x-admin.status :label="$etape['decision']" :tone="$etape['tone']" />
                                    @if (! empty($etape['redoublement']))
                                        <x-ui.badge label="Redoublement" tone="gold" />
                                    @endif
                                </td>
                                <td class="col-ref">
                                    <button class="admin-table__link" type="button" data-drawer-open="#parcours-pv-{{ $index }}">
                                        <x-ui.pv-reference :reference="$etape['reference']" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @foreach ($parcours as $index => $etape)
        <x-ui.drawer id="parcours-pv-{{ $index }}" title="Procès-verbal" :subtitle="$etape['annee'].' · '.$etape['filiere']" :wide="true">
            @slot('header')
                <x-ui.pv-reference :reference="$etape['reference']" />
                <x-admin.status :label="$etape['decision']" :tone="$etape['tone']" />
            @endslot

            <x-ui.viewer :reference="$etape['reference']" :pages="$etape['pages'] ?? 5" can-download />

            @slot('footer')
                <x-ui.btn variant="secondary" type="button" data-drawer-close>Fermer</x-ui.btn>
                <x-ui.btn variant="primary" href="{{ route('admin.pv.index') }}" icon="download">Ouvrir le registre des PV</x-ui.btn>
            @endslot
        </x-ui.drawer>
    @endforeach

    <section class="admin-section" aria-labelledby="admin-etudiant-pv-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="admin-etudiant-pv-title">PV où figure l'étudiant</h2>
                <span class="admin-tiny admin-muted">3 procès-verbaux</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Procès-verbaux mentionnant Adjoa K. Koffi</caption>
                    <colgroup>
                        <col class="col-ref" style="width: 178px;" />
                        <col style="width: 320px;" />
                        <col class="col-actions" style="width: 120px;" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="col-ref" scope="col">Référence</th>
                            <th scope="col">Promotion</th>
                            <th class="col-actions" scope="col"><span class="sr-only" hidden>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentPv as $index => $pv)
                            <tr>
                                <td class="col-ref">
                                    <button class="admin-table__link" type="button" data-drawer-open="#fiche-pv-{{ $index }}">
                                        <x-ui.pv-reference :reference="$pv['reference']" />
                                    </button>
                                </td>
                                <td title="{{ $pv['promotion'] }}">{{ $pv['promotion'] }}</td>
                                <td class="col-actions">
                                    <x-ui.table-actions :reference="$pv['reference']" :preview="'#'" :actions="[]" empty-message="Consultez le procès-verbal" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @foreach ($studentPv as $index => $pv)
        <x-ui.drawer id="fiche-pv-{{ $index }}" title="Procès-verbal" :subtitle="$pv['promotion']" :wide="true">
            @slot('header')
                <x-ui.pv-reference :reference="$pv['reference']" />
            @endslot

            <x-ui.viewer :reference="$pv['reference']" :pages="$pv['pages'] ?? 5" can-download />

            @slot('footer')
                <x-ui.btn variant="secondary" type="button" data-drawer-close>Fermer</x-ui.btn>
                <x-ui.btn variant="primary" href="{{ route('admin.pv.index') }}" icon="download">Ouvrir le registre des PV</x-ui.btn>
            @endslot
        </x-ui.drawer>
    @endforeach
@endsection
