@extends('layouts.admin')

@section('title', 'Fiche étudiant : '.$etudiant->nom.' '.$etudiant->prenoms)

@section('page-title', $etudiant->nom.' '.$etudiant->prenoms)
@section('page-subtitle', 'Parcours construit à partir des PV officiels')

@section('content')
    @php
        $initiales = mb_strtoupper(mb_substr($etudiant->nom, 0, 1).mb_substr($etudiant->prenoms, 0, 1));
        $libelleDecision = fn (string $decision): string => \App\Enums\Decision::tryFrom($decision)?->label() ?? $decision;
        $tonDecision = fn (string $decision): string => match ($decision) {
            'admis' => 'blue',
            'rattrapage' => 'gold',
            default => 'red',
        };
        $derniereMoyenne = $parcours->last()['moyenne_generale'] ?? null;
    @endphp

    <x-admin.page-header title="Fiche étudiant" subtitle="Consultation du parcours académique officiel." :breadcrumb="['Gestion des PV', 'Étudiants', $etudiant->matricule]">
        @slot('actions')
            <a class="admin-button admin-button--small" href="{{ route('etudiants.index') }}">
                <x-admin.icon name="chevron-left" /> Retour
            </a>
        @endslot
    </x-admin.page-header>

    <header class="admin-student-head">
        <x-admin.avatar :initials="$initiales" size="lg" tone="gold" label="Initiales de {{ $etudiant->nom }} {{ $etudiant->prenoms }}" />
        <div>
            <h1 class="admin-student-head__name">{{ $etudiant->nom }} {{ $etudiant->prenoms }}</h1>
            <p class="admin-student-head__meta">Matricule {{ $etudiant->matricule }} · {{ $parcours->last()['filiere'] ?? 'Filière non renseignée' }}</p>
        </div>
    </header>

    @if ($redoublements->isNotEmpty())
        <div class="admin-notice admin-notice--error" role="status">
            <x-admin.icon name="alert" />
            <span>Redoublement détecté pour le(s) niveau(x) : {{ $redoublements->implode(', ') }}.</span>
        </div>
    @endif

    <section class="admin-section" aria-labelledby="etudiant-synthese-title">
        <div class="admin-card">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="etudiant-synthese-title">Synthèse</h2>
                <span class="admin-tiny admin-muted">{{ $parcours->count() }} session(s) officielle(s)</span>
            </div>
            <div class="admin-card__body">
                <dl class="admin-info-list">
                    <div class="admin-info-row">
                        <dt>Dernière décision</dt>
                        <dd>
                            @if ($derniere_decision)
                                <x-admin.status :label="$libelleDecision($derniere_decision)" :tone="$tonDecision($derniere_decision)" />
                            @else
                                <span class="admin-muted">—</span>
                            @endif
                        </dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Dernière moyenne générale</dt>
                        <dd>{{ $derniereMoyenne !== null ? number_format((float) $derniereMoyenne, 2, ',', ' ').' / 20' : '—' }}</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Redoublements</dt>
                        <dd>{{ $redoublements->isNotEmpty() ? $redoublements->implode(', ') : 'Aucun' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="etudiant-parcours-title">
        <div class="admin-card">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="etudiant-parcours-title">Parcours officiel</h2>
                <span class="admin-tiny admin-muted">Sessions normales et rattrapages validés</span>
            </div>
            <div class="admin-card__body">
                <div class="admin-timeline">
                    @forelse ($parcours as $etape)
                        <div class="admin-timeline__item {{ $etape['decision'] === 'rattrapage' ? 'admin-timeline__item--gold' : '' }}">
                            <div class="admin-timeline__title">
                                {{ $etape['niveau'] }} · {{ $etape['semestre'] }}
                                <x-admin.status :label="$libelleDecision($etape['decision'])" :tone="$tonDecision($etape['decision'])" />
                            </div>
                            <div class="admin-timeline__meta">
                                Année {{ $etape['annee'] }} · Session {{ $etape['session'] }} · Moyenne {{ $etape['moyenne_generale'] !== null ? number_format((float) $etape['moyenne_generale'], 2, ',', ' ') : '—' }} / 20 · {{ $etape['filiere'] }}
                            </div>
                        </div>
                    @empty
                        <span class="admin-empty">Aucun résultat officiel enregistré pour cet étudiant.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="etudiant-pv-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="etudiant-pv-title">PV où figure l'étudiant</h2>
                <span class="admin-tiny admin-muted">{{ $parcours->count() }} procès-verbal(aux)</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Procès-verbaux officiels mentionnant {{ $etudiant->nom }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Année</th>
                            <th scope="col">Niveau</th>
                            <th scope="col">Session</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($parcours as $etape)
                            <tr>
                                <td><a class="admin-table__link" href="{{ route('pv.show', $etape['pv_id']) }}">{{ $etape['reference'] }}</a></td>
                                <td>{{ $etape['annee'] }}</td>
                                <td>{{ $etape['niveau'] }} · {{ $etape['semestre'] }}</td>
                                <td>{{ $etape['session'] }}</td>
                                <td>
                                    <a class="admin-table__filter" href="{{ route('pv.show', $etape['pv_id']) }}" aria-label="Consulter {{ $etape['reference'] }}">
                                        <x-admin.icon name="eye" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><span class="admin-empty">Aucun PV officiel à afficher.</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
