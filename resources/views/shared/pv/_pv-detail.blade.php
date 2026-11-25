@php
    $drawerId = 'pv-detail-'.$pvIndex;
    $isArchived = ($pv['status'] ?? '') === 'Archivé';
    $canManage = in_array($roleKey, ['admin', 'agent'], true);
    $isAdmin = $roleKey === 'admin';
    $canDownload = $canManage;
    $canEdit = $canManage && (! $isArchived || $isAdmin);
    $filiereLabel = collect(config('ui-demo.filieres', []))->firstWhere('code', $pv['filiere'] ?? '')['label'] ?? ($pv['filiere'] ?? '');
    $infosTabId = 'pv-infos-tab-'.$pvIndex;
    $resultatsTabId = 'pv-resultats-tab-'.$pvIndex;
@endphp

<x-ui.drawer id="{{ $drawerId }}" title="Détail du PV {{ $pv['reference'] }}"
             subtitle="{{ $filiereLabel }} · {{ $pv['niveau'] ?? '' }} · session {{ $pv['session'] ?? '' }}" :wide="true">
    <x-slot:header>
        <div class="admin-section__actions">
            <x-ui.badge :label="$pv['status']" :tone="$isArchived ? 'dark' : 'gold'" />
            <span class="admin-tiny admin-muted">{{ $pv['pages'] ?? 5 }} pages</span>
        </div>
    </x-slot:header>

    <div class="ui-pv-layout">
        <div>
            <h3 class="admin-section__title">Aperçu du document</h3>
            <x-ui.viewer :reference="$pv['reference']" :pages="$pv['pages'] ?? 5" :can-download="$canDownload" />
        </div>

        <div>
            <div class="admin-tabs" role="tablist" aria-label="Sections du dossier {{ $pv['reference'] }}">
                <button class="admin-tabs__button" type="button" role="tab" id="{{ $infosTabId }}"
                        data-tab="infos-{{ $pvIndex }}" aria-controls="pv-infos-panel-{{ $pvIndex }}"
                        aria-selected="true" tabindex="0">Informations</button>
                <button class="admin-tabs__button" type="button" role="tab" id="{{ $resultatsTabId }}"
                        data-tab="resultats-{{ $pvIndex }}" aria-controls="pv-resultats-panel-{{ $pvIndex }}"
                        aria-selected="false" tabindex="-1">Résultats</button>
            </div>

            <div id="pv-infos-panel-{{ $pvIndex }}" role="tabpanel" aria-labelledby="{{ $infosTabId }}"
                 data-tab-panel="infos-{{ $pvIndex }}">
                <div class="admin-card admin-card--flush">
                    <div class="admin-card__head">
                        <h3 class="admin-card__title">Informations du PV</h3>
                        <x-ui.badge :label="$pv['status']" :tone="$isArchived ? 'dark' : 'gold'" />
                    </div>
                    <div class="admin-card__body">
                        <dl class="admin-info-list">
                            <div class="admin-info-row">
                                <dt>Référence</dt>
                                <dd><x-ui.pv-reference :reference="$pv['reference']" /></dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Année académique</dt>
                                <dd>{{ $pv['annee'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Filière</dt>
                                <dd title="{{ $filiereLabel }}">{{ $filiereLabel }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Niveau</dt>
                                <dd>{{ $pv['niveau'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Session</dt>
                                <dd>{{ $pv['session'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Date de délibération</dt>
                                <dd>{{ \Illuminate\Support\Carbon::parse($pv['date'])->format('d/m/Y') }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Président du jury</dt>
                                <dd title="{{ $pv['president'] }}">{{ $pv['president'] }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Membres du jury</dt>
                                <dd title="{{ implode(', ', $pv['membres'] ?? []) }}">{{ implode(', ', $pv['membres'] ?? []) }}</dd>
                            </div>
                            <div class="admin-info-row">
                                <dt>Enregistré par</dt>
                                <dd>{{ $pv['enregistre_par'] }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="admin-card admin-card--flush">
                    <div class="admin-card__head">
                        <h3 class="admin-card__title">Historique du dossier</h3>
                    </div>
                    <div class="admin-card__body">
                        <div class="admin-timeline">
                            <div class="admin-timeline__item">
                                <div class="admin-timeline__title">Enregistré</div>
                                <div class="admin-timeline__meta">
                                    {{ \Illuminate\Support\Carbon::parse($pv['date_enregistrement'])->format('d/m/Y') }} — par {{ $pv['enregistre_par'] }}
                                </div>
                            </div>
                            <div class="admin-timeline__item{{ $isArchived ? ' admin-timeline__item--gold' : '' }}">
                                <div class="admin-timeline__title">{{ $isArchived ? 'Archivé' : 'En attente d’archivage' }}</div>
                                <div class="admin-timeline__meta">
                                    {{ $isArchived ? 'Dossier classé dans les archives, modification tracée.' : 'Le dossier reste modifiable avant archivage.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="pv-resultats-panel-{{ $pvIndex }}" role="tabpanel" aria-labelledby="{{ $resultatsTabId }}"
                 data-tab-panel="resultats-{{ $pvIndex }}" hidden>
                @include('shared.pv.results', [
                    'pv' => $pv,
                    'roleKey' => $roleKey,
                    'resultKey' => 'pv-'.$pvIndex,
                    'canEditResults' => $canEdit,
                ])
            </div>
        </div>
    </div>

    <x-slot:footer>
        <button class="admin-button admin-button--secondary admin-button--small" type="button" data-drawer-close>Fermer</button>
        @if ($canEdit)
            <x-ui.btn :href="route($roleKey.'.pv.edit', $pv['reference'])" variant="compact" icon="pencil">Modifier le PV</x-ui.btn>
        @endif
    </x-slot:footer>
</x-ui.drawer>
