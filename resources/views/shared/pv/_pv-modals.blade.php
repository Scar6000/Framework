@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $isArchived = ($pv['status'] ?? '') === 'Archivé';
    $isDraft = ($pv['status'] ?? '') === 'Brouillon';
    $isAdmin = $roleKey === 'admin';
    $isAgent = $roleKey === 'agent';
    $modalSubtitle = $pv['reference'].' · '.($pv['filiere'] ?? '').' '.($pv['niveau'] ?? '');
@endphp

@if ($isArchived && $isAdmin)
    <div class="admin-modal" id="pv-correct-{{ $pvIndex }}" hidden role="dialog" aria-modal="true"
         aria-labelledby="pv-correct-title-{{ $pvIndex }}">
        <div class="admin-modal__head">
            <h2 class="admin-modal__title" id="pv-correct-title-{{ $pvIndex }}">Corriger le PV</h2>
            <p class="admin-drawer__subtitle">{{ $modalSubtitle }}</p>
        </div>
        <div class="admin-modal__body">
            <form id="pv-correct-form-{{ $pvIndex }}" action="{{ $indexRoute }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="reference" value="{{ $pv['reference'] }}">
                <input type="hidden" name="action" value="correction">

                <div class="ui-notice">
                    <x-admin.icon name="info" />
                    <span>La correction d'un PV archivé est tracée dans le journal d'audit.</span>
                </div>

                <div class="admin-form-grid">
                    <div class="admin-field admin-form-grid__full{{ $errors->has('motif') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="pv-correct-motif-{{ $pvIndex }}">Motif de la correction (obligatoire)</label>
                        <textarea class="admin-textarea" id="pv-correct-motif-{{ $pvIndex }}" name="motif" required
                                  aria-invalid="@error('motif') true @else false @enderror"
                                  aria-describedby="pv-correct-help-{{ $pvIndex }} @error('motif') pv-correct-error-{{ $pvIndex }} @enderror"
                                  placeholder="Indiquez la donnée à rectifier et la valeur attendue."></textarea>
                        <p class="admin-tiny admin-muted" id="pv-correct-help-{{ $pvIndex }}">Le motif reste associé à la correction dans le journal.</p>
                        @error('motif')
                            <p class="admin-field__error" id="pv-correct-error-{{ $pvIndex }}">
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
            <button class="admin-button admin-button--small" type="submit" form="pv-correct-form-{{ $pvIndex }}">Enregistrer la correction</button>
        </div>
    </div>

    <div class="admin-modal" id="pv-unlock-{{ $pvIndex }}" hidden role="dialog" aria-modal="true"
         aria-labelledby="pv-unlock-title-{{ $pvIndex }}">
        <div class="admin-modal__head">
            <h2 class="admin-modal__title" id="pv-unlock-title-{{ $pvIndex }}">Déverrouiller le PV</h2>
            <p class="admin-drawer__subtitle">{{ $modalSubtitle }}</p>
        </div>
        <div class="admin-modal__body">
            <form id="pv-unlock-form-{{ $pvIndex }}" action="{{ $indexRoute }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="reference" value="{{ $pv['reference'] }}">
                <input type="hidden" name="action" value="unlock">

                <div class="ui-notice">
                    <x-admin.icon name="unlock" />
                    <span>Le dossier redevient modifiable. Le déverrouillage est tracé dans le journal d'audit.</span>
                </div>

                <div class="admin-form-grid">
                    <div class="admin-field admin-form-grid__full{{ $errors->has('motif') ? ' admin-field--invalid' : '' }}">
                        <label class="admin-field__label" for="pv-unlock-motif-{{ $pvIndex }}">Motif du déverrouillage (obligatoire)</label>
                        <textarea class="admin-textarea" id="pv-unlock-motif-{{ $pvIndex }}" name="motif" required
                                  aria-invalid="@error('motif') true @else false @enderror"
                                  aria-describedby="pv-unlock-help-{{ $pvIndex }} @error('motif') pv-unlock-error-{{ $pvIndex }} @enderror"
                                  placeholder="Précisez la raison du déverrouillage."></textarea>
                        <p class="admin-tiny admin-muted" id="pv-unlock-help-{{ $pvIndex }}">Une correction sur dossier archivé reste soumise à motif.</p>
                        @error('motif')
                            <p class="admin-field__error" id="pv-unlock-error-{{ $pvIndex }}">
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
            <button class="admin-button admin-button--small" type="submit" form="pv-unlock-form-{{ $pvIndex }}">Confirmer le déverrouillage</button>
        </div>
    </div>
@endif

@if ($isDraft && ($isAdmin || $isAgent))
    <div class="admin-modal" id="pv-archive-{{ $pvIndex }}" hidden role="dialog" aria-modal="true"
         aria-labelledby="pv-archive-title-{{ $pvIndex }}">
        <div class="admin-modal__head">
            <h2 class="admin-modal__title" id="pv-archive-title-{{ $pvIndex }}">Archiver le PV</h2>
            <p class="admin-drawer__subtitle">{{ $modalSubtitle }}</p>
        </div>
        <div class="admin-modal__body">
            <form id="pv-archive-form-{{ $pvIndex }}" action="{{ $indexRoute }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="reference" value="{{ $pv['reference'] }}">
                <input type="hidden" name="action" value="archive">

                <div class="ui-notice">
                    <x-admin.icon name="archive" />
                    <span>Le dossier {{ $pv['reference'] }} sera classé dans les archives après confirmation.</span>
                </div>
            </form>
        </div>
        <div class="admin-modal__footer">
            <button class="admin-button admin-button--secondary admin-button--small" type="button" data-overlay-close>Annuler</button>
            <button class="admin-button admin-button--small" type="submit" form="pv-archive-form-{{ $pvIndex }}">Archiver le PV</button>
        </div>
    </div>
@endif
