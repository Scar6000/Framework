@extends('layouts.admin')

@section('title', 'Fiche étudiant : Adjoa K. Koffi')

@section('content')
    @php
        $parcours = [
            ['semestre' => 'L1 · S1', 'annee' => '2023-2024', 'session' => 'Normale', 'moyenne' => '11,86', 'credits' => 28, 'decision' => 'Admis', 'tone' => 'blue', 'gold' => false],
            ['semestre' => 'L1 · S2', 'annee' => '2023-2024', 'session' => 'Normale', 'moyenne' => '12,42', 'credits' => 30, 'decision' => 'Admis', 'tone' => 'blue', 'gold' => false],
            ['semestre' => 'L2 · S1', 'annee' => '2024-2025', 'session' => 'Rattrapage', 'moyenne' => '10,15', 'credits' => 26, 'decision' => 'Rattrapage', 'tone' => 'lightgold', 'gold' => true],
            ['semestre' => 'L2 · S2', 'annee' => '2024-2025', 'session' => 'Normale', 'moyenne' => '13,04', 'credits' => 30, 'decision' => 'Admis', 'tone' => 'blue', 'gold' => false],
            ['semestre' => 'L3 · S2', 'annee' => '2025-2026', 'session' => 'Normale', 'moyenne' => '14,32', 'credits' => 36, 'decision' => 'Admis', 'tone' => 'blue', 'gold' => true],
        ];

        $studentPv = [
            ['reference' => 'PV-2026-IG-L3-S2-014', 'promotion' => 'Informatique de gestion, L3'],
            ['reference' => 'PV-2026-IG-L3-S1-009', 'promotion' => 'Informatique de gestion, L3'],
            ['reference' => 'PV-2025-IG-L2-S2-017', 'promotion' => 'Informatique de gestion, L2'],
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
                <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.recherche.index') }}">
                    <x-admin.icon name="chevron-left" /> Retour à la recherche
                </a>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <h3 class="admin-card__title" id="admin-etudiant-synthese-card-title">Synthèse</h3>
                <span class="admin-tiny admin-muted">Dernière session : L3 S2</span>
            </div>
            <div class="admin-card__body">
                <dl class="admin-info-list">
                    <div class="admin-info-row">
                        <dt>Crédits validés</dt>
                        <dd>150 / 180</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Moyenne générale</dt>
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
                <span class="admin-tiny admin-muted">5 semestres enregistrés</span>
            </div>
            <div class="admin-card__body">
                <div class="admin-timeline">
                    @foreach ($parcours as $etape)
                        <div class="admin-timeline__item {{ $etape['gold'] ? 'admin-timeline__item--gold' : '' }}">
                            <div class="admin-timeline__title">
                                {{ $etape['semestre'] }}
                                <x-admin.status :label="$etape['decision']" :tone="$etape['tone']" />
                            </div>
                            <div class="admin-timeline__meta">
                                Année {{ $etape['annee'] }} · Semestre {{ substr($etape['semestre'], -2) }} · Session {{ $etape['session'] }} · Moyenne {{ $etape['moyenne'] }} / 20 · {{ $etape['credits'] }} crédits validés
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="admin-etudiant-pv-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="admin-etudiant-pv-title">PV où figure l'étudiant</h2>
                <span class="admin-tiny admin-muted">3 procès-verbaux</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Procès-verbaux mentionnant Adjoa K. Koffi</caption>
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Promotion</th>
                            <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentPv as $pv)
                            <tr>
                                <td><a class="admin-table__link" href="{{ route('admin.pv.index') }}">{{ $pv['reference'] }}</a></td>
                                <td>{{ $pv['promotion'] }}</td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.pv.index') }}">
                                            <x-admin.icon name="eye" /> Ouvrir le scan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
