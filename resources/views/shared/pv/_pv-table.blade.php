<section class="admin-section" aria-labelledby="shared-pv-list-title">
    <div class="admin-section__head">
        <div>
            <h2 class="admin-section__title" id="shared-pv-list-title">PV enregistrés</h2>
            <p class="admin-section__hint">Sélectionnez une référence ou « Aperçu » pour consulter le dossier complet.</p>
        </div>
        <div class="admin-section__actions">
            <x-ui.badge :label="(string) count($pvs).' PV affichés'" tone="neutral" />
        </div>
    </div>

    <div class="admin-card admin-card--flush">
        <div class="admin-card__head">
            <h3 class="admin-card__title">Liste des procès-verbaux</h3>
            <span class="admin-tiny admin-muted">Registre partagé</span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <caption class="sr-only" hidden>Registre des procès-verbaux de délibération</caption>
                <colgroup>
                    <col class="col-ref">
                    <col data-pv-mobile-hide>
                    <col data-pv-mobile-hide>
                    <col class="col-date">
                    <col class="col-president">
                    <col data-pv-mobile-hide>
                    <col class="col-actions">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">Référence</th>
                        <th scope="col" data-pv-mobile-hide>Promotion</th>
                        <th scope="col" data-pv-mobile-hide>Session</th>
                        <th scope="col" class="col-date">Date de délibération</th>
                        <th scope="col" class="col-president">Président du jury</th>
                        <th scope="col" data-pv-mobile-hide>Statut</th>
                        <th scope="col" class="col-actions"><span class="sr-only" hidden>Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pvs as $pvIndex => $pv)
                        @php
                            $isArchived = ($pv['status'] ?? '') === 'Archivé';
                            $isDraft = ($pv['status'] ?? '') === 'Brouillon';
                            $isAdmin = $roleKey === 'admin';
                            $isAgent = $roleKey === 'agent';
                            $actions = [];
                            $emptyMessage = '';

                            if ($isAdmin) {
                                if ($isDraft) {
                                    $actions[] = [
                                        'label' => 'Corriger',
                                        'icon' => 'pencil',
                                        'url' => route($roleKey.'.pv.edit', $pv['reference']),
                                    ];
                                    $actions[] = [
                                        'label' => 'Archiver',
                                        'icon' => 'archive',
                                        'modal' => 'pv-archive-'.$pvIndex,
                                    ];
                                } elseif ($isArchived) {
                                    $actions[] = [
                                        'label' => 'Corriger',
                                        'icon' => 'pencil',
                                        'modal' => 'pv-correct-'.$pvIndex,
                                        'reason' => 'Motif obligatoire pour un PV archivé',
                                    ];
                                    $actions[] = [
                                        'label' => 'Déverrouiller',
                                        'icon' => 'unlock',
                                        'modal' => 'pv-unlock-'.$pvIndex,
                                        'reason' => 'Motif obligatoire',
                                    ];
                                }
                            } elseif ($isAgent && $isDraft) {
                                $actions[] = [
                                    'label' => 'Corriger',
                                    'icon' => 'pencil',
                                    'url' => route('agent.pv.edit', $pv['reference']),
                                ];
                                $actions[] = [
                                    'label' => 'Archiver',
                                    'icon' => 'archive',
                                    'modal' => 'pv-archive-'.$pvIndex,
                                ];
                            } elseif ($isArchived && $isAgent) {
                                $emptyMessage = "PV archivé : contactez l'administrateur pour une correction";
                            }

                            if ($emptyMessage === '' && $isArchived) {
                                $emptyMessage = 'Consultation seule';
                            }
                        @endphp

                        <tr class="is-hoverable-row">
                            <td>
                                <button class="admin-table__link" type="button"
                                        data-drawer-open="#pv-detail-{{ $pvIndex }}"
                                        data-pv-detail-trigger="pv-detail-{{ $pvIndex }}"
                                        title="{{ $pv['reference'] }}">{{ $pv['reference'] }}</button>
                                <span class="admin-table__subtext" title="{{ $pv['filiere'].' · '.$pv['niveau'].' · '.$pv['session'] }}">
                                    {{ $pv['filiere'] }} · {{ $pv['niveau'] }} · {{ $pv['session'] }}
                                </span>
                            </td>
                            <td data-pv-mobile-hide>
                                <span class="admin-table__primary" title="{{ $pv['filiere'] }}">{{ $pv['filiere'] }}</span>
                                <span class="admin-table__subtext">{{ $pv['annee'] }}</span>
                            </td>
                            <td data-pv-mobile-hide>{{ $pv['session'] }}</td>
                            <td class="admin-muted col-date" title="{{ \Illuminate\Support\Carbon::parse($pv['date'])->format('d/m/Y') }}">
                                {{ \Illuminate\Support\Carbon::parse($pv['date'])->format('d/m/Y') }}
                            </td>
                            <td class="col-president" title="{{ $pv['president'] }}">{{ $pv['president'] }}</td>
                            <td data-pv-mobile-hide>
                                <x-ui.badge :label="$pv['status']" :tone="$isArchived ? 'dark' : 'gold'" />
                            </td>
                            <td class="col-actions">
                                <x-ui.table-actions
                                    :reference="$pv['reference']"
                                    :preview="'#pv-detail-'.$pvIndex"
                                    :actions="$actions"
                                    :empty-message="$emptyMessage"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="admin-muted">Aucun PV ne correspond aux filtres sélectionnés.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <span>{{ count($pvs) }} PV affichés sur {{ $totalPvs ?? count(config('ui-demo.pvs', [])) }}</span>
            <span class="admin-muted">Données de démonstration</span>
        </div>
    </div>
</section>
