@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $mode = $mode ?? 'create';
    $isEdit = $mode === 'edit';
    $roleKey = $roleKey ?? 'admin';
    $pv = $pv ?? [];
    $reference = $reference ?? ($pv['reference'] ?? 'PV-2026-IG-L3-0XX');
    $isArchived = $isArchived ?? (($pv['status'] ?? '') === 'Archivé');
    $isAdmin = $roleKey === 'admin';
    $canEdit = $canEdit ?? ! ($isArchived && ! $isAdmin);
    $annees = config('ui-demo.annees', []);
    $filieres = collect(config('ui-demo.filieres', []));
    $niveaux = config('ui-demo.niveaux', []);
    $sessions = config('ui-demo.sessions', []);
    $pageCount = max(1, (int) ($pv['pages'] ?? 5));
    $membres = old('membres', $pv['membres'] ?? []);
    $defaultSession = old('session', $pv['session'] ?? 'Normale');
    $formAction = $isEdit
        ? route($roleKey.'.pv.edit', $reference)
        : route($roleKey.'.pv.index');
    $fieldValue = function (string $key, string $default = '') use ($pv): string {
        return (string) old($key, $pv[$key] ?? $default);
    };
@endphp

<form method="POST" action="{{ $formAction }}" data-pv-form novalidate>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="reference" value="{{ $reference }}">

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
                        <input class="sr-only" type="file" id="pv-scan" name="scan" accept=".pdf,.jpg,.jpeg,.png"
                               data-pv-file-input aria-label="Scan du procès-verbal">
                    </div>

                    <div class="ui-file-row" data-pv-file-row hidden>
                        <span class="ui-file-row__icon"><x-admin.icon name="pdf" /></span>
                        <span class="ui-file-row__name" data-pv-file-name title=""></span>
                        <span class="ui-file-row__meta" data-pv-file-size></span>
                        <button class="admin-icon-button admin-icon-button--small" type="button" data-pv-file-remove
                                aria-label="Retirer le fichier sélectionné"><x-admin.icon name="close" /></button>
                    </div>

                    @error('scan')
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
                        <div class="admin-field{{ $errors->has('annee') ? ' admin-field--invalid' : '' }}">
                            <label class="admin-field__label" for="pv-annee">Année académique</label>
                            <select class="admin-select" id="pv-annee" name="annee" required data-pv-required
                                    aria-invalid="@error('annee') true @else false @enderror">
                                <option value="">Choisir une année</option>
                                @foreach ($annees as $annee)
                                    <option value="{{ $annee }}" @selected($fieldValue('annee') === $annee)>{{ $annee }}</option>
                                @endforeach
                            </select>
                            <p class="admin-field__error" data-pv-error hidden>Ce champ est obligatoire.</p>
                            @error('annee')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field{{ $errors->has('filiere') ? ' admin-field--invalid' : '' }}">
                            <label class="admin-field__label" for="pv-filiere">Filière</label>
                            <select class="admin-select" id="pv-filiere" name="filiere" required data-pv-required
                                    aria-invalid="@error('filiere') true @else false @enderror">
                                <option value="">Choisir une filière</option>
                                @foreach ($filieres as $filiere)
                                    <option value="{{ $filiere['code'] }}" @selected($fieldValue('filiere') === $filiere['code'])>
                                        {{ $filiere['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="admin-field__error" data-pv-error hidden>Ce champ est obligatoire.</p>
                            @error('filiere')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field{{ $errors->has('niveau') ? ' admin-field--invalid' : '' }}">
                            <label class="admin-field__label" for="pv-niveau">Niveau</label>
                            <select class="admin-select" id="pv-niveau" name="niveau" required data-pv-required
                                    aria-invalid="@error('niveau') true @else false @enderror">
                                <option value="">Choisir un niveau</option>
                                @foreach ($niveaux as $niveau)
                                    <option value="{{ $niveau }}" @selected($fieldValue('niveau') === $niveau)>{{ $niveau }}</option>
                                @endforeach
                            </select>
                            <p class="admin-field__error" data-pv-error hidden>Ce champ est obligatoire.</p>
                            @error('niveau')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field">
                            <span class="admin-field__label" id="pv-session-label">Session</span>
                            <div class="ui-segmented" role="group" aria-labelledby="pv-session-label">
                                @foreach ($sessions as $session)
                                    <button class="ui-segmented__option" type="button" data-pv-session="{{ $session }}"
                                            aria-pressed="{{ $defaultSession === $session ? 'true' : 'false' }}">{{ $session }}</button>
                                @endforeach
                            </div>
                            <input type="hidden" name="session" value="{{ $defaultSession }}" data-pv-session-input>
                        </div>

                        <div class="admin-field{{ $errors->has('date') ? ' admin-field--invalid' : '' }}">
                            <label class="admin-field__label" for="pv-date">Date de délibération</label>
                            <input class="admin-input" id="pv-date" name="date" type="date" required data-pv-required
                                   value="{{ $fieldValue('date') }}" aria-invalid="@error('date') true @else false @enderror">
                            <p class="admin-field__error" data-pv-error hidden>Ce champ est obligatoire.</p>
                            @error('date')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field{{ $errors->has('president') ? ' admin-field--invalid' : '' }}">
                            <label class="admin-field__label" for="pv-president">Président du jury</label>
                            <input class="admin-input" id="pv-president" name="president" type="text"
                                   placeholder="Nom et titre du président" required data-pv-required
                                   value="{{ $fieldValue('president') }}"
                                   aria-invalid="@error('president') true @else false @enderror">
                            <p class="admin-field__error" data-pv-error hidden>Ce champ est obligatoire.</p>
                            @error('president')
                                <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field admin-form-grid__full">
                            <span class="admin-field__label" id="pv-membres-label">Membres du jury</span>
                            <div class="ui-tag-list" data-pv-tags aria-labelledby="pv-membres-label">
                                @foreach ($membres as $membre)
                                    <span class="ui-tag">
                                        {{ $membre }}
                                        <input type="hidden" name="membres[]" value="{{ $membre }}">
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
                            <template data-pv-tag-template>
                                <span class="ui-tag">
                                    <span data-pv-tag-label></span>
                                    <input type="hidden" name="membres[]" value="" data-pv-tag-value>
                                    <button class="ui-tag__remove" type="button" data-pv-tag-remove aria-label="Retirer le membre">
                                        <x-admin.icon name="close" />
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>

                    <div class="ui-reference-line">
                        <x-ui.pv-reference :reference="$reference" />
                        <span class="admin-tiny admin-muted">attribuée à l'enregistrement</span>
                    </div>

                    @if ($isEdit && $isArchived)
                        @if ($isAdmin)
                            <div class="ui-notice">
                                <x-admin.icon name="info" />
                                <span>PV archivé : toute modification est tracée.</span>
                            </div>
                            <div class="admin-field{{ $errors->has('motif') ? ' admin-field--invalid' : '' }}">
                                <label class="admin-field__label" for="pv-motif">Motif de la modification (obligatoire)</label>
                                <textarea class="admin-textarea" id="pv-motif" name="motif" required data-pv-required
                                          placeholder="Indiquez la donnée à rectifier et la valeur attendue."
                                          aria-invalid="@error('motif') true @else false @enderror">{{ old('motif') }}</textarea>
                                <p class="admin-field__error" data-pv-error hidden>Le motif est obligatoire pour un PV archivé.</p>
                                @error('motif')
                                    <p class="admin-field__error"><x-admin.icon name="alert" /> {{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div class="ui-notice">
                                <x-admin.icon name="lock" />
                                <span>PV archivé : pour corriger, contactez l'administrateur.</span>
                            </div>
                        @endif
                    @endif

                    <div class="ui-notice ui-notice--success" data-pv-success role="status" hidden>
                        <x-admin.icon name="check" />
                        <span data-pv-success-text></span>
                    </div>
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
                    <x-ui.viewer :reference="$reference" :pages="$pageCount"
                                 :can-download="$isEdit && in_array($roleKey, ['admin', 'agent'], true)" />
                    <p class="admin-tiny admin-muted" data-pv-file-caption>
                        {{ $isEdit ? 'Scan enregistré : '.$reference.'.pdf' : 'Le scan associé apparaîtra ici après sélection.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-sticky-actions">
        <x-ui.btn :href="route($roleKey.'.pv.index')" variant="ghost">Annuler</x-ui.btn>
        <x-ui.btn variant="secondary" type="submit" data-pv-submit="draft" :disabled="! $canEdit"
                  aria-disabled="{{ $canEdit ? 'false' : 'true' }}">
            <span data-pv-submit-label>Enregistrer comme brouillon</span>
        </x-ui.btn>
        <x-ui.btn variant="primary" type="submit" data-pv-submit="archive" :disabled="! $canEdit"
                  aria-disabled="{{ $canEdit ? 'false' : 'true' }}">
            <span data-pv-submit-label>Enregistrer et archiver</span>
        </x-ui.btn>
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
            var success = form.querySelector('[data-pv-success]');
            var successText = form.querySelector('[data-pv-success-text]');
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
                event.preventDefault();

                var submitter = event.submitter || form.querySelector('[data-pv-submit]');
                var valid = true;
                var firstInvalid = null;

                form.querySelectorAll('[data-pv-required]').forEach(function (field) {
                    var wrapper = field.closest('.admin-field');
                    var empty = field.value.trim() === '';

                    if (empty) {
                        valid = false;

                        if (wrapper) {
                            wrapper.classList.add('admin-field--invalid');
                            var error = wrapper.querySelector('[data-pv-error]');

                            if (error) {
                                error.hidden = false;
                            }
                        }

                        field.setAttribute('aria-invalid', 'true');

                        if (!firstInvalid) {
                            firstInvalid = field;
                        }
                    } else {
                        field.setAttribute('aria-invalid', 'false');

                        if (wrapper) {
                            wrapper.classList.remove('admin-field--invalid');
                            var resolved = wrapper.querySelector('[data-pv-error]');

                            if (resolved) {
                                resolved.hidden = true;
                            }
                        }
                    }
                });

                if (!valid) {
                    success.hidden = true;

                    if (firstInvalid) {
                        firstInvalid.focus();
                    }

                    return;
                }

                var label = submitter.querySelector('[data-pv-submit-label]');

                if (submitter.disabled) {
                    return;
                }

                submitter.disabled = true;
                submitter.setAttribute('aria-busy', 'true');

                if (label) {
                    label.textContent = 'Enregistrement en cours…';
                }

                window.setTimeout(function () {
                    submitter.disabled = false;
                    submitter.removeAttribute('aria-busy');

                    if (label) {
                        label.textContent = submitter.getAttribute('data-pv-submit') === 'archive'
                            ? 'Enregistrer et archiver'
                            : 'Enregistrer comme brouillon';
                    }

                    successText.textContent = submitter.getAttribute('data-pv-submit') === 'archive'
                        ? 'PV enregistré et archivé (simulation).'
                        : 'PV enregistré comme brouillon (simulation).';
                    success.hidden = false;
                }, 650);
            });
        })();
    </script>
@endpushonce
