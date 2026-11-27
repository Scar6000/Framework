@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $mode = $mode ?? 'create';
    $isEdit = $mode === 'edit';
    $roleKey = $roleKey ?? 'admin';
    $pv = $pv ?? null;
    $filiere = $filiere ?? null;
    $promotion = $promotion ?? null;
    $reference = $reference ?? ($pv?->reference ?? '');
    $isArchived = $isArchived ?? false;
    $isAdmin = $roleKey === 'admin';
    $canEdit = $canEdit ?? ! ($isArchived && ! $isAdmin);
    $sessions = $sessions ?? ['Normale', 'Rattrapage'];
    $annees = $annees ?? [];
    $pageCount = 5;

    $anneeLabel = $promotion?->annee->libelle ?? ($annees[0] ?? '');
    $filiereLabel = $filiere?->nom ?? ($promotion?->filiere?->nom ?? '');
    $filiereCode = $filiere?->code ?? ($promotion?->filiere?->code ?? '');
    $niveauCode = $promotion?->semestre?->niveau?->code ?? '';
    $filiereId = $filiere?->id ?? $promotion?->filiere_id;
    $promotionId = $promotion?->id;

    $defaultSession = old('session', $isEdit && $pv?->session === 'rattrapage' ? 'Rattrapage' : 'Normale');
    $membres = old('membres_jury', $pv?->membres_jury ?? []);
    $dateValue = old('date_deliberation', $pv?->date_deliberation?->toDateString() ?? '');
    $presidentValue = old('president_jury', $pv?->president_jury ?? '');

    $indexRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.pv.index')
        ? route($roleKey.'.pv.index')
        : route('admin.pv.index');
    $formAction = $isEdit
        ? route($roleKey.'.pv.update', $reference)
        : route($roleKey.'.pv.store');
@endphp

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" data-pv-form novalidate>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="filiere_id" value="{{ $filiereId }}">
    <input type="hidden" name="promotion_id" value="{{ $promotionId }}">

    <div class="ui-notice">
        <x-admin.icon name="info" />
        <span>
            PV annuel — Filière <strong>{{ $filiereLabel }} ({{ $filiereCode }})</strong>,
            niveau <strong>{{ $niveauCode }}</strong>, année <strong>{{ $anneeLabel }}</strong>.
            Référence attribuée : <x-ui.pv-reference :reference="$reference" />.
        </span>
    </div>

    <div class="ui-pv-layout">
        <div class="ui-form-stack">
            <div class="admin-card admin-card--flush">
                <div class="admin-card__head">
                    <h2 class="admin-card__title">Document du PV</h2>
                    <span class="admin-tiny admin-muted">PDF, JPG ou PNG · 20 Mo maximum</span>
                </div>
                <div class="admin-card__body ui-form-stack">
                    <div class="ui-dropzone" data-pv-dropzone role="button" tabindex="0"
                         aria-label="Sélectionner le scan du procès-verbal">
                        <span class="ui-dropzone__icon"><x-admin.icon name="upload" /></span>
                        <span class="ui-dropzone__title">Déposez le scan du PV ici</span>
                        <span class="ui-dropzone__hint">ou choisissez un fichier sur votre poste</span>
                        <button class="admin-button admin-button--secondary admin-button--small" type="button"
                                data-pv-file-pick>Parcourir les fichiers</button>
                        <input class="sr-only" type="file" id="pv-scan" name="fichier" accept=".pdf,.jpg,.jpeg,.png"
                               @if (! $isEdit) required @endif
                               data-pv-file-input aria-label="Scan du procès-verbal">
                    </div>

                    <div class="ui-file-row" data-pv-file-row hidden>
                        <span class="ui-file-row__icon"><x-admin.icon name="pdf" /></span>
                        <span class="ui-file-row__name" data-pv-file-name title=""></span>
                        <span class="ui-file-row__meta" data-pv-file-size></span>
                        <button class="admin-icon-button admin-icon-button--small" type="button" data-pv-file-remove
                                aria-label="Retirer le fichier sélectionné"><x-admin.icon name="close" /></button>
                    </div>

                    <p class="admin-tiny admin-muted" data-pv-file-caption>
                        {{ $isEdit ? 'Scan enregistré : '.$reference.'.pdf. Laissez vide pour conserver le fichier actuel.' : 'Le scan est obligatoire pour enregistrer le PV.' }}
                    </p>

                    @error('fichier')
                        <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="admin-card admin-card--flush">
                <div class="admin-card__head">
                    <h2 class="admin-card__title">Informations du PV</h2>
                    <span class="admin-tiny admin-muted">Champs obligatoires sauf les membres du jury</span>
                </div>
                <div class="admin-card__body">
                    <div class="admin-form-grid">
                        <div class="admin-field">
                            <span class="admin-field__label" id="pv-session-label">Session</span>
                            <div class="ui-segmented" role="group" aria-labelledby="pv-session-label">
                                @foreach ($sessions as $session)
                                    <button class="ui-segmented__option" type="button" data-pv-session="{{ $session }}"
                                            aria-pressed="{{ $defaultSession === $session ? 'true' : 'false' }}">{{ $session }}</button>
                                @endforeach
                            </div>
                            <input type="hidden" name="session" value="{{ $defaultSession }}" data-pv-session-input>
                            @error('session')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field{{ $errors->has('date_deliberation') ? ' admin-field--invalid' : '' }}">
                            <label class="admin-field__label" for="pv-date">Date de délibération</label>
                            <input class="admin-input" id="pv-date" name="date_deliberation" type="date"
                                   value="{{ $dateValue }}" aria-invalid="@error('date_deliberation') true @else false @enderror">
                            @error('date_deliberation')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field{{ $errors->has('president_jury') ? ' admin-field--invalid' : '' }} admin-form-grid__full">
                            <label class="admin-field__label" for="pv-president">Président du jury</label>
                            <input class="admin-input" id="pv-president" name="president_jury" type="text"
                                   placeholder="Nom et titre du président" value="{{ $presidentValue }}"
                                   aria-invalid="@error('president_jury') true @else false @enderror">
                            @error('president_jury')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field admin-form-grid__full{{ $errors->has('membres_jury') ? ' admin-field--invalid' : '' }}">
                            <span class="admin-field__label" id="pv-membres-label">Membres du jury</span>
                            <div class="ui-tag-list" data-pv-tags aria-labelledby="pv-membres-label">
                                @foreach ($membres as $membre)
                                    <span class="ui-tag">
                                        <span>{{ $membre }}</span>
                                        <input type="hidden" name="membres_jury[]" value="{{ $membre }}">
                                        <button class="ui-tag__remove" type="button" data-pv-tag-remove
                                                aria-label="Retirer {{ $membre }}"><x-admin.icon name="close" /></button>
                                    </span>
                                @endforeach
                            </div>
                            <div class="admin-filters">
                                <input class="admin-input" type="text" data-pv-tag-input
                                       placeholder="Ajouter un membre du jury" aria-label="Nouveau membre du jury">
                                <x-ui.btn variant="secondary" icon="plus" data-pv-tag-add>Ajouter</x-ui.btn>
                            </div>
                            <p class="admin-tiny admin-muted">Au moins un membre est requis pour archiver le PV.</p>
                            @error('membres_jury')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                            <template data-pv-tag-template>
                                <span class="ui-tag">
                                    <span data-pv-tag-label></span>
                                    <input type="hidden" name="membres_jury[]" value="" data-pv-tag-value>
                                    <button class="ui-tag__remove" type="button" data-pv-tag-remove aria-label="Retirer le membre">
                                        <x-admin.icon name="close" />
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>

                    @if ($isEdit && $isArchived)
                        <div class="ui-notice">
                            <x-admin.icon name="info" />
                            <span>PV archivé : toute modification est tracée dans le journal d'audit.</span>
                        </div>
                        <div class="admin-field admin-form-grid__full{{ $errors->has('motif') ? ' admin-field--invalid' : '' }}">
                            <label class="admin-field__label" for="pv-motif">Motif de la modification (obligatoire)</label>
                            <textarea class="admin-textarea" id="pv-motif" name="motif"
                                      placeholder="Indiquez la donnée à rectifier et la valeur attendue."
                                      aria-invalid="@error('motif') true @else false @enderror">{{ old('motif') }}</textarea>
                            @error('motif')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="ui-form-stack">
            <div class="admin-card admin-card--flush is-hoverable-card">
                <div class="admin-card__head">
                    <h2 class="admin-card__title">Aperçu du document</h2>
                    <span class="admin-tiny admin-muted">{{ $pageCount }} pages</span>
                </div>
                <div class="admin-card__body ui-form-stack">
                    <x-ui.viewer :reference="$reference" :pages="$pageCount" :can-download="false" />
                </div>
            </div>
        </div>
    </div>

    <div class="ui-sticky-actions">
        <x-ui.btn :href="$indexRoute" variant="ghost">Annuler</x-ui.btn>
        @if ($canEdit)
            <x-ui.btn variant="secondary" type="submit" name="action" value="draft" data-pv-submit="draft">
                <span data-pv-submit-label>Enregistrer comme brouillon</span>
            </x-ui.btn>
            <x-ui.btn variant="primary" type="submit" name="action" value="archive" data-pv-submit="archive">
                <span data-pv-submit-label>Enregistrer et archiver</span>
            </x-ui.btn>
        @else
            <x-ui.btn variant="secondary" type="button" disabled>Modification réservée à l'administrateur</x-ui.btn>
        @endif
    </div>
</form>

@pushOnce('scripts', 'shared-pv-form')
    <script>
        (function () {
            var form = document.querySelector('[data-pv-form]');

            if (!form) {
                return;
            }

            var fileInput = form.querySelector('[data-pv-file-input]');
            var dropzone = form.querySelector('[data-pv-dropzone]');
            var filePick = form.querySelector('[data-pv-file-pick]');
            var fileRow = form.querySelector('[data-pv-file-row]');
            var fileName = form.querySelector('[data-pv-file-name]');
            var fileSize = form.querySelector('[data-pv-file-size]');
            var fileRemove = form.querySelector('[data-pv-file-remove]');
            var fileCaption = form.querySelector('[data-pv-file-caption]');
            var tagList = form.querySelector('[data-pv-tags]');
            var tagTemplate = form.querySelector('[data-pv-tag-template]');
            var tagInput = form.querySelector('[data-pv-tag-input]');
            var tagAdd = form.querySelector('[data-pv-tag-add]');
            var maxFileSize = 20 * 1024 * 1024;
            var initialCaption = fileCaption ? fileCaption.textContent.trim() : '';

            function formatSize(bytes) {
                if (bytes < 1024) {
                    return bytes + ' o';
                }

                if (bytes < 1024 * 1024) {
                    return (bytes / 1024).toFixed(1).replace('.', ',') + ' Ko';
                }

                return (bytes / (1024 * 1024)).toFixed(1).replace('.', ',') + ' Mo';
            }

            function setFile(file) {
                if (!file) {
                    return;
                }

                var extensionOk = /\.(pdf|jpe?g|png)$/i.test(file.name);

                if (!extensionOk || file.size > maxFileSize) {
                    fileCaption.textContent = 'Fichier refusé : utilisez un PDF, JPG ou PNG de 20 Mo maximum.';
                    return;
                }

                fileName.textContent = file.name;
                fileName.setAttribute('title', file.name);
                fileSize.textContent = formatSize(file.size);
                fileRow.hidden = false;
                fileCaption.textContent = 'Scan sélectionné : ' + file.name;
            }

            function clearFile() {
                fileInput.value = '';
                fileRow.hidden = true;
                fileCaption.textContent = initialCaption;
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
                        setFile(event.dataTransfer.files[0]);
                    }
                });
            }

            fileInput.addEventListener('change', function () {
                setFile(fileInput.files[0]);
            });

            fileRemove.addEventListener('click', clearFile);

            form.querySelectorAll('[data-pv-session]').forEach(function (button) {
                button.addEventListener('click', function () {
                    form.querySelectorAll('[data-pv-session]').forEach(function (item) {
                        item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
                    });
                    form.querySelector('[data-pv-session-input]').value = button.getAttribute('data-pv-session');
                });
            });

            function addTag() {
                var value = tagInput.value.trim();

                if (value === '') {
                    return;
                }

                var fragment = tagTemplate.content.cloneNode(true);
                var tag = fragment.querySelector('.ui-tag');

                tag.querySelector('[data-pv-tag-label]').textContent = value;
                tag.querySelector('[data-pv-tag-value]').value = value;
                tag.querySelector('[data-pv-tag-remove]').setAttribute('aria-label', 'Retirer ' + value);
                tagList.appendChild(fragment);
                tagInput.value = '';
                tagInput.focus();
            }

            tagAdd.addEventListener('click', addTag);

            tagInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    addTag();
                }
            });

            tagList.addEventListener('click', function (event) {
                var remove = event.target.closest('[data-pv-tag-remove]');

                if (remove) {
                    event.preventDefault();
                    var tag = remove.closest('.ui-tag');

                    if (tag) {
                        tag.remove();
                    }
                }
            });

            form.addEventListener('submit', function (event) {
                var submitter = event.submitter || form.querySelector('[data-pv-submit]:not([disabled])');

                if (!submitter || submitter.disabled) {
                    event.preventDefault();
                    return;
                }

                var label = submitter.querySelector('[data-pv-submit-label]');

                form.querySelectorAll('[data-pv-submit]').forEach(function (button) {
                    if (button !== submitter) {
                        button.disabled = true;
                    }
                });

                submitter.disabled = true;
                submitter.setAttribute('aria-busy', 'true');

                if (label) {
                    label.textContent = 'Enregistrement en cours…';
                }
            });

            window.addEventListener('pageshow', function () {
                form.querySelectorAll('[data-pv-submit]').forEach(function (button) {
                    button.disabled = false;
                    button.removeAttribute('aria-busy');
                    var label = button.querySelector('[data-pv-submit-label]');

                    if (label) {
                        label.textContent = button.getAttribute('data-pv-submit') === 'archive'
                            ? 'Enregistrer et archiver'
                            : 'Enregistrer comme brouillon';
                    }
                });
            });
        })();
    </script>
@endpushOnce
