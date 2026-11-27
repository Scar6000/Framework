@extends('layouts.admin')

@section('title', "Journal d'audit")

@section('content')
    @php
        $selected = function (string $key, string $value): string {
            return old($key, request($key)) === $value ? 'selected' : '';
        };

        $entries = [
            [
                'date' => '24/11/2026 — 09 h 42',
                'initials' => 'JA',
                'name' => 'Dr Jean Théophile Agadamè',
                'role' => 'Administrateur',
                'action' => 'Modification',
                'object' => 'PV / PV-2026-IG-L3-014',
                'ip' => '10.12.4.18',
                'tone' => 'gold',
                'avatarTone' => 'gold',
                'avant' => 'Session normale · Statut : Brouillon · Moyenne : 13,10 / 20',
                'apres' => 'Session normale · Statut : Brouillon · Moyenne : 13,18 / 20',
                'motif' => 'Correction de la moyenne après vérification du procès-verbal signé.',
            ],
            [
                'date' => '24/11/2026 — 08 h 57',
                'initials' => 'AK',
                'name' => 'Aminata Koné',
                'role' => 'Agent de scolarité',
                'action' => 'Création',
                'object' => 'PV / PV-2026-BF-L2-008',
                'ip' => '10.12.4.31',
                'tone' => 'blue',
                'avatarTone' => 'soft',
                'avant' => 'Procès-verbal absent du registre',
                'apres' => 'Procès-verbal ajouté avec 42 étudiants',
                'motif' => 'Numérisation du document reçu du centre de gestion.',
            ],
            [
                'date' => '23/11/2026 — 16 h 14',
                'initials' => 'MD',
                'name' => 'Mamadou Diop',
                'role' => 'Chef de département',
                'action' => 'Archivage',
                'object' => 'PV / PV-2026-CCA-L3-021',
                'ip' => '10.12.5.07',
                'tone' => 'blue',
                'avatarTone' => 'blue',
                'avant' => 'Statut : Brouillon',
                'apres' => 'Statut : Archivé',
                'motif' => 'Classement du procès-verbal signé dans les archives de l’année 2025-2026.',
            ],
            [
                'date' => '23/11/2026 — 14 h 03',
                'initials' => 'SN',
                'name' => 'Sophie Nénesson',
                'role' => 'Chef de département',
                'action' => 'Consultation',
                'object' => 'Fiche étudiant / ENE-2023-1048',
                'ip' => '10.12.2.44',
                'tone' => 'neutral',
                'avatarTone' => 'soft',
                'avant' => 'Aucune consultation enregistrée',
                'apres' => 'Fiche consultée pendant quatre minutes',
                'motif' => 'Vérification du parcours dans le cadre d’une réclamation.',
            ],
            [
                'date' => '23/11/2026 — 11 h 26',
                'initials' => 'FL',
                'name' => 'Fatou Ly',
                'role' => 'Administrateur',
                'action' => 'Export',
                'object' => 'Promotion / Informatique de gestion L3',
                'ip' => '10.12.4.09',
                'tone' => 'neutral',
                'avatarTone' => 'gold',
                'avant' => 'Aucun export pour cette promotion',
                'apres' => 'Export Excel généré pour 36 étudiants',
                'motif' => 'Préparation du dossier de conseil de perfectionnement.',
            ],
            [
                'date' => '22/11/2026 — 17 h 38',
                'initials' => 'YB',
                'name' => 'Yaya Bathily',
                'role' => 'Agent de scolarité',
                'action' => 'Connexion',
                'object' => 'Session / Espace administrateur',
                'ip' => '10.12.6.22',
                'tone' => 'neutral',
                'avatarTone' => 'soft',
                'avant' => 'Session inactive',
                'apres' => 'Connexion établie depuis le poste de scolarité',
                'motif' => 'Accès nécessaire à la mise à jour des informations scolaires.',
            ],
            [
                'date' => '22/11/2026 — 10 h 05',
                'initials' => 'JA',
                'name' => 'Dr Jean Théophile Agadamè',
                'role' => 'Administrateur',
                'action' => 'Déverrouillage',
                'object' => 'PV / PV-2026-GRH-L1-006',
                'ip' => '10.12.4.18',
                'tone' => 'gold',
                'avatarTone' => 'gold',
                'avant' => 'PV archivé verrouillé',
                'apres' => 'PV archivé déverrouillé pour correction',
                'motif' => 'Correction demandée par le secrétariat de scolarité.',
            ],
            [
                'date' => '21/11/2026 — 15 h 12',
                'initials' => 'AK',
                'name' => 'Aminata Koné',
                'role' => 'Agent de scolarité',
                'action' => 'Import en masse',
                'actionLabel' => 'Import en masse (12 PV)',
                'object' => 'Lot / Année 2025-2026 — 12 PV',
                'ip' => '10.12.4.31',
                'tone' => 'blue',
                'avatarTone' => 'soft',
                'pvs' => [
                    'PV-2026-IG-L1-001', 'PV-2026-IG-L2-002', 'PV-2026-BF-L1-003', 'PV-2026-BF-L3-004',
                    'PV-2026-CCA-L2-005', 'PV-2026-CCA-L3-006', 'PV-2026-GRH-L1-007', 'PV-2026-GRH-L2-008',
                    'PV-2026-MK-L1-009', 'PV-2026-MK-L3-010', 'PV-2026-ST-L2-011', 'PV-2026-ST-L3-012',
                ],
                'motif' => 'Import groupé depuis le fichier transmis par le centre de gestion.',
            ],
            [
                'date' => '20/11/2026 — 16 h 48',
                'initials' => 'FL',
                'name' => 'Fatou Ly',
                'role' => 'Administrateur',
                'action' => 'Suppression',
                'object' => 'Fichier / PV-2026-MK-L2-012.pdf',
                'ip' => '10.12.4.09',
                'tone' => 'red',
                'avatarTone' => 'gold',
                'avant' => 'Fichier joint présent au registre',
                'apres' => 'Fichier joint retiré du registre',
                'motif' => 'Doublon détecté lors de la numérisation.',
            ],
        ];
    @endphp

    <x-admin.page-header
        title="Journal d'audit"
        subtitle="Consultez les connexions, modifications, archivages et exports enregistrés dans l'ENEAM."
        :breadcrumb="['Accueil', 'Administration', 'Journal d’audit']"
    >
        <x-slot name="actions">
            <div class="admin-dropdown">
                <x-ui.btn variant="primary" type="button" data-dropdown aria-controls="admin-journal-export-menu" aria-expanded="false">Exporter <x-admin.icon name="chevron-down" /></x-ui.btn>
                <div class="admin-dropdown__menu" id="admin-journal-export-menu" hidden>
                    <a class="admin-dropdown__item" href="{{ route('admin.exports.index') }}"><x-admin.icon name="file" /> Excel (.xlsx)</a>
                    <a class="admin-dropdown__item" href="{{ route('admin.exports.index') }}"><x-admin.icon name="pdf" /> PDF</a>
                </div>
            </div>
        </x-slot>
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="admin-journal-filtres-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-journal-filtres-title">Filtrer le journal</h2>
                <p class="admin-section__hint">Les critères portent uniquement sur les entrées existantes.</p>
            </div>
        </div>

        <form class="admin-filters" method="GET" action="{{ route('admin.journal.index') }}">
            <label class="admin-field admin-field--grow {{ $errors->has('utilisateur') ? 'admin-field--invalid' : '' }}" for="admin-journal-utilisateur">
                <span class="admin-field__label">Utilisateur</span>
                <select class="admin-select" id="admin-journal-utilisateur" name="utilisateur">
                    <option value="">Tous les utilisateurs</option>
                    <option value="Dr Jean Théophile Agadamè" {{ $selected('utilisateur', 'Dr Jean Théophile Agadamè') }}>Dr Jean Théophile Agadamè</option>
                    <option value="Aminata Koné" {{ $selected('utilisateur', 'Aminata Koné') }}>Aminata Koné</option>
                    <option value="Mamadou Diop" {{ $selected('utilisateur', 'Mamadou Diop') }}>Mamadou Diop</option>
                    <option value="Sophie Nénesson" {{ $selected('utilisateur', 'Sophie Nénesson') }}>Sophie Nénesson</option>
                    <option value="Fatou Ly" {{ $selected('utilisateur', 'Fatou Ly') }}>Fatou Ly</option>
                    <option value="Yaya Bathily" {{ $selected('utilisateur', 'Yaya Bathily') }}>Yaya Bathily</option>
                </select>
                @error('utilisateur')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field admin-field--grow {{ $errors->has('type_action') ? 'admin-field--invalid' : '' }}" for="admin-journal-type-action">
                <span class="admin-field__label">Type d'action</span>
                <select class="admin-select" id="admin-journal-type-action" name="type_action">
                    <option value="">Toutes les actions</option>
                    <option value="Connexion" {{ $selected('type_action', 'Connexion') }}>Connexion</option>
                    <option value="Consultation" {{ $selected('type_action', 'Consultation') }}>Consultation</option>
                    <option value="Création" {{ $selected('type_action', 'Création') }}>Création</option>
                    <option value="Modification" {{ $selected('type_action', 'Modification') }}>Modification</option>
                    <option value="Archivage" {{ $selected('type_action', 'Archivage') }}>Archivage</option>
                    <option value="Déverrouillage" {{ $selected('type_action', 'Déverrouillage') }}>Déverrouillage</option>
                    <option value="Import en masse" {{ $selected('type_action', 'Import en masse') }}>Import en masse</option>
                    <option value="Export" {{ $selected('type_action', 'Export') }}>Export</option>
                    <option value="Suppression" {{ $selected('type_action', 'Suppression') }}>Suppression</option>
                </select>
                @error('type_action')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field {{ $errors->has('du') ? 'admin-field--invalid' : '' }}" for="admin-journal-du">
                <span class="admin-field__label">Période du</span>
                <input class="admin-input" type="date" id="admin-journal-du" name="du" value="{{ old('du', request('du', '')) }}">
                @error('du')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field {{ $errors->has('au') ? 'admin-field--invalid' : '' }}" for="admin-journal-au">
                <span class="admin-field__label">Période au</span>
                <input class="admin-input" type="date" id="admin-journal-au" name="au" value="{{ old('au', request('au', '')) }}">
                @error('au')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <label class="admin-field admin-field--grow {{ $errors->has('objet') ? 'admin-field--invalid' : '' }}" for="admin-journal-objet">
                <span class="admin-field__label">Objet</span>
                <input class="admin-input" type="search" id="admin-journal-objet" name="objet" placeholder="Référence, matricule ou promotion" value="{{ old('objet', request('objet', '')) }}">
                @error('objet')
                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                @enderror
            </label>

            <x-ui.btn variant="primary" type="submit" icon="filter">Filtrer</x-ui.btn>
            <x-ui.btn variant="secondary" :href="route('admin.journal.index')" icon="refresh">Réinitialiser</x-ui.btn>
        </form>
    </section>

    <div class="admin-section">
        <div class="admin-notice" role="note">
            <x-admin.icon name="lock" />
            <span>Lecture seule : les entrées du journal ne peuvent être ni modifiées ni supprimées.</span>
        </div>
    </div>

    <section class="admin-section" aria-labelledby="admin-journal-table-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-journal-table-title">Entrées enregistrées</h2>
                <p class="admin-section__hint">9 dernières entrées affichées sur 248.</p>
            </div>
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Entrées les plus récentes du journal d'audit</caption>
                    <colgroup>
                        <col class="col-date">
                        <col width="20%">
                        <col width="14%">
                        <col width="16%">
                        <col class="col-ref">
                        <col width="10%">
                        <col class="col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col" class="col-date">Date et heure</th>
                            <th scope="col">Utilisateur</th>
                            <th scope="col">Rôle</th>
                            <th scope="col">Action</th>
                            <th scope="col" class="col-ref">Objet</th>
                            <th scope="col">Adresse IP</th>
                            <th scope="col" class="col-actions"><span class="sr-only" hidden>Détails</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entries as $index => $entry)
                            @php
                                $detailId = 'detail-journal-'.($index + 1);
                            @endphp
                            <tr>
                                <td class="admin-muted col-date">{{ $entry['date'] }}</td>
                                <td>
                                    <span class="admin-cell-user">
                                        <x-admin.avatar :initials="$entry['initials']" size="sm" :tone="$entry['avatarTone']" :label="$entry['name']" />
                                        <span class="admin-cell-user__text">
                                            <span class="admin-table__primary" title="{{ $entry['name'] }}">{{ $entry['name'] }}</span>
                                        </span>
                                    </span>
                                </td>
                                <td>{{ $entry['role'] }}</td>
                                <td><x-admin.status :label="$entry['actionLabel'] ?? $entry['action']" :tone="$entry['tone']" /></td>
                                <td class="col-ref" title="{{ $entry['object'] }}">{{ $entry['object'] }}</td>
                                <td class="admin-muted">{{ $entry['ip'] }}</td>
                                <td class="col-actions">
                                    <div class="admin-table__actions">
                                        <button type="button" class="admin-table__filter" data-details-toggle aria-controls="{{ $detailId }}" aria-expanded="false" aria-label="Voir les détails de {{ $entry['action'] }}">
                                            <x-admin.icon name="chevron-down" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr id="{{ $detailId }}" class="admin-journal-details" hidden>
                                <td colspan="7">
                                    @if (! empty($entry['pvs']))
                                        <div class="admin-diff">
                                            <div class="admin-diff__block">
                                                <h4>Références importées</h4>
                                                @foreach ($entry['pvs'] as $reference)
                                                    <p class="admin-tiny" title="{{ $reference }}">{{ $reference }}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="admin-diff">
                                            <div class="admin-diff__block">
                                                <h4>Avant</h4>
                                                <p title="{{ $entry['avant'] }}">{{ $entry['avant'] }}</p>
                                            </div>
                                            <div class="admin-diff__block">
                                                <h4>Après</h4>
                                                <p title="{{ $entry['apres'] }}">{{ $entry['apres'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    <p class="admin-tiny admin-muted"><strong>Motif :</strong> {{ $entry['motif'] }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-pagination">
                <span>Affichage de 9 entrées sur 248</span>
                <nav class="admin-pagination__list" aria-label="Pages du journal">
                    <a class="admin-pagination__link" href="{{ route('admin.journal.index') }}" aria-label="Page précédente">Précédent</a>
                    <a class="admin-pagination__link is-active" href="{{ route('admin.journal.index') }}" aria-current="page">1</a>
                    <a class="admin-pagination__link" href="{{ route('admin.journal.index') }}">2</a>
                    <a class="admin-pagination__link" href="{{ route('admin.journal.index') }}">3</a>
                    <a class="admin-pagination__link" href="{{ route('admin.journal.index') }}">4</a>
                    <a class="admin-pagination__link" href="{{ route('admin.journal.index') }}" aria-label="Page suivante">Suivant</a>
                </nav>
            </div>
        </div>
    </section>
@endsection
