@extends('layouts.admin')

@section('title', 'Registre des PV')

@section('content')
    @php
        $pvs = [
            [
                'reference' => 'PV-2026-IG-L3-S2-014',
                'promotion' => 'Informatique de gestion, L3',
                'session' => 'S2 Normale',
                'date' => '20/11/2026',
                'president' => 'M. Moussa Diallo',
                'jury' => 'Mme Awa Traoré, M. Koffi Brou, Mme Nadia Sarr',
                'status' => 'Validé',
                'tone' => 'blue',
                'size' => '2,4 Mo',
                'recordedBy' => 'Aminata Koné',
                'folio' => 'Page 1 sur 4',
            ],
            [
                'reference' => 'PV-2026-BF-L2-S1-008',
                'promotion' => 'Banque et finance, L2',
                'session' => 'S1 Normale',
                'date' => '19/11/2026',
                'president' => 'Mme Awa Traoré',
                'jury' => 'M. Moussa Diallo, Mme Nadia Sarr, M. Yaya Bathily',
                'status' => 'Brouillon',
                'tone' => 'slate',
                'size' => '1,8 Mo',
                'recordedBy' => 'Mamadou Diop',
                'folio' => 'Page 1 sur 3',
            ],
            [
                'reference' => 'PV-2026-CCA-L3-S1-021',
                'promotion' => 'Comptabilité contrôle et audit, L3',
                'session' => 'S1 Rattrapage',
                'date' => '18/11/2026',
                'president' => 'M. Koffi Brou',
                'jury' => 'Mme Awa Traoré, M. Moussa Diallo, Mme Fatou Ly',
                'status' => 'Validé',
                'tone' => 'blue',
                'size' => '2,1 Mo',
                'recordedBy' => 'Sophie Nénesson',
                'folio' => 'Page 1 sur 4',
            ],
            [
                'reference' => 'PV-2026-GRH-L1-S2-006',
                'promotion' => 'Gestion des ressources humaines, L1',
                'session' => 'S2 Normale',
                'date' => '17/11/2026',
                'president' => 'Mme Nadia Sarr',
                'jury' => 'M. Koffi Brou, Mme Awa Traoré, M. Mamadou Diop',
                'status' => 'Archivé',
                'tone' => 'dark',
                'size' => '3,0 Mo',
                'recordedBy' => 'Yaya Bathily',
                'folio' => 'Page 1 sur 5',
            ],
            [
                'reference' => 'PV-2026-MK-L2-S1-012',
                'promotion' => 'Marketing, L2',
                'session' => 'S1 Normale',
                'date' => '16/11/2026',
                'president' => 'M. Moussa Diallo',
                'jury' => 'Mme Nadia Sarr, M. Koffi Brou, Mme Awa Traoré',
                'status' => 'Validé',
                'tone' => 'blue',
                'size' => '2,2 Mo',
                'recordedBy' => 'Fatou Ly',
                'folio' => 'Page 1 sur 4',
            ],
            [
                'reference' => 'PV-2026-ST-L3-S3-005',
                'promotion' => 'Statistique, L3',
                'session' => 'S3 Rattrapage',
                'date' => '15/11/2026',
                'president' => 'Mme Awa Traoré',
                'jury' => 'M. Moussa Diallo, Mme Fatou Ly, M. Yaya Bathily',
                'status' => 'Validé',
                'tone' => 'blue',
                'size' => '1,9 Mo',
                'recordedBy' => 'Aminata Koné',
                'folio' => 'Page 1 sur 3',
            ],
        ];
    @endphp

    <x-admin.page-header
        title="Registre des PV de délibération"
        subtitle="Espace de supervision : consultez, corrigez et archivez les procès-verbaux enregistrés pour l'année 2025-2026."
        :breadcrumb="['Accueil', 'PV de délibération']"
    >
        <x-slot:actions>
            <x-admin.status label="Mode supervision" tone="dark" />
            <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.exports.index') }}">
                <x-admin.icon name="download" />
                Exporter la sélection
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="pv-filters-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="pv-filters-title">Recherche et filtres</h2>
                <p class="admin-section__hint">Affinez la liste par année, filière, niveau, semestre, session et statut.</p>
            </div>
        </div>

        <form class="admin-filters" method="GET" action="{{ route('admin.pv.index') }}">
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

            <label class="admin-field" for="pv-semestre">
                <span class="admin-field__label">Semestre</span>
                <select class="admin-select" id="pv-semestre" name="semestre">
                    <option value="" @selected(old('semestre', '') === '')>Tous</option>
                    <option value="s1" @selected(old('semestre', '') === 's1')>S1</option>
                    <option value="s2" @selected(old('semestre', '') === 's2')>S2</option>
                    <option value="s3" @selected(old('semestre', '') === 's3')>S3</option>
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
                    <option value="valide" @selected(old('statut', '') === 'valide')>Validé</option>
                    <option value="archive" @selected(old('statut', '') === 'archive')>Archivé</option>
                </select>
            </label>

            <label class="admin-field admin-field--grow" for="pv-reference">
                <span class="admin-field__label">Référence</span>
                <input class="admin-input" id="pv-reference" name="reference" type="search" placeholder="PV-2026-IG-L3-S2-014" value="{{ old('reference', '') }}" />
            </label>

            <button class="admin-button admin-button--small" type="submit">
                <x-admin.icon name="search" />
                Filtrer
            </button>
            <a class="admin-button admin-button--secondary admin-button--small" href="{{ route('admin.pv.index') }}">Réinitialiser</a>
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
                <p class="admin-section__hint">Les actions sont limitées à la consultation et à la correction des métadonnées.</p>
            </div>
            <div class="admin-section__actions">
                <x-admin.status label="25 à contrôler" tone="gold" />
                <x-admin.status label="14 manquants" tone="red" />
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h3 class="admin-card__title">Liste des procès-verbaux</h3>
                <span class="admin-tiny admin-muted">Année 2025-2026</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Liste des procès-verbaux de délibération supervisés</caption>
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Promotion</th>
                            <th scope="col">Session</th>
                            <th scope="col">Date de délibération</th>
                            <th scope="col">Président du jury</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Fichier</th>
                            <th scope="col">Enregistré par</th>
                            <th scope="col"><span class="sr-only" hidden>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pvs as $index => $pv)
                            <tr>
                                <td>
                                    <button class="admin-table__link" type="button" data-drawer-open data-target="pv-detail-{{ $index }}">{{ $pv['reference'] }}</button>
                                </td>
                                <td>
                                    <span class="admin-table__primary">{{ $pv['promotion'] }}</span>
                                    <span class="admin-table__subtext">Année 2025-2026</span>
                                </td>
                                <td>{{ $pv['session'] }}</td>
                                <td class="admin-muted">{{ $pv['date'] }}</td>
                                <td>{{ $pv['president'] }}</td>
                                <td><x-admin.status :label="$pv['status']" :tone="$pv['tone']" /></td>
                                <td>
                                    <span class="admin-table__filter" aria-hidden="true"><x-admin.icon name="pdf" /></span>
                                    <span class="admin-table__primary">{{ $pv['reference'] }}.pdf</span>
                                    <span class="admin-table__subtext">{{ $pv['size'] }}</span>
                                </td>
                                <td>{{ $pv['recordedBy'] }}</td>
                                <td>
                                    <div class="admin-table__actions">
                                        <button class="admin-button admin-button--secondary admin-button--small" type="button" data-drawer-open data-target="pv-detail-{{ $index }}">
                                            <x-admin.icon name="eye" />
                                            Aperçu
                                        </button>
                                        <button class="admin-button admin-button--secondary admin-button--small" type="button" data-modal-open data-target="pv-correct-{{ $index }}" aria-label="Corriger les informations de {{ $pv['reference'] }}">
                                            <x-admin.icon name="pencil" />
                                            Corriger les informations
                                        </button>
                                        <button
                                            class="admin-button admin-button--secondary admin-button--small"
                                            type="button"
                                            @if ($pv['status'] !== 'Validé') disabled @else data-modal-open data-target="pv-archive-{{ $index }}" @endif
                                            aria-label="Archiver {{ $pv['reference'] }}"
                                        >
                                            <x-admin.icon name="archive" />
                                            Archiver
                                        </button>
                                        <button class="admin-button admin-button--secondary admin-button--small" type="button" data-modal-open data-target="pv-unlock-{{ $index }}" aria-label="Déverrouiller {{ $pv['reference'] }}">
                                            <x-admin.icon name="unlock" />
                                            Déverrouiller
                                        </button>
                                        <div class="admin-dropdown">
                                            <button class="admin-table__filter" type="button" data-dropdown aria-controls="pv-menu-{{ $index }}" aria-expanded="false" aria-label="Autres actions pour {{ $pv['reference'] }}">
                                                <x-admin.icon name="more" />
                                            </button>
                                            <div class="admin-dropdown__menu" id="pv-menu-{{ $index }}" hidden>
                                                <button class="admin-dropdown__item" type="button" data-drawer-open data-target="pv-detail-{{ $index }}"><x-admin.icon name="eye" /> Ouvrir l'aperçu</button>
                                                <button class="admin-dropdown__item" type="button"><x-admin.icon name="copy" /> Copier la référence</button>
                                                <a class="admin-dropdown__item" href="{{ route('admin.journal.index') }}"><x-admin.icon name="journal" /> Voir le journal</a>
                                                <div class="admin-dropdown__divider"></div>
                                                <a class="admin-dropdown__item" href="{{ route('admin.exports.index') }}"><x-admin.icon name="download" /> Télécharger le fichier</a>
                                            </div>
                                        </div>
                                    </div>
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
            $isValidated = in_array($pv['status'], ['Validé', 'Archivé'], true);
            $timelineItems = [
                ['title' => 'Enregistré', 'meta' => $pv['date'].' — par '.$pv['recordedBy'], 'gold' => false],
                ['title' => 'Validé', 'meta' => $isValidated ? '21/11/2026 — par Jury de délibération' : 'En attente de validation du jury', 'gold' => false],
                ['title' => 'Archivé', 'meta' => $isArchived ? '22/11/2026 — par Fatou Ly' : 'Non applicable pour ce dossier', 'gold' => $isArchived],
            ];
        @endphp

        <div class="admin-drawer admin-drawer--wide" id="pv-detail-{{ $index }}" hidden role="dialog" aria-modal="true" aria-labelledby="pv-detail-title-{{ $index }}">
            <div class="admin-drawer__head">
                <div>
                    <h2 class="admin-drawer__title" id="pv-detail-title-{{ $index }}">Détail du PV</h2>
                    <p class="admin-drawer__subtitle">{{ $pv['reference'] }} — {{ $pv['promotion'] }}</p>
                </div>
                <button class="admin-icon-button" type="button" data-overlay-close aria-label="Fermer le détail du PV">
                    <x-admin.icon name="close" />
                </button>
            </div>

            <div class="admin-drawer__body">
                <div class="admin-pv-layout">
                    <div>
                        <h3 class="admin-section__title">Aperçu du document</h3>
                        <div class="admin-scan-frame">
                            <div class="admin-scan-frame__toolbar">
                                <span>{{ $pv['folio'] }}</span>
                                <div class="admin-scan-frame__tools">
                                    <button class="admin-table__filter" type="button" aria-label="Zoom arrière"><x-admin.icon name="minus" /></button>
                                    <button class="admin-table__filter" type="button" aria-label="Zoom avant"><x-admin.icon name="plus" /></button>
                                    <button class="admin-table__filter" type="button" aria-label="Page précédente"><x-admin.icon name="chevron-left" /></button>
                                    <button class="admin-table__filter" type="button" aria-label="Page suivante"><x-admin.icon name="chevron-right" /></button>
                                    <button class="admin-table__filter" type="button" aria-label="Imprimer la page"><x-admin.icon name="printer" /></button>
                                    <button class="admin-table__filter" type="button" aria-label="Télécharger le fichier"><x-admin.icon name="download" /></button>
                                </div>
                            </div>
                            <div class="admin-scan-frame__page">
                                <svg viewBox="0 0 300 390" role="img" aria-label="Aperçu simplifié du procès-verbal {{ $pv['reference'] }}">
                                    <rect x="1" y="1" width="298" height="388" rx="8" fill="#FFFFFF" stroke="#DCE7FF" stroke-width="2"></rect>
                                    <rect x="1" y="1" width="298" height="54" rx="8" fill="#0B3394"></rect>
                                    <rect x="1" y="46" width="298" height="9" fill="#0B3394"></rect>
                                    <text x="18" y="23" font-size="11" font-weight="700" fill="#FFFFFF">ENEAM — PROCÈS-VERBAL</text>
                                    <text x="18" y="40" font-size="9" fill="#DCE7FF">{{ $pv['reference'] }}</text>

                                    <rect x="18" y="70" width="180" height="8" rx="4" fill="#0B3394"></rect>
                                    <rect x="18" y="86" width="248" height="4" rx="2" fill="#DCE7FF"></rect>
                                    <rect x="18" y="96" width="212" height="4" rx="2" fill="#DCE7FF"></rect>

                                    <rect x="18" y="118" width="264" height="66" rx="4" fill="#F5F7FF" stroke="#DCE7FF"></rect>
                                    <path d="M18 140H282M18 162H282" stroke="#DCE7FF" stroke-width="1"></path>
                                    <path d="M96 118V184M190 118V184" stroke="#DCE7FF" stroke-width="1"></path>
                                    <rect x="26" y="124" width="52" height="5" rx="2.5" fill="#2F6BFF"></rect>
                                    <rect x="104" y="124" width="44" height="5" rx="2.5" fill="#2F6BFF"></rect>
                                    <rect x="198" y="124" width="52" height="5" rx="2.5" fill="#2F6BFF"></rect>
                                    <rect x="26" y="148" width="56" height="4" rx="2" fill="#DCE7FF"></rect>
                                    <rect x="104" y="148" width="64" height="4" rx="2" fill="#DCE7FF"></rect>
                                    <rect x="198" y="148" width="48" height="4" rx="2" fill="#DCE7FF"></rect>

                                    <rect x="18" y="198" width="264" height="4" rx="2" fill="#DCE7FF"></rect>
                                    <rect x="18" y="210" width="236" height="4" rx="2" fill="#DCE7FF"></rect>
                                    <rect x="18" y="222" width="252" height="4" rx="2" fill="#DCE7FF"></rect>
                                    <rect x="18" y="234" width="196" height="4" rx="2" fill="#DCE7FF"></rect>

                                    <rect x="18" y="256" width="264" height="48" rx="4" fill="#E8EFFF"></rect>
                                    <path d="M18 272H282M18 288H282" stroke="#DCE7FF" stroke-width="1"></path>
                                    <rect x="26" y="262" width="86" height="5" rx="2.5" fill="#0B3394"></rect>
                                    <rect x="26" y="278" width="120" height="4" rx="2" fill="#FFFFFF"></rect>
                                    <rect x="26" y="294" width="104" height="4" rx="2" fill="#FFFFFF"></rect>

                                    <circle cx="238" cy="314" r="26" fill="#F5B91E" opacity="0.9"></circle>
                                    <path d="m227 314 8 8 14-16" fill="none" stroke="#08236B" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>

                                    <path d="M18 338h104M170 338h112" stroke="#5B6B94" stroke-width="2"></path>
                                    <text x="18" y="354" font-size="8" fill="#5B6B94">Président du jury</text>
                                    <text x="170" y="354" font-size="8" fill="#5B6B94">Secrétariat</text>
                                    <rect x="18" y="370" width="264" height="4" rx="2" fill="#DCE7FF"></rect>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="admin-card admin-card--flush">
                            <div class="admin-card__head">
                                <h3 class="admin-card__title">Informations du PV</h3>
                                <x-admin.status :label="$pv['status']" :tone="$pv['tone']" />
                            </div>
                            <div class="admin-card__body">
                                <dl class="admin-info-list">
                                    <div class="admin-info-row">
                                        <dt>Référence</dt>
                                        <dd>{{ $pv['reference'] }}</dd>
                                    </div>
                                    <div class="admin-info-row">
                                        <dt>Promotion</dt>
                                        <dd>{{ $pv['promotion'] }}</dd>
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
                                </dl>
                            </div>
                        </div>

                        <div class="admin-section">
                            <div class="admin-card admin-card--flush">
                                <div class="admin-card__head">
                                    <h3 class="admin-card__title">Historique du dossier</h3>
                                </div>
                                <div class="admin-card__body">
                                    <div class="admin-timeline">
                                        @foreach ($timelineItems as $item)
                                            <div class="admin-timeline__item{{ $item['gold'] ? ' admin-timeline__item--gold' : '' }}">
                                                <div class="admin-timeline__title">{{ $item['title'] }}</div>
                                                <div class="admin-timeline__meta">{{ $item['meta'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="admin-section">
                                        <div class="admin-notice">
                                            <x-admin.icon name="info" />
                                            <span>Fichier conforme : PDF, {{ $pv['size'] }}.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-drawer__footer">
                <button class="admin-button admin-button--secondary admin-button--small" type="button" data-overlay-close>Fermer</button>
                <a class="admin-button admin-button--small" href="{{ route('admin.exports.index') }}">
                    <x-admin.icon name="download" />
                    Télécharger le fichier
                </a>
            </div>
        </div>
    @endforeach

    {{-- Modales de correction, d'archivage et de déverrouillage --}}
    @foreach ($pvs as $index => $pv)
        <div class="admin-modal" id="pv-correct-{{ $index }}" hidden role="dialog" aria-modal="true" aria-labelledby="pv-correct-title-{{ $index }}">
            <div class="admin-modal__head">
                <h2 class="admin-modal__title" id="pv-correct-title-{{ $index }}">Corriger les informations</h2>
                <p class="admin-drawer__subtitle">{{ $pv['reference'] }}</p>
            </div>
            <div class="admin-modal__body">
                <form id="pv-correct-form-{{ $index }}" action="{{ route('admin.pv.index') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="reference" value="{{ old('reference', $pv['reference']) }}" />

                    <div class="admin-form-grid">
                        <div class="admin-field admin-form-grid__full">
                            <label class="admin-field__label" for="pv-correct-motif-{{ $index }}">Motif (obligatoire)</label>
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
                <button class="admin-button admin-button--secondary admin-button--small" type="button" data-overlay-close>Annuler</button>
                <button class="admin-button admin-button--small" type="submit" form="pv-correct-form-{{ $index }}">Enregistrer la correction</button>
            </div>
        </div>

        <div class="admin-modal" id="pv-unlock-{{ $index }}" hidden role="dialog" aria-modal="true" aria-labelledby="pv-unlock-title-{{ $index }}">
            <div class="admin-modal__head">
                <h2 class="admin-modal__title" id="pv-unlock-title-{{ $index }}">Déverrouiller le PV</h2>
                <p class="admin-drawer__subtitle">{{ $pv['reference'] }}</p>
            </div>
            <div class="admin-modal__body">
                <form id="pv-unlock-form-{{ $index }}" action="{{ route('admin.pv.index') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="reference" value="{{ old('reference', $pv['reference']) }}" />

                    <div class="admin-form-grid">
                        <div class="admin-field admin-form-grid__full">
                            <label class="admin-field__label" for="pv-unlock-motif-{{ $index }}">Motif (obligatoire)</label>
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
                <button class="admin-button admin-button--secondary admin-button--small" type="button" data-overlay-close>Annuler</button>
                <button class="admin-button admin-button--small" type="submit" form="pv-unlock-form-{{ $index }}">Confirmer le déverrouillage</button>
            </div>
        </div>

        @if ($pv['status'] === 'Validé')
            <div class="admin-modal" id="pv-archive-{{ $index }}" hidden role="dialog" aria-modal="true" aria-labelledby="pv-archive-title-{{ $index }}">
                <div class="admin-modal__head">
                    <h2 class="admin-modal__title" id="pv-archive-title-{{ $index }}">Archiver le PV</h2>
                    <p class="admin-drawer__subtitle">{{ $pv['reference'] }}</p>
                </div>
                <div class="admin-modal__body">
                    <form id="pv-archive-form-{{ $index }}" action="{{ route('admin.pv.index') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="reference" value="{{ old('reference', $pv['reference']) }}" />
                        <div class="admin-notice">
                            <x-admin.icon name="info" />
                            <span>Le dossier {{ $pv['reference'] }} sera classé dans les archives après confirmation.</span>
                        </div>
                    </form>
                </div>
                <div class="admin-modal__footer">
                    <button class="admin-button admin-button--secondary admin-button--small" type="button" data-overlay-close>Annuler</button>
                    <button class="admin-button admin-button--small" type="submit" form="pv-archive-form-{{ $index }}">Archiver</button>
                </div>
            </div>
        @endif
    @endforeach
@endsection
