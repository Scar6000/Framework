@extends('layouts.admin')

@section('title', 'PV '.$pv->reference)

@section('page-title', 'PV '.$pv->reference)
@section('page-subtitle', 'Détail du procès-verbal et résultats')

@section('content')
    @php
        $tonStatut = fn (string $statut): string => match ($statut) {
            'valide' => 'blue',
            'archive' => 'dark',
            'soumis' => 'gold',
            default => 'slate',
        };
        $tonDecision = fn (string $decision): string => match ($decision) {
            'admis' => 'blue',
            'rattrapage' => 'gold',
            'ajourne', 'exclu', 'absent' => 'red',
            default => 'slate',
        };
        $promotion = $pv->promotion;
    @endphp

    <x-admin.page-header title="PV {{ $pv->reference }}" subtitle="Consultation sécurisée du procès-verbal." :breadcrumb="['Gestion des PV', 'Registre', $pv->reference]">
        @slot('actions')
            <a class="admin-button admin-button--small" href="{{ route('pv.index') }}">
                <x-admin.icon name="chevron-left" /> Retour
            </a>
            @if ($permissions['telecharger'] ?? false)
                <a class="admin-button admin-button--small" href="{{ route('pv.telecharger', $pv) }}">
                    <x-admin.icon name="download" /> Télécharger
                </a>
            @endif
        @endslot
    </x-admin.page-header>

    @if ($pv->motif_rejet)
        <div class="admin-notice admin-notice--error" role="alert">
            <x-admin.icon name="alert" />
            <span>Motif de renvoi : {{ $pv->motif_rejet }}</span>
        </div>
    @endif

    <section class="admin-section" aria-labelledby="pv-infos-title">
        <div class="admin-card">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="pv-infos-title">Informations du procès-verbal</h2>
                <x-admin.status :label="$pv->statut?->label()" :tone="$tonStatut($pv->statut?->value ?? 'brouillon')" />
            </div>

            <div class="admin-card__body">
                <dl class="admin-info-list">
                    <div>
                        <dt>Référence</dt>
                        <dd>{{ $pv->reference }}</dd>
                    </div>
                    <div>
                        <dt>Filière</dt>
                        <dd>{{ $promotion?->filiere?->nom ?? '—' }} @if ($promotion?->filiere?->code)({{ $promotion->filiere->code }})@endif</dd>
                    </div>
                    <div>
                        <dt>Promotion</dt>
                        <dd>{{ $promotion?->semestre?->niveau?->code ?? '—' }} {{ $promotion?->semestre?->code }}</dd>
                    </div>
                    <div>
                        <dt>Année académique</dt>
                        <dd>{{ $promotion?->annee?->libelle ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt>Session</dt>
                        <dd>{{ $pv->session?->label() }}</dd>
                    </div>
                    <div>
                        <dt>Date de délibération</dt>
                        <dd>{{ $pv->date_deliberation?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt>Président du jury</dt>
                        <dd>{{ $pv->president_jury ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt>Membres du jury</dt>
                        <dd>{{ is_array($pv->membres_jury) ? implode(', ', $pv->membres_jury) : ($pv->membres_jury ?: '—') }}</dd>
                    </div>
                    <div>
                        <dt>Créé par</dt>
                        <dd>{{ $pv->createur?->nom_complet ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt>Validé par</dt>
                        <dd>{{ $pv->validateur?->nom_complet ?? '—' }} @if ($pv->validated_at)({{ $pv->validated_at->format('d/m/Y H:i') }})@endif</dd>
                    </div>
                </dl>

                <div class="admin-table__actions">
                    <a class="admin-button admin-button--small" href="{{ route('pv.scan', $pv) }}">
                        <x-admin.icon name="file" /> Consulter le scan
                    </a>

                    @if ($permissions['valider'] ?? false)
                        <form method="POST" action="{{ route('pv.valider', $pv) }}">
                            @csrf
                            <button class="admin-button admin-button--gold admin-button--small" type="submit">
                                <x-admin.icon name="check" /> Valider le PV
                            </button>
                        </form>
                    @endif

                    @if ($permissions['partager'] ?? false)
                        <form method="POST" action="{{ route('pv.partage', $pv) }}" class="admin-table__actions">
                            @csrf
                            <label class="sr-only" for="duree-partage">Durée du lien en minutes</label>
                            <input class="admin-input" id="duree-partage" name="duree_minutes" type="number" min="5" max="1440" value="60">
                            <button class="admin-button admin-button--small" type="submit">
                                <x-admin.icon name="link" /> Créer un lien signé
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($permissions['rejeter'] ?? false)
        <section class="admin-section" aria-labelledby="pv-rejet-title">
            <div class="admin-card">
                <div class="admin-card__head">
                    <h2 class="admin-card__title" id="pv-rejet-title">Renvoyer le PV en brouillon</h2>
                </div>
                <div class="admin-card__body">
                    <form method="POST" action="{{ route('pv.rejeter', $pv) }}" class="admin-form-grid">
                        @csrf
                        <label class="admin-field admin-field--grow">
                            <span class="admin-field__label">Motif du renvoi</span>
                            <textarea class="admin-input" name="motif" rows="3" minlength="5" maxlength="1000" required placeholder="Précisez les corrections attendues…">{{ old('motif') }}</textarea>
                        </label>
                        <button class="admin-button admin-button--small" type="submit">
                            <x-admin.icon name="alert" /> Renvoyer
                        </button>
                    </form>
                </div>
            </div>
        </section>
    @endif

    <section class="admin-section" aria-labelledby="pv-resultats-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="pv-resultats-title">Résultats</h2>
                <span class="admin-muted admin-tiny">{{ $lignes->total() }} étudiant(s)</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Résultats du PV {{ $pv->reference }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">Matricule</th>
                            <th scope="col">Étudiant</th>
                            <th scope="col">Moyenne</th>
                            <th scope="col">Rang</th>
                            <th scope="col">Mention</th>
                            <th scope="col">Décision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lignes as $ligne)
                            <tr>
                                <td>{{ $ligne->etudiant?->matricule ?? '—' }}</td>
                                <td>
                                    <a class="admin-table__link" href="{{ route('etudiants.show', $ligne->etudiant) }}">
                                        {{ $ligne->etudiant?->nom }} {{ $ligne->etudiant?->prenoms }}
                                    </a>
                                </td>
                                <td>{{ $ligne->moyenne_generale !== null ? number_format((float) $ligne->moyenne_generale, 2, ',', ' ') : '—' }}</td>
                                <td>{{ $ligne->rang ?? '—' }}</td>
                                <td>{{ $ligne->mention ?: '—' }}</td>
                                <td><x-admin.status :label="$ligne->decision?->label()" :tone="$tonDecision($ligne->decision?->value ?? '')" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"><span class="admin-empty">Aucun résultat enregistré pour ce PV.</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-pagination">
            {{ $lignes->links() }}
        </div>
    </section>
@endsection
