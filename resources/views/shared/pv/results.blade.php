@php
    $resultKey = $resultKey ?? 'resultats';
    $roleKey = $roleKey ?? 'admin';
    $canEditResults = $canEditResults ?? false;
    $isArchived = ($pv['status'] ?? '') === 'Archivé';
    $resultats = config('ui-demo.resultats', []);
    $decisionTones = [
        'Admis' => 'blue',
        'Rattrapage' => 'gold',
        'Ajourné' => 'red',
    ];
@endphp

<div class="ui-form-stack" data-pv-result-scope="{{ $resultKey }}">
    @if ($isArchived && $roleKey === 'agent')
        <div class="ui-notice">
            <x-admin.icon name="lock" />
            <span>PV archivé : pour corriger, contactez l'administrateur.</span>
        </div>
    @elseif (! $canEditResults)
        <div class="ui-notice">
            <x-admin.icon name="info" />
            <span>Consultation seule : la saisie des résultats n'est pas disponible pour ce profil.</span>
        </div>
    @endif

    <div class="admin-card admin-card--flush">
        <div class="admin-card__head">
            <h3 class="admin-card__title">Résultats du PV {{ $pv['reference'] }}</h3>
        </div>

        <div class="admin-card__body ui-form-stack">
            <div class="admin-filters">
                <div class="admin-field admin-field--grow">
                    <label class="admin-field__label" for="pv-results-search-{{ $resultKey }}">Recherche rapide</label>
                    <input class="admin-input" id="pv-results-search-{{ $resultKey }}" type="search"
                           placeholder="Matricule ou nom" data-pv-result-search
                           aria-label="Rechercher un résultat dans le PV">
                </div>

                @if ($canEditResults)
                    <x-ui.btn variant="secondary" icon="plus" data-pv-result-add>Ajouter une ligne</x-ui.btn>
                    <x-ui.btn variant="secondary" icon="upload" data-pv-result-import-toggle>Importer (Excel / CSV)</x-ui.btn>
                @endif

                @if ($roleKey !== 'chef')
                    <x-ui.btn href="#" variant="ghost" icon="download">Télécharger le modèle</x-ui.btn>
                @endif
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Résultats de délibération du PV {{ $pv['reference'] }}</caption>
                    <colgroup>
                        <col class="col-ref">
                        <col>
                        <col>
                        <col>
                        <col>
                        <col class="col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col" class="col-ref">Matricule</th>
                            <th scope="col">Nom et prénoms</th>
                            <th scope="col">Moyenne annuelle</th>
                            <th scope="col">Crédits obtenus</th>
                            <th scope="col">Décision</th>
                            <th scope="col" class="col-actions"><span class="sr-only" hidden>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody data-pv-result-body>
                        @foreach ($resultats as $resultat)
                            <tr data-pv-result-row
                                data-pv-result-text="{{ $resultat['matricule'].' '.$resultat['nom'].' '.$resultat['decision'] }}">
                                <td title="{{ $resultat['matricule'] }}">{{ $resultat['matricule'] }}</td>
                                <td title="{{ $resultat['nom'] }}">{{ $resultat['nom'] }}</td>
                                <td>{{ $resultat['moyenne'] }}</td>
                                <td>{{ $resultat['credits'] }}</td>
                                <td>
                                    <x-ui.badge :label="$resultat['decision']"
                                                :tone="$decisionTones[$resultat['decision']] ?? 'neutral'" />
                                </td>
                                <td class="col-actions">
                                    @if ($canEditResults)
                                        <button class="admin-icon-button admin-icon-button--small" type="button"
                                                data-pv-result-remove
                                                aria-label="Retirer la ligne de {{ $resultat['nom'] }}">
                                            <x-admin.icon name="trash" />
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <template data-pv-result-template>
                <tr data-pv-result-row data-pv-result-text="nouvelle ligne">
                    <td><input class="admin-input" data-pv-result-field="matricule" aria-label="Matricule" placeholder="Matricule"></td>
                    <td><input class="admin-input" data-pv-result-field="nom" aria-label="Nom et prénoms" placeholder="Nom et prénoms"></td>
                    <td><input class="admin-input" data-pv-result-field="moyenne" aria-label="Moyenne annuelle" placeholder="Moyenne"></td>
                    <td><input class="admin-input" data-pv-result-field="credits" aria-label="Crédits obtenus" placeholder="Crédits"></td>
                    <td>
                        <select class="admin-select" data-pv-result-field="decision" aria-label="Décision">
                            <option value="Admis">Admis</option>
                            <option value="Rattrapage">Rattrapage</option>
                            <option value="Ajourné">Ajourné</option>
                        </select>
                    </td>
                    <td class="col-actions">
                        <button class="admin-icon-button admin-icon-button--small" type="button" data-pv-result-remove
                                aria-label="Retirer la ligne ajoutée"><x-admin.icon name="trash" /></button>
                    </td>
                </tr>
            </template>

            @if ($canEditResults)
                <div class="ui-form-stack" data-pv-result-import hidden>
                    <div class="ui-steps" role="list" aria-label="Étapes de l'import de résultats">
                        <span class="ui-step is-current" data-pv-result-step-indicator="1" aria-current="step">1. Choix du fichier</span>
                        <span class="ui-step" data-pv-result-step-indicator="2">2. Contrôle des lignes</span>
                        <span class="ui-step" data-pv-result-step-indicator="3">3. Confirmation</span>
                    </div>

                    <div data-pv-result-step-panel="1">
                        <p class="admin-tiny admin-muted">Fichier Excel ou CSV, une ligne par étudiant.</p>
                        <div class="admin-field">
                            <label class="admin-field__label" for="pv-results-file-{{ $resultKey }}">Fichier de résultats</label>
                            <input class="admin-input" id="pv-results-file-{{ $resultKey }}" type="file"
                                   accept=".xlsx,.xls,.csv" data-pv-result-file>
                        </div>
                        <x-ui.btn variant="secondary" icon="arrow-right" data-pv-result-analyze disabled>Analyser le fichier</x-ui.btn>
                    </div>

                    <div data-pv-result-step-panel="2" hidden>
                        <p class="admin-tiny admin-muted" data-pv-result-preview-count>2 lignes valides, 1 ligne à corriger.</p>
                        <div class="ui-import-scroll">
                            <table class="ui-import-table">
                                <caption class="sr-only" hidden>Aperçu des lignes importées</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Matricule</th>
                                        <th scope="col">Nom et prénoms</th>
                                        <th scope="col">Moyenne</th>
                                        <th scope="col">Crédits</th>
                                        <th scope="col">Décision</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2024-IG-051</td><td>Solène Agbo</td><td>13,40</td><td>60</td><td>Admis</td>
                                    </tr>
                                    <tr>
                                        <td>2024-IG-052</td><td>Rémi Houessou</td><td>10,15</td><td>48</td><td>Rattrapage</td>
                                    </tr>
                                    <tr>
                                        <td class="is-missing">2024-IG-</td>
                                        <td class="is-missing">Nom absent</td>
                                        <td class="is-missing">—</td>
                                        <td>36</td>
                                        <td>Ajourné</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <x-ui.btn variant="primary" data-pv-result-import-confirm>Importer 2 lignes valides</x-ui.btn>
                    </div>

                    <div data-pv-result-step-panel="3" hidden>
                        <div class="ui-notice ui-notice--success">
                            <x-admin.icon name="check" />
                            <span>2 lignes ont été ajoutées aux résultats du PV.</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@pushOnce('scripts', 'shared-pv-results')
    <script>
        (function () {
            document.querySelectorAll('[data-pv-result-scope]').forEach(function (scope) {
                var search = scope.querySelector('[data-pv-result-search]');
                var body = scope.querySelector('[data-pv-result-body]');
                var template = scope.querySelector('[data-pv-result-template]');
                var addButton = scope.querySelector('[data-pv-result-add]');
                var importPanel = scope.querySelector('[data-pv-result-import]');
                var importToggle = scope.querySelector('[data-pv-result-import-toggle]');
                var fileInput = scope.querySelector('[data-pv-result-file]');
                var analyzeButton = scope.querySelector('[data-pv-result-analyze]');
                var confirmButton = scope.querySelector('[data-pv-result-import-confirm]');

                if (search) {
                    search.addEventListener('input', function () {
                        var term = search.value.trim().toLowerCase();

                        body.querySelectorAll('[data-pv-result-row]').forEach(function (row) {
                            var text = (row.getAttribute('data-pv-result-text') || '').toLowerCase();
                            row.hidden = term !== '' && text.indexOf(term) === -1;
                        });
                    });
                }

                if (addButton) {
                    addButton.addEventListener('click', function () {
                        var fragment = template.content.cloneNode(true);
                        body.appendChild(fragment);
                        var lastRow = body.lastElementChild;
                        var firstInput = lastRow.querySelector('input');

                        if (firstInput) {
                            firstInput.focus();
                        }
                    });
                }

                body.addEventListener('click', function (event) {
                    var removeButton = event.target.closest('[data-pv-result-remove]');

                    if (removeButton) {
                        event.preventDefault();
                        var row = removeButton.closest('tr');

                        if (row) {
                            row.remove();
                        }
                    }
                });

                if (!importPanel) {
                    return;
                }

                function setImportStep(step) {
                    scope.querySelectorAll('[data-pv-result-step-indicator]').forEach(function (indicator) {
                        var current = indicator.getAttribute('data-pv-result-step-indicator') === String(step);
                        indicator.classList.toggle('is-current', current);

                        if (current) {
                            indicator.setAttribute('aria-current', 'step');
                        } else {
                            indicator.removeAttribute('aria-current');
                        }
                    });

                    scope.querySelectorAll('[data-pv-result-step-panel]').forEach(function (panel) {
                        panel.hidden = panel.getAttribute('data-pv-result-step-panel') !== String(step);
                    });
                }

                importToggle.addEventListener('click', function () {
                    importPanel.hidden = !importPanel.hidden;

                    if (!importPanel.hidden) {
                        setImportStep(1);
                        fileInput.value = '';
                        analyzeButton.disabled = true;
                    }
                });

                fileInput.addEventListener('change', function () {
                    analyzeButton.disabled = fileInput.files.length === 0;
                });

                analyzeButton.addEventListener('click', function () {
                    setImportStep(2);
                });

                confirmButton.addEventListener('click', function () {
                    var previewRows = scope.querySelectorAll('[data-pv-result-step-panel="2"] tbody tr');

                    previewRows.forEach(function (previewRow, index) {
                        if (previewRow.querySelector('.is-missing') || index > 1) {
                            return;
                        }

                        var fragment = template.content.cloneNode(true);
                        var row = fragment.querySelector('tr');
                        var cells = previewRow.querySelectorAll('td');
                        var inputs = row.querySelectorAll('[data-pv-result-field]');

                        inputs.forEach(function (input, inputIndex) {
                            if (cells[inputIndex]) {
                                input.value = cells[inputIndex].textContent.trim();
                            }
                        });

                        row.setAttribute('data-pv-result-text', row.textContent.replace(/\s+/g, ' ').trim());
                        body.appendChild(fragment);
                    });

                    setImportStep(3);
                });
            });
        })();
    </script>
@endpushonce
