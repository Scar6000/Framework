@extends('layouts.admin')

@section('title', 'Exports et partages')

@section('content')
    @php
        $exportCards = [
            [
                'title' => 'Liste des PV',
                'text' => 'Exporte la liste complète des procès-verbaux avec références, promotions, sessions et statuts de validation.',
                'icon' => 'pv',
            ],
            [
                'title' => 'Résultats par promotion',
                'text' => 'Regroupe les résultats par promotion, par semestre et par session pour l’année académique sélectionnée.',
                'icon' => 'graduation',
            ],
            [
                'title' => 'Statistiques comparatives',
                'text' => 'Compare les taux de réussite et les volumes de PV sur trois années académiques consécutives.',
                'icon' => 'chart',
            ],
        ];

        $exportHistory = [
            ['date' => '24/11/2026 — 09 h 42', 'initials' => 'JA', 'name' => 'Dr Jean Théophile Agadamè', 'role' => 'Administrateur', 'type' => 'Liste des PV', 'format' => 'Excel', 'icon' => 'file', 'scope' => 'Année 2025-2026 · Toutes promotions', 'status' => 'Prêt', 'tone' => 'blue'],
            ['date' => '23/11/2026 — 16 h 05', 'initials' => 'AK', 'name' => 'Aminata Koné', 'role' => 'Agent de scolarité', 'type' => 'Résultats par promotion', 'format' => 'PDF', 'icon' => 'pdf', 'scope' => 'Banque et finance · L2 · S1', 'status' => 'Prêt', 'tone' => 'blue'],
            ['date' => '22/11/2026 — 17 h 38', 'initials' => 'FL', 'name' => 'Fatou Ly', 'role' => 'Administrateur', 'type' => 'Statistiques comparatives', 'format' => 'Excel', 'icon' => 'file', 'scope' => '2023-2024 à 2025-2026', 'status' => 'Archivé', 'tone' => 'slate'],
            ['date' => '20/11/2026 — 11 h 20', 'initials' => 'MD', 'name' => 'Mamadou Diop', 'role' => 'Chef de département', 'type' => 'Liste des PV', 'format' => 'PDF', 'icon' => 'pdf', 'scope' => 'Comptabilité et audit · L3', 'status' => 'Prêt', 'tone' => 'blue'],
            ['date' => '19/11/2026 — 08 h 47', 'initials' => 'YB', 'name' => 'Yaya Bathily', 'role' => 'Agent de scolarité', 'type' => 'Résultats par promotion', 'format' => 'Excel', 'icon' => 'file', 'scope' => 'Marketing · L3 · Session normale', 'status' => 'Archivé', 'tone' => 'slate'],
            ['date' => '18/11/2026 — 14 h 12', 'initials' => 'SN', 'name' => 'Sophie Nénesson', 'role' => 'Direction', 'type' => 'Statistiques comparatives', 'format' => 'PDF', 'icon' => 'pdf', 'scope' => 'Toutes filières · 2025-2026', 'status' => 'Prêt', 'tone' => 'blue'],
        ];

        $sharedLinks = [
            ['excerpt' => 'PV-2026-IG-L3-S2-014', 'excerptMeta' => 'Informatique de gestion · L3', 'recipient' => 'Conseil de département', 'recipientMeta' => 'conseil.ig@eneam.bj', 'createdBy' => 'Dr Jean Théophile Agadamè', 'createdAt' => '22/11/2026 — 10 h 18', 'expiresAt' => '06/12/2026', 'status' => 'Actif', 'tone' => 'blue'],
            ['excerpt' => 'Résultats · Banque et finance L2', 'excerptMeta' => 'Session normale · Semestre 1', 'recipient' => 'Secrétariat de scolarité', 'recipientMeta' => 'scolarite@eneam.bj', 'createdBy' => 'Aminata Koné', 'createdAt' => '21/11/2026 — 15 h 02', 'expiresAt' => '05/12/2026', 'status' => 'Actif', 'tone' => 'blue'],
            ['excerpt' => 'Statistiques comparatives 2025-2026', 'excerptMeta' => 'Toutes filières', 'recipient' => 'Direction des études', 'recipientMeta' => 'direction.etudes@eneam.bj', 'createdBy' => 'Fatou Ly', 'createdAt' => '12/11/2026 — 09 h 30', 'expiresAt' => '26/11/2026', 'status' => 'Expiré', 'tone' => 'slate'],
            ['excerpt' => 'PV-2026-CCA-L3-S1-021', 'excerptMeta' => 'Comptabilité et audit · L3', 'recipient' => 'Bibliothèque centrale', 'recipientMeta' => 'bibliotheque@eneam.bj', 'createdBy' => 'Mamadou Diop', 'createdAt' => '08/11/2026 — 11 h 45', 'expiresAt' => '22/11/2026', 'status' => 'Révoqué', 'tone' => 'red'],
            ['excerpt' => 'PV-2026-MK-L3-S1-012', 'excerptMeta' => 'Marketing · L3', 'recipient' => 'Service des examens', 'recipientMeta' => 'examens@eneam.bj', 'createdBy' => 'Dr Jean Théophile Agadamè', 'createdAt' => '05/11/2026 — 16 h 10', 'expiresAt' => '19/12/2026', 'status' => 'Actif', 'tone' => 'blue'],
        ];
    @endphp

    <x-admin.page-header
        title="Exports et partages"
        subtitle="Générez des extraits, suivez les téléchargements et gérez les liens partagés."
        :breadcrumb="['Accueil', 'Administration', 'Exports et partages']"
    />

    <section class="admin-section" aria-label="Exports et liens partagés">
        <div class="admin-card admin-card--flush">
            <div class="admin-tabs" role="tablist" aria-label="Exports et liens partagés">
                <button
                    class="admin-tabs__button"
                    id="admin-exports-tab-exports"
                    type="button"
                    role="tab"
                    aria-selected="true"
                    aria-controls="admin-exports-panel-exports"
                    data-tab="exports"
                >Exports</button>
                <button
                    class="admin-tabs__button"
                    id="admin-exports-tab-links"
                    type="button"
                    role="tab"
                    aria-selected="false"
                    aria-controls="admin-exports-panel-links"
                    data-tab="links"
                    tabindex="-1"
                >Liens partagés</button>
            </div>

            <div id="admin-exports-panel-exports" role="tabpanel" aria-labelledby="admin-exports-tab-exports" data-tab-panel="exports">
                <div class="admin-card__body">
                    <div class="admin-export-grid">
                        @foreach ($exportCards as $card)
                            <article class="admin-export-card">
                                <span class="admin-export-card__icon"><x-admin.icon name="{{ $card['icon'] }}" /></span>
                                <h2 class="admin-export-card__title">{{ $card['title'] }}</h2>
                                <p class="admin-export-card__text">{{ $card['text'] }}</p>
                                <div class="admin-export-card__actions">
                                    <button class="admin-button admin-button--small admin-button--secondary" type="button">
                                        <x-admin.icon name="file" /> Excel
                                    </button>
                                    <button class="admin-button admin-button--small" type="button">
                                        <x-admin.icon name="pdf" /> PDF
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="admin-card__head">
                    <h2 class="admin-card__title" id="admin-exports-history-title">Historique des exports</h2>
                    <button class="admin-button admin-button--small admin-button--secondary" type="button">
                        <x-admin.icon name="filter" /> Filtrer
                    </button>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table" aria-labelledby="admin-exports-history-title">
                        <caption class="sr-only" hidden>6 exports récents</caption>
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Utilisateur</th>
                                <th scope="col">Type</th>
                                <th scope="col">Format</th>
                                <th scope="col">Périmètre</th>
                                <th scope="col">Statut</th>
                                <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($exportHistory as $export)
                                <tr>
                                    <td class="admin-muted">{{ $export['date'] }}</td>
                                    <td>
                                        <span class="admin-cell-user">
                                            <x-admin.avatar :initials="$export['initials']" size="sm" tone="soft" />
                                            <span class="admin-cell-user__text">
                                                <span class="admin-table__primary">{{ $export['name'] }}</span>
                                                <span class="admin-table__subtext">{{ $export['role'] }}</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td>{{ $export['type'] }}</td>
                                    <td>
                                        <span class="admin-export-card__actions">
                                            <x-admin.icon name="{{ $export['icon'] }}" /> {{ $export['format'] }}
                                        </span>
                                    </td>
                                    <td>{{ $export['scope'] }}</td>
                                    <td><x-admin.status :label="$export['status']" :tone="$export['tone']" /></td>
                                    <td>
                                        <div class="admin-table__actions">
                                            <a class="admin-table__link" href="#">
                                                <x-admin.icon name="download" /> Télécharger
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="admin-pagination">
                    <span>6 exports affichés sur 34</span>
                    <div class="admin-pagination__list" role="navigation" aria-label="Pages de l’historique des exports">
                        <a class="admin-pagination__link is-active" href="#" aria-current="page">1</a>
                        <a class="admin-pagination__link" href="#">2</a>
                        <a class="admin-pagination__link" href="#">3</a>
                        <a class="admin-pagination__link" href="#" aria-label="Page suivante"><x-admin.icon name="chevron-right" /></a>
                    </div>
                </div>
            </div>

            <div id="admin-exports-panel-links" role="tabpanel" aria-labelledby="admin-exports-tab-links" data-tab-panel="links" hidden>
                <div class="admin-card__body">
                    <div class="admin-notice">
                        <x-admin.icon name="info" />
                        <span>Un lien révoqué devient immédiatement inaccessible. Cette action est conservée dans le journal d’audit.</span>
                    </div>
                </div>

                <div class="admin-card__head">
                    <h2 class="admin-card__title" id="admin-exports-links-title">Liens partagés</h2>
                    <span class="admin-muted admin-tiny">3 liens actifs sur 5</span>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table" aria-labelledby="admin-exports-links-title">
                        <caption class="sr-only" hidden>Liens de partage d’extraits</caption>
                        <thead>
                            <tr>
                                <th scope="col">Extrait partagé</th>
                                <th scope="col">Destinataire</th>
                                <th scope="col">Créé par</th>
                                <th scope="col">Créé le</th>
                                <th scope="col">Expire le</th>
                                <th scope="col">Statut</th>
                                <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sharedLinks as $index => $link)
                                <tr>
                                    <td>
                                        <span class="admin-table__primary">{{ $link['excerpt'] }}</span>
                                        <span class="admin-table__subtext">{{ $link['excerptMeta'] }}</span>
                                    </td>
                                    <td>
                                        <span class="admin-table__primary">{{ $link['recipient'] }}</span>
                                        <span class="admin-table__subtext">{{ $link['recipientMeta'] }}</span>
                                    </td>
                                    <td>{{ $link['createdBy'] }}</td>
                                    <td class="admin-muted">{{ $link['createdAt'] }}</td>
                                    <td class="admin-muted">{{ $link['expiresAt'] }}</td>
                                    <td><x-admin.status :label="$link['status']" :tone="$link['tone']" /></td>
                                    <td>
                                        <div class="admin-table__actions">
                                            @if ($link['status'] === 'Actif')
                                                <button
                                                    class="admin-button admin-button--small admin-button--secondary"
                                                    type="button"
                                                    data-modal-open
                                                    data-target="admin-revoke-modal-{{ $index }}"
                                                    aria-controls="admin-revoke-modal-{{ $index }}"
                                                >
                                                    <x-admin.icon name="lock" /> Révoquer
                                                </button>
                                            @else
                                                <span class="admin-muted admin-tiny">Aucune action</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="admin-pagination">
                    <span>5 liens affichés sur 5</span>
                    <span>Dernière vérification : 24/11/2026 — 10 h 05</span>
                </div>

                @foreach ($sharedLinks as $index => $link)
                    @if ($link['status'] === 'Actif')
                        <div class="admin-modal" id="admin-revoke-modal-{{ $index }}" role="dialog" aria-modal="true" aria-labelledby="admin-revoke-title-{{ $index }}" hidden>
                            <form method="POST" action="#">
                                @csrf
                                @method('PUT')

                                <div class="admin-modal__head">
                                    <h3 class="admin-modal__title" id="admin-revoke-title-{{ $index }}">Révoquer le lien partagé</h3>
                                </div>

                                <div class="admin-modal__body">
                                    <p class="admin-muted">
                                        Destinataire : {{ $link['recipient'] }} — extrait : {{ $link['excerpt'] }}.
                                    </p>

                                    <div class="admin-field @error('motif') admin-field--invalid @enderror">
                                        <label class="admin-field__label" for="admin-revoke-reason-{{ $index }}">Motif de la révocation (facultatif)</label>
                                        <textarea
                                            class="admin-textarea"
                                            id="admin-revoke-reason-{{ $index }}"
                                            name="motif"
                                            rows="3"
                                            placeholder="Exemple : diffusion limitée à la session de novembre."
                                            @error('motif') aria-invalid="true" aria-describedby="admin-revoke-reason-{{ $index }}-error" @enderror
                                        >{{ old('motif') }}</textarea>
                                        @error('motif')
                                            <p class="admin-field__error" id="admin-revoke-reason-{{ $index }}-error">
                                                <x-admin.icon name="alert" /> {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="admin-modal__footer">
                                    <button class="admin-button admin-button--small admin-button--secondary" type="button" data-overlay-close>Annuler</button>
                                    <button class="admin-button admin-button--small" type="submit">Confirmer la révocation</button>
                                </div>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
