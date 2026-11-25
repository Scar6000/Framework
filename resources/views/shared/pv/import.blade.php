@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Import en masse de PV')

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $indexRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.pv.index')
            ? route($roleKey.'.pv.index')
            : route('admin.pv.index');
        $annees = config('ui-demo.annees', []);
        $filieres = collect(config('ui-demo.filieres', []));
        $niveaux = config('ui-demo.niveaux', []);
        $sessions = config('ui-demo.sessions', []);
    @endphp

    <x-admin.page-header
        title="Import en masse de PV"
        subtitle="Enregistrez plusieurs procès-verbaux en une seule opération, puis contrôlez leurs informations."
        :breadcrumb="['Accueil', 'Registre des PV', 'Import en masse']"
    >
        <x-slot:actions>
            <x-ui.btn :href="$indexRoute" variant="secondary" icon="chevron-left">Retour au registre</x-ui.btn>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="shared-pv-import-title" data-pv-import>
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-pv-import-title">Importer des PV</h2>
                <p class="admin-section__hint">Deux étapes : sélection des fichiers, puis informations communes et contrôle ligne par ligne.</p>
            </div>
        </div>

        <div class="ui-steps" role="list" aria-label="Progression de l'import">
            <span class="ui-step is-current" data-pv-step-indicator="1" aria-current="step">1. Fichiers</span>
            <span class="ui-step" data-pv-step-indicator="2">2. Informations</span>
        </div>

        <section data-pv-step-panel="1" aria-label="Étape 1 : sélection des fichiers">
            <div class="admin-card admin-card--flush">
                <div class="admin-card__head">
                    <h3 class="admin-card__title">Fichiers à importer</h3>
                    <span class="admin-tiny admin-muted">10 à 100 fichiers · PDF, JPG ou PNG</span>
                </div>
                <div class="admin-card__body ui-form-stack">
                    <div class="ui-dropzone" data-pv-dropzone role="button" tabindex="0"
                         aria-label="Sélectionner les fichiers à importer">
                        <span class="ui-dropzone__icon"><x-admin.icon name="upload" /></span>
                        <span class="ui-dropzone__title">Déposez vos fichiers ici</span>
                        <span class="ui-dropzone__hint">ou choisissez plusieurs fichiers sur votre poste</span>
                        <button class="admin-button admin-button--secondary admin-button--small" type="button"
                                data-pv-file-pick>Parcourir les fichiers</button>
                        <input class="sr-only" type="file" multiple accept=".pdf,.jpg,.jpeg,.png"
                               data-pv-file-input aria-label="Fichiers des PV à importer">
                    </div>

                    <p class="admin-tiny admin-muted" data-pv-summary aria-live="polite">0 fichiers sélectionnés (0 Mo)</p>

                    <div class="ui-form-stack" data-pv-files aria-label="Fichiers acceptés"></div>

                    <div class="ui-form-stack" data-pv-rejected aria-label="Fichiers rejetés"></div>
                </div>
            </div>

            <div class="ui-sticky-actions">
                <span class="ui-sticky-actions__count">La limite est fixée à 100 fichiers par import.</span>
                <x-ui.btn :href="$indexRoute" variant="ghost">Annuler</x-ui.btn>
                <x-ui.btn variant="primary" icon="arrow-right" data-pv-continue disabled>Continuer</x-ui.btn>
            </div>
        </section>

        <section data-pv-step-panel="2" aria-label="Étape 2 : informations des PV" hidden>
            <div class="admin-card admin-card--flush">
                <div class="ui-common-bar" aria-label="Appliquer une valeur commune">
                    <div class="admin-field">
                        <label class="admin-field__label" for="pv-common-annee">Année</label>
                        <select class="admin-select" id="pv-common-annee" data-pv-common="annee">
                            <option value="">Année</option>
                            @foreach ($annees as $annee)
                                <option value="{{ $annee }}">{{ $annee }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-field">
                        <label class="admin-field__label" for="pv-common-filiere">Filière</label>
                        <select class="admin-select" id="pv-common-filiere" data-pv-common="filiere">
                            <option value="">Filière</option>
                            @foreach ($filieres as $filiere)
                                <option value="{{ $filiere['code'] }}">{{ $filiere['code'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-field">
                        <label class="admin-field__label" for="pv-common-niveau">Niveau</label>
                        <select class="admin-select" id="pv-common-niveau" data-pv-common="niveau">
                            <option value="">Niveau</option>
                            @foreach ($niveaux as $niveau)
                                <option value="{{ $niveau }}">{{ $niveau }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-field">
                        <label class="admin-field__label" for="pv-common-session">Session</label>
                        <select class="admin-select" id="pv-common-session" data-pv-common="session">
                            <option value="">Session</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session }}">{{ $session }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-field">
                        <label class="admin-field__label" for="pv-common-date">Date</label>
                        <input class="admin-input" id="pv-common-date" type="date" data-pv-common="date">
                    </div>
                    <div class="admin-field">
                        <label class="admin-field__label" for="pv-common-president">Président</label>
                        <input class="admin-input" id="pv-common-president" type="text"
                               placeholder="Président du jury" data-pv-common="president">
                    </div>
                    <x-ui.btn variant="primary" data-pv-apply>
                        <span data-pv-apply-label>Appliquer à toutes les lignes</span>
                    </x-ui.btn>
                </div>

                <div class="ui-import-scroll">
                    <table class="ui-import-table">
                        <caption class="sr-only" hidden>PV à importer avec leurs informations</caption>
                        <colgroup>
                            <col class="col-file">
                            <col>
                            <col class="col-file">
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col" class="col-file"><span class="sr-only" hidden>Sélection</span></th>
                                <th scope="col">N°</th>
                                <th scope="col" class="col-file" style="left: 38px;">Fichier</th>
                                <th scope="col">Année</th>
                                <th scope="col">Filière</th>
                                <th scope="col">Niveau</th>
                                <th scope="col">Session</th>
                                <th scope="col">Date</th>
                                <th scope="col">Président</th>
                                <th scope="col">Référence prévue</th>
                                <th scope="col">État</th>
                                <th scope="col"><span class="sr-only" hidden>Rétablir</span></th>
                            </tr>
                        </thead>
                        <tbody data-pv-rows></tbody>
                    </table>
                </div>
            </div>

            <p class="ui-notice">
                <x-admin.icon name="info" />
                <span>Une ligne de synthèse et une ligne par PV seront ajoutées au journal.</span>
            </p>

            <div class="admin-card admin-card--flush" data-pv-progress hidden>
                <div class="admin-card__body ui-form-stack">
                    <p class="admin-tiny admin-muted" data-pv-progress-text aria-live="polite">Enregistrement 0 / 0</p>
                    <div class="ui-progress-line" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-pv-progress-bar>
                        <span data-pv-progress-fill></span>
                    </div>
                </div>
            </div>

            <div class="ui-result-screen" data-pv-result hidden>
                <span class="ui-result-screen__icon"><x-admin.icon name="check" /></span>
                <h2 data-pv-result-title>0 PV enregistrés et archivés</h2>
                <p class="admin-muted">Les lignes enregistrées sont consultables dans le registre.</p>
                <ul class="ui-confirm-list" data-pv-result-failures hidden></ul>
                <div class="admin-section__actions">
                    <x-ui.btn :href="$indexRoute" variant="secondary">Voir le registre</x-ui.btn>
                    <x-ui.btn variant="secondary" data-pv-retry hidden>Réessayer ces lignes</x-ui.btn>
                    <x-ui.btn variant="primary" data-pv-new-import>Nouvel import</x-ui.btn>
                </div>
            </div>

            <div class="ui-sticky-actions" data-pv-step2-actions>
                <span class="ui-sticky-actions__count" data-pv-count aria-live="polite">0 prêts, 0 à compléter</span>
                <x-ui.btn variant="secondary" icon="chevron-left" data-pv-back>Retour</x-ui.btn>
                <x-ui.btn :href="$indexRoute" variant="ghost">Annuler</x-ui.btn>
                <x-ui.btn variant="primary" data-pv-save disabled>
                    <span data-pv-save-label>Enregistrer tous les PV (0)</span>
                </x-ui.btn>
            </div>
        </section>

        <template data-pv-file-row-template>
            <div class="ui-file-row">
                <span class="ui-file-row__icon"><x-admin.icon name="pdf" /></span>
                <span class="ui-file-row__name" data-pv-file-name title=""></span>
                <span class="ui-file-row__meta" data-pv-file-size></span>
                <button class="admin-icon-button admin-icon-button--small" type="button" data-pv-file-remove
                        aria-label="Retirer le fichier"><x-admin.icon name="close" /></button>
            </div>
        </template>

        <template data-pv-reject-row-template>
            <div class="ui-file-row__reject">
                <span><strong data-pv-reject-name></strong> — <span data-pv-reject-reason></span></span>
                <x-ui.badge label="Rejeté" tone="red" />
            </div>
        </template>

        <template data-pv-row-template>
            <tr data-pv-row>
                <td class="col-file">
                    <label class="admin-checkbox">
                        <input type="checkbox" data-pv-row-check aria-label="Sélectionner cette ligne">
                    </label>
                </td>
                <td data-pv-cell-index></td>
                <td class="col-file" style="left: 38px;">
                    <span class="ui-file-row__icon"><x-admin.icon name="pdf" /></span>
                    <span data-pv-cell-name title=""></span>
                    <span class="admin-table__subtext" data-pv-cell-size></span>
                    <button class="admin-icon-button admin-icon-button--small" type="button"
                            data-drawer-open="#pv-import-preview" data-pv-preview-file
                            aria-label="Aperçu du fichier"><x-admin.icon name="eye" /></button>
                </td>
                <td>
                    <select class="control-sm" data-pv-field="annee" aria-label="Année">
                        @foreach ($annees as $annee)
                            <option value="{{ $annee }}">{{ $annee }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="control-sm" data-pv-field="filiere" aria-label="Filière">
                        @foreach ($filieres as $filiere)
                            <option value="{{ $filiere['code'] }}">{{ $filiere['code'] }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="control-sm" data-pv-field="niveau" aria-label="Niveau">
                        @foreach ($niveaux as $niveau)
                            <option value="{{ $niveau }}">{{ $niveau }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="control-sm" data-pv-field="session" aria-label="Session">
                        @foreach ($sessions as $session)
                            <option value="{{ $session }}">{{ $session }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input class="control-sm" type="date" data-pv-field="date" aria-label="Date de délibération"></td>
                <td><input class="control-sm" type="text" data-pv-field="president" aria-label="Président du jury"></td>
                <td><span class="pv-ref" data-pv-cell-ref title=""></span></td>
                <td>
                    <x-ui.badge label="À compléter" tone="gold" data-pv-cell-state />
                    <span class="admin-tiny admin-muted" data-pv-cell-note hidden>Vérifier la référence : doublon possible.</span>
                </td>
                <td>
                    <button class="admin-icon-button admin-icon-button--small" type="button" data-pv-row-reset
                            aria-label="Rétablir la ligne"><x-admin.icon name="refresh" /></button>
                </td>
            </tr>
        </template>
    </section>

    <x-ui.drawer id="pv-import-preview" title="Aperçu du fichier" subtitle="Aperçu simulé du scan" :wide="true">
        <x-slot:header>
            <x-ui.badge label="Aperçu" tone="blue" />
        </x-slot:header>
        <x-ui.viewer reference="PV-2026-IG-L3-0XX" :pages="5" />
        <x-slot:footer>
            <button class="admin-button admin-button--secondary admin-button--small" type="button" data-drawer-close>Fermer</button>
        </x-slot:footer>
    </x-ui.drawer>

    <button type="button" hidden data-modal-open data-target="pv-import-duplicates" data-pv-duplicate-trigger
            aria-label="Confirmer les doublons"></button>

    <div class="admin-modal" id="pv-import-duplicates" hidden role="dialog" aria-modal="true"
         aria-labelledby="pv-import-duplicates-title">
        <div class="admin-modal__head">
            <h2 class="admin-modal__title" id="pv-import-duplicates-title">Doublons possibles</h2>
            <p class="admin-drawer__subtitle">Vérifiez les références avant de poursuivre.</p>
        </div>
        <div class="admin-modal__body">
            <div class="ui-notice">
                <x-admin.icon name="alert" />
                <span data-pv-duplicate-count>0 lignes en doublon : enregistrer quand même ?</span>
            </div>
            <ul class="ui-confirm-list" data-pv-duplicate-list></ul>
        </div>
        <div class="admin-modal__footer">
            <button class="admin-button admin-button--secondary admin-button--small" type="button" data-overlay-close>Annuler</button>
            <button class="admin-button admin-button--small" type="button" data-pv-confirm-save data-overlay-close>Enregistrer quand même</button>
        </div>
    </div>
@endsection

@pushOnce('scripts', 'shared-pv-import')
    <script>
        (function () {
            var root = document.querySelector('[data-pv-import]');

            if (!root) {
                return;
            }

            var fileInput = root.querySelector('[data-pv-file-input]');
            var dropzone = root.querySelector('[data-pv-dropzone]');
            var filePick = root.querySelector('[data-pv-file-pick]');
            var summary = root.querySelector('[data-pv-summary]');
            var filesContainer = root.querySelector('[data-pv-files]');
            var rejectedContainer = root.querySelector('[data-pv-rejected]');
            var fileRowTemplate = root.querySelector('[data-pv-file-row-template]');
            var rejectTemplate = root.querySelector('[data-pv-reject-row-template]');
            var rowTemplate = root.querySelector('[data-pv-row-template]');
            var rowsBody = root.querySelector('[data-pv-rows]');
            var continueButton = root.querySelector('[data-pv-continue]');
            var backButton = root.querySelector('[data-pv-back]');
            var saveButton = root.querySelector('[data-pv-save]');
            var saveLabel = root.querySelector('[data-pv-save-label]');
            var countLabel = root.querySelector('[data-pv-count]');
            var applyButton = root.querySelector('[data-pv-apply]');
            var applyLabel = root.querySelector('[data-pv-apply-label]');
            var commonFields = Array.prototype.slice.call(root.querySelectorAll('[data-pv-common]'));
            var stepPanels = Array.prototype.slice.call(root.querySelectorAll('[data-pv-step-panel]'));
            var stepIndicators = Array.prototype.slice.call(root.querySelectorAll('[data-pv-step-indicator]'));
            var progress = root.querySelector('[data-pv-progress]');
            var progressText = root.querySelector('[data-pv-progress-text]');
            var progressBar = root.querySelector('[data-pv-progress-bar]');
            var progressFill = root.querySelector('[data-pv-progress-fill]');
            var result = root.querySelector('[data-pv-result]');
            var resultTitle = root.querySelector('[data-pv-result-title]');
            var failuresList = root.querySelector('[data-pv-result-failures]');
            var retryButton = root.querySelector('[data-pv-retry]');
            var newImportButton = root.querySelector('[data-pv-new-import]');
            var step2Actions = root.querySelector('[data-pv-step2-actions]');
            var duplicateTrigger = root.querySelector('[data-pv-duplicate-trigger]');
            var duplicateCount = root.querySelector('[data-pv-duplicate-count]');
            var duplicateList = root.querySelector('[data-pv-duplicate-list]');
            var confirmSaveButton = root.querySelector('[data-pv-confirm-save]');
            var acceptedFiles = [];
            var rejectedFiles = [];
            var nextId = 1;
            var lastFailures = [];
            var pendingRows = [];
            var maxFiles = 100;
            var maxFileSize = 20 * 1024 * 1024;

            function formatSize(bytes) {
                var mo = bytes / (1024 * 1024);
                return mo.toFixed(1).replace('.', ',') + ' Mo';
            }

            function setStep(step) {
                stepPanels.forEach(function (panel) {
                    panel.hidden = panel.getAttribute('data-pv-step-panel') !== String(step);
                });

                stepIndicators.forEach(function (indicator) {
                    var current = indicator.getAttribute('data-pv-step-indicator') === String(step);
                    indicator.classList.toggle('is-current', current);

                    if (current) {
                        indicator.setAttribute('aria-current', 'step');
                    } else {
                        indicator.removeAttribute('aria-current');
                    }
                });
            }

            function ingestFiles(fileList) {
                Array.prototype.slice.call(fileList).forEach(function (file) {
                    if (acceptedFiles.length + rejectedFiles.length >= maxFiles) {
                        rejectedFiles.push({ name: file.name, reason: 'Limite de 100 fichiers atteinte' });
                    } else if (!/\.(pdf|jpe?g|png)$/i.test(file.name)) {
                        rejectedFiles.push({ name: file.name, reason: 'Format non accepté' });
                    } else if (file.size > maxFileSize) {
                        rejectedFiles.push({ name: file.name, reason: 'Fichier supérieur à 20 Mo' });
                    } else {
                        acceptedFiles.push({ id: nextId++, name: file.name, size: file.size });
                    }
                });

                renderStepOne();
            }

            function renderStepOne() {
                var totalBytes = acceptedFiles.reduce(function (sum, file) {
                    return sum + file.size;
                }, 0);

                summary.textContent = acceptedFiles.length + ' fichiers sélectionnés (' + formatSize(totalBytes) + ')';
                filesContainer.textContent = '';
                rejectedContainer.textContent = '';

                acceptedFiles.forEach(function (file) {
                    var fragment = fileRowTemplate.content.cloneNode(true);
                    var name = fragment.querySelector('[data-pv-file-name]');

                    name.textContent = file.name;
                    name.setAttribute('title', file.name);
                    fragment.querySelector('[data-pv-file-size]').textContent = formatSize(file.size);
                    fragment.querySelector('[data-pv-file-remove]').setAttribute('data-pv-file-id', String(file.id));
                    fragment.querySelector('[data-pv-file-remove]').setAttribute('aria-label', 'Retirer ' + file.name);
                    filesContainer.appendChild(fragment);
                });

                rejectedFiles.forEach(function (file) {
                    var fragment = rejectTemplate.content.cloneNode(true);

                    fragment.querySelector('[data-pv-reject-name]').textContent = file.name;
                    fragment.querySelector('[data-pv-reject-reason]').textContent = file.reason;
                    rejectedContainer.appendChild(fragment);
                });

                continueButton.disabled = acceptedFiles.length === 0;
            }

            function defaultsForIndex(index) {
                return {
                    annee: '2025-2026',
                    filiere: 'IG',
                    niveau: 'L3',
                    session: index % 4 === 3 ? 'Rattrapage' : 'Normale',
                    date: index % 3 === 1 ? '' : '2026-07-14',
                    president: index % 4 === 2 ? '' : 'Pr Aurélien Kossi',
                };
            }

            function buildReference(row) {
                var annee = row.querySelector('[data-pv-field="annee"]').value;
                var filiere = row.querySelector('[data-pv-field="filiere"]').value;
                var niveau = row.querySelector('[data-pv-field="niveau"]').value;
                var index = row.getAttribute('data-pv-row-index') || '0';
                var suffix = String(Number(index) + 1).padStart(3, '0');
                var year = annee.slice(-4) || '2026';

                return 'PV-' + year + '-' + filiere + '-' + niveau + '-' + suffix;
            }

            function updateRowState(row) {
                var missing = false;

                row.querySelectorAll('[data-pv-field]').forEach(function (field) {
                    var isMissing = field.value.trim() === '';
                    field.classList.toggle('is-missing', isMissing);

                    if (isMissing) {
                        missing = true;
                    }
                });

                var duplicate = row.getAttribute('data-pv-duplicate') === 'true';
                var state = row.querySelector('[data-pv-cell-state]');
                var note = row.querySelector('[data-pv-cell-note]');
                var label = missing ? 'À compléter' : (duplicate ? 'Doublon possible' : 'Prêt');
                var tone = missing || duplicate ? 'gold' : 'blue';

                state.textContent = label;
                state.classList.remove('admin-badge--neutral', 'admin-badge--blue', 'admin-badge--dark', 'admin-badge--gold', 'admin-badge--red');
                state.classList.add('admin-badge--' + tone);
                note.hidden = !duplicate;

                var reference = row.querySelector('[data-pv-cell-ref]');
                reference.textContent = buildReference(row);
                reference.setAttribute('title', reference.textContent);
            }

            function allRows() {
                return Array.prototype.slice.call(rowsBody.querySelectorAll('[data-pv-row]'));
            }

            function updateCounts() {
                var rows = allRows();
                var incomplete = rows.filter(function (row) {
                    return row.querySelector('[data-pv-cell-state]').textContent === 'À compléter';
                }).length;
                var ready = rows.length - incomplete;
                var checked = rows.filter(function (row) {
                    return row.querySelector('[data-pv-row-check]').checked;
                }).length;

                countLabel.textContent = ready + ' prêts, ' + incomplete + ' à compléter';
                saveLabel.textContent = 'Enregistrer tous les PV (' + ready + ')';
                saveButton.disabled = incomplete > 0 || rows.length === 0;
                applyLabel.textContent = checked > 0
                    ? 'Appliquer aux ' + checked + ' lignes cochées'
                    : 'Appliquer à toutes les lignes';
            }

            function renderRows() {
                rowsBody.textContent = '';

                acceptedFiles.forEach(function (file, index) {
                    var fragment = rowTemplate.content.cloneNode(true);
                    var row = fragment.querySelector('[data-pv-row]');
                    var defaults = defaultsForIndex(index);

                    row.setAttribute('data-pv-row-index', String(index));
                    row.setAttribute('data-pv-duplicate', index % 5 === 4 ? 'true' : 'false');
                    row.querySelector('[data-pv-cell-index]').textContent = String(index + 1);
                    row.querySelector('[data-pv-cell-name]').textContent = file.name;
                    row.querySelector('[data-pv-cell-name]').setAttribute('title', file.name);
                    row.querySelector('[data-pv-cell-size]').textContent = formatSize(file.size);
                    row.querySelector('[data-pv-preview-file]').setAttribute('aria-label', 'Aperçu de ' + file.name);
                    row.querySelector('[data-pv-row-check]').setAttribute('aria-label', 'Sélectionner ' + file.name);

                    Object.keys(defaults).forEach(function (key) {
                        var field = row.querySelector('[data-pv-field="' + key + '"]');

                        if (field) {
                            field.value = defaults[key];
                        }
                    });

                    updateRowState(row);
                    rowsBody.appendChild(fragment);
                });

                updateCounts();
            }

            function showResult(rows, isRetry) {
                progress.hidden = true;
                result.hidden = false;
                step2Actions.hidden = true;

                failuresList.textContent = '';
                lastFailures = [];

                if (!isRetry && rows.length > 1) {
                    lastFailures = rows.slice(-2).map(function (row) {
                        return {
                            name: row.querySelector('[data-pv-cell-name]').textContent,
                            reference: row.querySelector('[data-pv-cell-ref]').textContent,
                        };
                    });
                }

                var saved = rows.length - lastFailures.length;

                resultTitle.textContent = saved + ' PV enregistrés et archivés';
                retryButton.hidden = lastFailures.length === 0;
                failuresList.hidden = lastFailures.length === 0;

                lastFailures.forEach(function (failure) {
                    var item = document.createElement('li');
                    item.textContent = failure.name + ' — ' + failure.reference;
                    failuresList.appendChild(item);
                });
            }

            function runSave(rows, isRetry) {
                if (rows.length === 0) {
                    return;
                }

                result.hidden = true;
                progress.hidden = false;
                step2Actions.hidden = true;
                progressFill.style.transform = 'scaleX(0)';
                progressBar.setAttribute('aria-valuenow', '0');

                var total = rows.length;
                var current = 0;
                var timer = window.setInterval(function () {
                    current += 1;
                    progressText.textContent = 'Enregistrement ' + current + ' / ' + total;
                    progressFill.style.transform = 'scaleX(' + (current / total) + ')';
                    progressBar.setAttribute('aria-valuenow', String(Math.round((current / total) * 100)));

                    if (current >= total) {
                        window.clearInterval(timer);
                        showResult(rows, isRetry);
                    }
                }, 90);
            }

            if (filePick) {
                filePick.addEventListener('click', function () {
                    fileInput.click();
                });
            }

            if (dropzone) {
                dropzone.addEventListener('click', function (event) {
                    if (!event.target.closest('[data-pv-file-pick]')) {
                        fileInput.click();
                    }
                });

                dropzone.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        fileInput.click();
                    }
                });

                ['dragenter', 'dragover'].forEach(function (type) {
                    dropzone.addEventListener(type, function (event) {
                        event.preventDefault();
                        dropzone.classList.add('is-dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(function (type) {
                    dropzone.addEventListener(type, function (event) {
                        event.preventDefault();
                        dropzone.classList.remove('is-dragover');
                    });
                });

                dropzone.addEventListener('drop', function (event) {
                    if (event.dataTransfer && event.dataTransfer.files.length > 0) {
                        ingestFiles(event.dataTransfer.files);
                    }
                });
            }

            fileInput.addEventListener('change', function () {
                ingestFiles(fileInput.files);
            });

            filesContainer.addEventListener('click', function (event) {
                var removeButton = event.target.closest('[data-pv-file-remove]');

                if (!removeButton) {
                    return;
                }

                var id = Number(removeButton.getAttribute('data-pv-file-id'));
                acceptedFiles = acceptedFiles.filter(function (file) {
                    return file.id !== id;
                });
                renderStepOne();
            });

            continueButton.addEventListener('click', function () {
                setStep(2);
                renderRows();
            });

            backButton.addEventListener('click', function () {
                setStep(1);
            });

            rowsBody.addEventListener('change', function (event) {
                if (!event.target.closest('[data-pv-field]') && !event.target.closest('[data-pv-row-check]')) {
                    return;
                }

                var row = event.target.closest('[data-pv-row]');

                if (row && event.target.closest('[data-pv-field]')) {
                    updateRowState(row);
                }

                updateCounts();
            });

            rowsBody.addEventListener('input', function (event) {
                if (!event.target.closest('[data-pv-field="president"]')) {
                    return;
                }

                updateRowState(event.target.closest('[data-pv-row]'));
                updateCounts();
            });

            rowsBody.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter') {
                    return;
                }

                var field = event.target.closest('[data-pv-field]');

                if (!field) {
                    return;
                }

                event.preventDefault();
                var nextRow = field.closest('[data-pv-row]').nextElementSibling;

                if (nextRow) {
                    var target = nextRow.querySelector('[data-pv-field="' + field.getAttribute('data-pv-field') + '"]');

                    if (target) {
                        target.focus();
                    }
                }
            });

            rowsBody.addEventListener('click', function (event) {
                var resetButton = event.target.closest('[data-pv-row-reset]');

                if (!resetButton) {
                    return;
                }

                var row = resetButton.closest('[data-pv-row]');
                var index = Number(row.getAttribute('data-pv-row-index'));
                var defaults = defaultsForIndex(index);

                Object.keys(defaults).forEach(function (key) {
                    var field = row.querySelector('[data-pv-field="' + key + '"]');

                    if (field) {
                        field.value = defaults[key];
                    }
                });

                updateRowState(row);
                updateCounts();
            });

            applyButton.addEventListener('click', function () {
                var rows = allRows();
                var checked = rows.filter(function (row) {
                    return row.querySelector('[data-pv-row-check]').checked;
                });
                var targets = checked.length > 0 ? checked : rows;

                commonFields.forEach(function (field) {
                    var value = field.value.trim();
                    var key = field.getAttribute('data-pv-common');

                    if (value === '') {
                        return;
                    }

                    targets.forEach(function (row) {
                        var target = row.querySelector('[data-pv-field="' + key + '"]');

                        if (target) {
                            target.value = value;
                        }
                    });
                });

                rows.forEach(updateRowState);
                updateCounts();
            });

            saveButton.addEventListener('click', function () {
                var rows = allRows();

                pendingRows = rows;
                var duplicates = rows.filter(function (row) {
                    return row.getAttribute('data-pv-duplicate') === 'true';
                });

                if (duplicates.length > 0) {
                    duplicateCount.textContent = duplicates.length + ' lignes en doublon : enregistrer quand même ?';
                    duplicateList.textContent = '';
                    duplicates.forEach(function (row) {
                        var item = document.createElement('li');
                        item.textContent = row.querySelector('[data-pv-cell-ref]').textContent;
                        duplicateList.appendChild(item);
                    });
                    duplicateTrigger.click();
                    return;
                }

                runSave(rows, false);
            });

            confirmSaveButton.addEventListener('click', function () {
                runSave(pendingRows, false);
            });

            retryButton.addEventListener('click', function () {
                runSave(lastFailures.length > 0 ? lastFailures : pendingRows, true);
            });

            newImportButton.addEventListener('click', function () {
                acceptedFiles = [];
                rejectedFiles = [];
                lastFailures = [];
                pendingRows = [];
                fileInput.value = '';
                rowsBody.textContent = '';
                filesContainer.textContent = '';
                rejectedContainer.textContent = '';
                progressFill.style.transform = 'scaleX(0)';
                progress.hidden = true;
                result.hidden = true;
                step2Actions.hidden = false;
                setStep(1);
                renderStepOne();
                commonFields.forEach(function (field) {
                    field.value = '';
                });
                updateCounts();
            });

            document.addEventListener('click', function (event) {
                var previewButton = event.target.closest('[data-pv-preview-file]');

                if (!previewButton) {
                    return;
                }

                var row = previewButton.closest('[data-pv-row]');
                var title = document.getElementById('pv-import-preview-title');

                if (row && title) {
                    title.textContent = 'Aperçu — ' + row.querySelector('[data-pv-cell-name]').textContent;
                }
            }, true);

            renderStepOne();
            updateCounts();
        })();
    </script>
@endpushonce
