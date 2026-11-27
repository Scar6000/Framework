@extends('layouts.admin')

@section('title', 'Registre des PV')

@section('content')
    @php
        $pvs = [
            [
                'reference' => 'PV-2026-IG-L3-014',
                'promotion' => 'Informatique de gestion, L3',
                'session' => 'Normale',
                'date' => '20/11/2026',
                'president' => 'M. Moussa Diallo',
                'jury' => 'Mme Awa Traoré, M. Koffi Brou, Mme Nadia Sarr',
                'status' => 'Brouillon',
                'tone' => 'slate',
                'size' => '2,4 Mo',
                'recordedBy' => 'Aminata Koné',
                'pages' => 4,
            ],
            [
                'reference' => 'PV-2026-BF-L2-008',
                'promotion' => 'Banque et finance, L2',
                'session' => 'Normale',
                'date' => '19/11/2026',
                'president' => 'Mme Awa Traoré',
                'jury' => 'M. Moussa Diallo, Mme Nadia Sarr, M. Yaya Bathily',
                'status' => 'Archivé',
                'tone' => 'dark',
                'size' => '1,8 Mo',
                'recordedBy' => 'Mamadou Diop',
                'pages' => 3,
            ],
            [
                'reference' => 'PV-2026-CCA-L3-021',
                'promotion' => 'Comptabilité contrôle et audit, L3',
                'session' => 'Rattrapage',
                'date' => '18/11/2026',
                'president' => 'M. Koffi Brou',
                'jury' => 'Mme Awa Traoré, M. Moussa Diallo, Mme Fatou Ly',
                'status' => 'Brouillon',
                'tone' => 'slate',
                'size' => '2,1 Mo',
                'recordedBy' => 'Sophie Nénesson',
                'pages' => 4,
            ],
            [
                'reference' => 'PV-2026-GRH-L1-006',
                'promotion' => 'Gestion des ressources humaines, L1',
                'session' => 'Normale',
                'date' => '17/11/2026',
                'president' => 'Mme Nadia Sarr',
                'jury' => 'M. Koffi Brou, Mme Awa Traoré, M. Mamadou Diop',
                'status' => 'Archivé',
                'tone' => 'dark',
                'size' => '3,0 Mo',
                'recordedBy' => 'Yaya Bathily',
                'pages' => 5,
            ],
            [
                'reference' => 'PV-2026-MK-L2-012',
                'promotion' => 'Marketing, L2',
                'session' => 'Normale',
                'date' => '16/11/2026',
                'president' => 'M. Moussa Diallo',
                'jury' => 'Mme Nadia Sarr, M. Koffi Brou, Mme Awa Traoré',
                'status' => 'Brouillon',
                'tone' => 'slate',
                'size' => '2,2 Mo',
                'recordedBy' => 'Fatou Ly',
                'pages' => 4,
            ],
            [
                'reference' => 'PV-2026-ST-L3-005',
                'promotion' => 'Statistique, L3',
                'session' => 'Rattrapage',
                'date' => '15/11/2026',
                'president' => 'Mme Awa Traoré',
                'jury' => 'M. Moussa Diallo, Mme Fatou Ly, M. Yaya Bathily',
                'status' => 'Archivé',
                'tone' => 'dark',
                'size' => '1,9 Mo',
                'recordedBy' => 'Aminata Koné',
                'pages' => 3,
            ],
        ];
    @endphp

    <x-admin.page-header
        title="Registre des PV de délibération"
        subtitle="Créez, importez, corrigez, archivez et déverrouillez les procès-verbaux enregistrés pour l'année 2025-2026."
        :breadcrumb="['Accueil', 'PV de délibération']"
    >
        <x-slot:actions>
            <x-ui.btn variant="primary" href="{{ route('admin.pv.create') }}" icon="plus">Nouveau PV</x-ui.btn>
            <x-ui.btn variant="secondary" href="{{ route('admin.pv.import') }}" icon="upload">Import en masse</x-ui.btn>
            <x-ui.btn variant="ghost" href="{{ route('admin.exports.index') }}" icon="download">Exporter la sélection</x-ui.btn>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="pv-filters-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="pv-filters-title">Recherche et filtres</h2>
                <p class="admin-section__hint">Affinez la liste par année, filière, niveau, session, statut et référence.</p>
            </div>
        </div>

        <form class="admin-filters" method="GET" action="{{ route('admin.pv.index') }}">
            @csrf
            <label class="admin-field" for="pv-annee">
                <span class="admin-field__label">Année académique</span>
                <select class="admin-select" id="pv-annee" name="annee">
                    <option value="2024-2025" @selected(old('annee', '2025-2026') === '2024-2025')>2024-2025</option>
                    <option value="2025-2026" @selected(old('annee', '2025-2026') === '2025-2026')>2025-2026</option>
                    <option value="2026-2027" @selected(old('annee', '2025-2026') === '2026-2027')>2026-2027</option>
                </select>
            </label>

            <label class="admin-field" for="pv-filiere">
                <span class="admin-field__label">Filière</span>
                <select class="admin-select" id="pv-filiere" name="filiere">
                    <option value="" @selected(old('filiere', '') === '')>Toutes</option>
                    <option value="ig" @selected(old('filiere', '') === 'ig')>Informatique de gestion</option>
                    <option value="bf" @selected(old('filiere', '') === 'bf')>Banque et finance</option>
                    <option value="cca" @selected(old('filiere', '') === 'cca')>Comptabilité contrôle et audit</option>
                    <option value="grh" @selected(old('filiere', '') === 'grh')>Gestion des ressources humaines</option>
                    <option value="mk" @selected(old('filiere', '') === 'mk')>Marketing</option>
                    <option value="st" @selected(old('filiere', '') === 'st')>Statistique</option>
                </select>
            </label>

            <label class="admin-field" for="pv-niveau">
                <span class="admin-field__label">Niveau</span>
                <select class="admin-select" id="pv-niveau" name="niveau">
                    <option value="" @selected(old('niveau', '') === '')>Tous</option>
                    <option value="l1" @selected(old('niveau', '') === 'l1')>L1</option>
                    <option value="l2" @selected(old('niveau', '') === 'l2')>L2</option>
                    <option value="l3" @selected(old('niveau', '') === 'l3')>L3</option>
                </select>
            </label>

            <label class="admin-field" for="pv-session">
                <span class="admin-field__label">Session</span>
                <select class="admin-select" id="pv-session" name="session">
                    <option value="" @selected(old('session', '') === '')>Toutes</option>
                    <option value="normale" @selected(old('session', '') === 'normale')>Normale</option>
                    <option value="rattrapage" @selected(old('session', '') === 'rattrapage')>Rattrapage</option>
                </select>
            </label>

            <label class="admin-field" for="pv-statut">
                <span class="admin-field__label">Statut</span>
                <select class="admin-select" id="pv-statut" name="statut">
                    <option value="" @selected(old('statut', '') === '')>Tous</option>
                    <option value="brouillon" @selected(old('statut', '') === 'brouillon')>Brouillon</option>
                    <option value="archive" @selected(old('statut', '') === 'archive')>Archivé</option>
                </select>
            </label>

            <label class="admin-field admin-field--grow" for="pv-reference">
                <span class="admin-field__label">Référence</span>
                <input class="admin-input" id="pv-reference" name="reference" type="search" placeholder="PV-2026-IG-L3-014" value="{{ old('reference', '') }}" />
            </label>

            <x-ui.btn variant="primary" type="submit" icon="search">Filtrer</x-ui.btn>
            <x-ui.btn variant="secondary" href="{{ route('admin.pv.index') }}">Réinitialiser</x-ui.btn>
        </form>
    </section>

    <section class="admin-section" aria-labelledby="pv-summary-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="pv-summary-title">Aperçu du registre</h2>
                <p class="admin-section__hint">Volumes de l'année académique 2025-2026, toutes sessions confondues.</p>
            </div>
        </div>

        <div class="admin-summary-grid">
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="pv" /></span>
                <div>
                    <span class="admin-summary-card__value">248</span>
                    <span class="admin-summary-card__label">PV enregistrés</span>
                </div>
            </article>
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="clock" /></span>
                <div>
                    <span class="admin-summary-card__value">25</span>
                    <span class="admin-summary-card__label">En brouillon</span>
                </div>
            </article>
            <article class="admin-summary-card">
                <span class="admin-summary-card__icon"><x-admin.icon name="archive" /></span>
                <div>
                    <span class="admin-summary-card__value">96</span>
                    <span class="admin-summary-card__label">Archivés</span>
                </div>
            </article>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="pv-list-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="pv-list-title">PV enregistrés</h2>
                <p class="admin-section__hint">Créez, importez, corrigez, archivez et déverrouillez les procès-verbaux de l'année 2025-2026.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.badge label="25 à contrôler" tone="gold" />
                <x-ui.badge label="14 manquants" tone="red" />
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h3 class="admin-card__title">Liste des procès-verbaux</h3>
                <span class="admin-tiny admin-muted">Année 2025-2026</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Liste des procès-verbaux de délibération</caption>
                    <colgroup>
                        <col class="col-ref" style="width: 178px;" />
                        <col style="width: 220px;" />
                        <col style="width: 96px;" />
                        <col class="col-date" style="width: 120px;" />
                        <col class="col-president" style="width: 170px;" />
                        <col style="width: 100px;" />
                        <col style="width: 140px;" />
                        <col class="col-actions" style="width: 120px;" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="col-ref" scope="col">Référence</th>
                            <th scope="col">Promotion</th>
                            <th scope="col">Session</th>
                            <th class="col-date" scope="col">Date de délibération</th>
                            <th class="col-president" scope="col">Président du jury</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Fichier</th>
                            <th class="col-actions" scope="col"><span class="sr-only" hidden>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pvs as $index => $pv)
                            @php
                                $isArchived = $pv['status'] === 'Archivé';
                                $actions = [
                                    [
                                        'label' => 'Corriger les informations',
                                        'icon' => 'pencil',
                                        'modal' => 'pv-correct-'.$index,
                                        'reason' => $isArchived ? 'Motif obligatoire' : '',
                                    ],
                                ];
                                $actions[] = $isArchived
                                    ? ['label' => 'Déverrouiller', 'icon' => 'unlock', 'modal' => 'pv-unlock-'.$index, 'reason' => 'Motif obligatoire']
                                    : ['label' => 'Archiver', 'icon' => 'archive', 'modal' => 'pv-archive-'.$index];
                            @endphp
                            <tr>
                                <td class="col-ref">
                                    <button class="admin-table__link" type="button" data-drawer-open="#pv-detail-{{ $index }}">
                                        <x-ui.pv-reference :reference="$pv['reference']" />
                                    </button>
                                </td>
                                <td title="{{ $pv['promotion'] }} — Année 2025-2026">
                                    <span class="admin-table__primary">{{ $pv['promotion'] }}</span>
                                    <span class="admin-table__subtext">Année 2025-2026</span>
                                </td>
                                <td>{{ $pv['session'] }}</td>
                                <td class="admin-muted col-date">{{ $pv['date'] }}</td>
                                <td class="col-president" title="{{ $pv['president'] }}">{{ $pv['president'] }}</td>
                                <td><x-admin.status :label="$pv['status']" :tone="$pv['tone']" /></td>
                                <td title="Document PDF, {{ $pv['size'] }}">
                                    <span class="admin-table__filter" aria-hidden="true"><x-admin.icon name="pdf" /></span>
                                    <span class="admin-table__primary">Document PDF</span>
                                    <span class="admin-table__subtext">{{ $pv['size'] }}</span>
                                </td>
                                <td class="col-actions">
                                    <x-ui.table-actions :reference="$pv['reference']" :preview="'#pv-detail-'.$index" :actions="$actions" empty-message="Consultez le détail du PV" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-pagination">
                <span>6 PV affichés sur 248</span>
                <span class="admin-muted">Dernière mise à jour : 24/11/2026 à 09 h 42</span>
            </div>
        </div>
    </section>

    {{-- Tiroirs de détail : un par PV affiché --}}
    @foreach ($pvs as $index => $pv)
        @php
            $isArchived = $pv['status'] === 'Archivé';
            $timelineItems = [
                ['title' => 'Enregistré', 'meta' => $pv['date'].' — par '.$pv['recordedBy'], 'gold' => false],
                ['title' => 'Archivé', 'meta' => $isArchived ? '22/11/2026 — par Fatou Ly' : "En attente d'archivage", 'gold' => $isArchived],
            ];
            $results = [
                ['label' => 'Admis', 'tone' => 'blue', 'hommes' => 18, 'femmes' => 16, 'total' => 34, 'part' => '62,9 %'],
                ['label' => 'Ajourné', 'tone' => 'gold', 'hommes' => 6, 'femmes' => 5, 'total' => 11, 'part' => '20,4 %'],
                ['label' => 'Rattrapage', 'tone' => 'neutral', 'hommes' => 4, 'femmes' => 3, 'total' => 7, 'part' => '13,0 %'],
                ['label' => 'Exclu', 'tone' => 'red', 'hommes' => 1, 'femmes' => 1, 'total' => 2, 'part' => '3,7 %'],
            ];
        @endphp

        <x-ui.drawer id="pv-detail-{{ $index }}" title="Détail du PV" :subtitle="$pv['promotion']" :wide="true">
            @slot('header')
                <x-ui.pv-reference :reference="$pv['reference']" />
                <x-admin.status :label="$pv['status']" :tone="$pv['tone']" />
            @endslot

            <x-ui.viewer :reference="$pv['reference']" :pages="$pv['pages'] ?? 5" can-download />

            <div class="admin-card admin-card--flush">
                <div class="admin-tabs" role="tablist" aria-label="Sections du détail du PV">
                    <button class="admin-tabs__button" id="pv-info-tab-{{ $index }}" type="button" role="tab" aria-selected="true" aria-controls="pv-info-panel-{{ $index }}" data-tab="pv-info-{{ $index }}">Informations</button>
                    <button class="admin-tabs__button" id="pv-resultats-tab-{{ $index }}" type="button" role="tab" aria-selected="false" aria-controls="pv-resultats-panel-{{ $index }}" data-tab="pv-resultats-{{ $index }}" tabindex="-1">Résultats</button>
                </div>

                <div id="pv-info-panel-{{ $index }}" role="tabpanel" aria-labelledby="pv-info-tab-{{ $index }}" data-tab-panel="pv-info-{{ $index }}">
                    <div class="admin-card__body">
                        <dl class="admin-info-list">
                            <div class="admin-info-row">
                                <dt>Référence</dt>
                                <dd><x-ui.pv-reference :reference="$pv['reference']" /></dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Promotion</dt>
                                <dd>{{ $pv['promotion'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Année académique</dt>
                                <dd>2025-2026</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Session</dt>
                                <dd>{{ $pv['session'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Date de délibération</dt>
                                <dd>{{ $pv['date'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Président du jury</dt>
                                <dd>{{ $pv['president'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Membres du jury</dt>
                                <dd>{{ $pv['jury'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Enregistré par</dt>
                                <dd>{{ $pv['recordedBy'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Fichier</dt>
                                <dd>Document PDF, {{ $pv['size'] }}</dd>
                            </div>
                        </dl>

                        <div class="admin-section">
                            <h3 class="admin-card__title">Historique du dossier</h3>
                        </div>
                        <div class="admin-timeline">
                            @foreach ($timelineItems as $item)
                                <div class="admin-timeline__item{{ $item['gold'] ? ' admin-timeline__item--gold' : '' }}">
                                    <div class="admin-timeline__title">{{ $item['title'] }}</div>
                                    <div class="admin-timeline__meta">{{ $item['meta'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div id="pv-resultats-panel-{{ $index }}" role="tabpanel" aria-labelledby="pv-resultats-tab-{{ $index }}" data-tab-panel="pv-resultats-{{ $index }}" hidden>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <caption class="sr-only" hidden>Résultats du procès-verbal</caption>
                            <colgroup>
                                <col style="width: 190px;" />
                                <col style="width: 90px;" />
                                <col style="width: 90px;" />
                                <col style="width: 90px;" />
                                <col style="width: 100px;" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th scope="col">Décision</th>
                                    <th scope="col">Hommes</th>
                                    <th scope="col">Femmes</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Part</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    <tr>
                                        <td><x-admin.status :label="$result['label']" :tone="$result['tone']" /></td>
                                        <td>{{ $result['hommes'] }}</td>
                                        <td>{{ $result['femmes'] }}</td>
                                        <td>{{ $result['total'] }}</td>
                                        <td>{{ $result['part'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @slot('footer')
                <x-ui.btn variant="secondary" type="button" data-drawer-close>Fermer</x-ui.btn>
                <x-ui.btn variant="primary" href="{{ route('admin.exports.index') }}" icon="download">Télécharger le fichier</x-ui.btn>
            @endslot
        </x-ui.drawer>
    @endforeach

    {{-- Modales de correction, d'archivage et de déverrouillage --}}
    @foreach ($pvs as $index => $pv)
        <div class="admin-modal" id="pv-correct-{{ $index }}" hidden role="dialog" aria-modal="true" aria-labelledby="pv-correct-title-{{ $index }}">
            <div class="admin-modal__head">
                <h2 class="admin-modal__title" id="pv-correct-title-{{ $index }}">Corriger les informations</h2>
                <p class="admin-drawer__subtitle"><x-ui.pv-reference :reference="$pv['reference']" /></p>
            </div>
            <div class="admin-modal__body">
                <form id="pv-correct-form-{{ $index }}" action="{{ route('admin.pv.index') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="reference" value="{{ old('reference', $pv['reference']) }}" />

                    <div class="admin-form-grid">
                        <div class="admin-field admin-form-grid__full">
                            <label class="admin-field__label" for="pv-correct-motif-{{ $index }}">Motif de la modification</label>
                            <textarea
                                class="admin-textarea"
                                id="pv-correct-motif-{{ $index }}"
                                name="motif"
                                required
                                aria-invalid="@error('motif') true @else false @enderror"
                                aria-describedby="pv-correct-help-{{ $index }} @error('motif') pv-correct-error-{{ $index }} @enderror"
                            >{{ old('motif') }}</textarea>
                            <p class="admin-tiny admin-muted" id="pv-correct-help-{{ $index }}">Indiquez la donnée à rectifier et la valeur attendue.</p>
                            @error('motif')
                                <p class="admin-field__error" id="pv-correct-error-{{ $index }}">
                                    <x-admin.icon name="alert" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="admin-modal__footer">
                <x-ui.btn variant="secondary" type="button" data-overlay-close>Annuler</x-ui.btn>
                <x-ui.btn variant="primary" type="submit" form="pv-correct-form-{{ $index }}">Enregistrer la correction</x-ui.btn>
            </div>
        </div>

        <div class="admin-modal" id="pv-unlock-{{ $index }}" hidden role="dialog" aria-modal="true" aria-labelledby="pv-unlock-title-{{ $index }}">
            <div class="admin-modal__head">
                <h2 class="admin-modal__title" id="pv-unlock-title-{{ $index }}">Déverrouiller le PV</h2>
                <p class="admin-drawer__subtitle"><x-ui.pv-reference :reference="$pv['reference']" /></p>
            </div>
            <div class="admin-modal__body">
                <form id="pv-unlock-form-{{ $index }}" action="{{ route('admin.pv.index') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="reference" value="{{ old('reference', $pv['reference']) }}" />

                    <div class="admin-form-grid">
                        <div class="admin-field admin-form-grid__full">
                            <label class="admin-field__label" for="pv-unlock-motif-{{ $index }}">Motif du déverrouillage</label>
                            <textarea
                                class="admin-textarea"
                                id="pv-unlock-motif-{{ $index }}"
                                name="motif"
                                required
                                aria-invalid="@error('motif') true @else false @enderror"
                                aria-describedby="pv-unlock-help-{{ $index }} @error('motif') pv-unlock-error-{{ $index }} @enderror"
                            >{{ old('motif') }}</textarea>
                            <p class="admin-tiny admin-muted" id="pv-unlock-help-{{ $index }}">Le déverrouillage est tracé dans le journal d'audit.</p>
                            @error('motif')
                                <p class="admin-field__error" id="pv-unlock-error-{{ $index }}">
                                    <x-admin.icon name="alert" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="admin-modal__footer">
                <x-ui.btn variant="secondary" type="button" data-overlay-close>Annuler</x-ui.btn>
                <x-ui.btn variant="primary" type="submit" form="pv-unlock-form-{{ $index }}">Confirmer le déverrouillage</x-ui.btn>
            </div>
        </div>

        @if ($pv['status'] === 'Brouillon')
            <div class="admin-modal" id="pv-archive-{{ $index }}" hidden role="dialog" aria-modal="true" aria-labelledby="pv-archive-title-{{ $index }}">
                <div class="admin-modal__head">
                    <h2 class="admin-modal__title" id="pv-archive-title-{{ $index }}">Archiver le PV</h2>
                    <p class="admin-drawer__subtitle"><x-ui.pv-reference :reference="$pv['reference']" /></p>
                </div>
                <div class="admin-modal__body">
                    <form id="pv-archive-form-{{ $index }}" action="{{ route('admin.pv.index') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="reference" value="{{ old('reference', $pv['reference']) }}" />

                        <div class="admin-form-grid">
                            <div class="admin-field admin-form-grid__full">
                                <label class="admin-field__label" for="pv-archive-motif-{{ $index }}">Motif de l'archivage</label>
                                <textarea
                                    class="admin-textarea"
                                    id="pv-archive-motif-{{ $index }}"
                                    name="motif"
                                    required
                                    aria-invalid="@error('motif') true @else false @enderror"
                                    aria-describedby="pv-archive-help-{{ $index }} @error('motif') pv-archive-error-{{ $index }} @enderror"
                                >{{ old('motif') }}</textarea>
                                <p class="admin-tiny admin-muted" id="pv-archive-help-{{ $index }}">Le classement est tracé dans le journal d'audit.</p>
                                @error('motif')
                                    <p class="admin-field__error" id="pv-archive-error-{{ $index }}">
                                        <x-admin.icon name="alert" />
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="admin-modal__footer">
                    <x-ui.btn variant="secondary" type="button" data-overlay-close>Annuler</x-ui.btn>
                    <x-ui.btn variant="primary" type="submit" form="pv-archive-form-{{ $index }}">Archiver</x-ui.btn>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@pushOnce('scripts')
    <script>
        document.addEventListener('click', function (event) {
            var link = event.target.closest('td.col-actions a[href^="#pv-detail-"]');

            if (!link) {
                return;
            }

            event.preventDefault();
            var trigger = document.querySelector('[data-drawer-open="#' + link.getAttribute('href').slice(1) + '"]');

            if (trigger) {
                trigger.click();
            }
        }, true);
    </script>
@endPushOnce
