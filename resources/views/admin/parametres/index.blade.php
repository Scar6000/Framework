@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
    @php
        $selectedFormats = (array) old('formats', ['pdf', 'jpg', 'png']);
    @endphp

    <x-admin.page-header
        title="Paramètres"
        subtitle="Configurez les dépôts de fichiers, la sécurité des accès et la conservation du journal."
        :breadcrumb="['Accueil', 'Administration', 'Paramètres']"
    />

    <section class="admin-section" aria-label="Configuration de l’application">
        <form method="POST" action="#">
            @csrf
            @method('PUT')

            <div class="admin-settings-grid">
                <section class="admin-card" aria-labelledby="admin-settings-general-title">
                    <div class="admin-card__head">
                        <h2 class="admin-card__title" id="admin-settings-general-title">Général</h2>
                        <x-admin.icon name="sliders" />
                    </div>
                    <div class="admin-card__body">
                        <div class="admin-form-grid">
                            <div class="admin-field admin-form-grid__full @error('app_name') admin-field--invalid @enderror">
                                <label class="admin-field__label" for="admin-settings-app-name">Nom de l’application</label>
                                <input
                                    class="admin-input"
                                    id="admin-settings-app-name"
                                    name="app_name"
                                    type="text"
                                    value="{{ old('app_name', 'Gestion des PV — ENEAM') }}"
                                    @error('app_name') aria-invalid="true" aria-describedby="admin-settings-app-name-error" @enderror
                                >
                                @error('app_name')
                                    <p class="admin-field__error" id="admin-settings-app-name-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="admin-field admin-form-grid__full @error('academic_year') admin-field--invalid @enderror">
                                <label class="admin-field__label" for="admin-settings-academic-year">Année académique en cours</label>
                                <select
                                    class="admin-select"
                                    id="admin-settings-academic-year"
                                    name="academic_year"
                                    @error('academic_year') aria-invalid="true" aria-describedby="admin-settings-academic-year-error" @enderror
                                >
                                    <option value="2024-2025" @selected(old('academic_year', '2025-2026') === '2024-2025')>2024-2025</option>
                                    <option value="2025-2026" @selected(old('academic_year', '2025-2026') === '2025-2026')>2025-2026</option>
                                    <option value="2026-2027" @selected(old('academic_year', '2025-2026') === '2026-2027')>2026-2027</option>
                                </select>
                                @error('academic_year')
                                    <p class="admin-field__error" id="admin-settings-academic-year-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="admin-card" aria-labelledby="admin-settings-files-title">
                    <div class="admin-card__head">
                        <h2 class="admin-card__title" id="admin-settings-files-title">Fichiers</h2>
                        <x-admin.icon name="upload" />
                    </div>
                    <div class="admin-card__body">
                        <div class="admin-form-grid">
                            <div
                                class="admin-field admin-form-grid__full @error('formats') admin-field--invalid @enderror"
                                role="group"
                                aria-labelledby="admin-settings-formats-label"
                                @error('formats') aria-describedby="admin-settings-formats-error" @enderror
                            >
                                <span class="admin-field__label" id="admin-settings-formats-label">Formats acceptés</span>
                                <div class="admin-form-grid">
                                    <label class="admin-checkbox">
                                        <input type="checkbox" name="formats[]" value="pdf" {{ in_array('pdf', $selectedFormats, true) ? 'checked' : '' }}> PDF
                                    </label>
                                    <label class="admin-checkbox">
                                        <input type="checkbox" name="formats[]" value="jpg" {{ in_array('jpg', $selectedFormats, true) ? 'checked' : '' }}> JPG
                                    </label>
                                    <label class="admin-checkbox">
                                        <input type="checkbox" name="formats[]" value="png" {{ in_array('png', $selectedFormats, true) ? 'checked' : '' }}> PNG
                                    </label>
                                </div>
                                @error('formats')
                                    <p class="admin-field__error" id="admin-settings-formats-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="admin-field admin-form-grid__full @error('max_upload_size') admin-field--invalid @enderror">
                                <label class="admin-field__label" for="admin-settings-max-size">Taille maximale (Mo)</label>
                                <input
                                    class="admin-input"
                                    id="admin-settings-max-size"
                                    name="max_upload_size"
                                    type="number"
                                    min="1"
                                    max="50"
                                    value="{{ old('max_upload_size', 10) }}"
                                    @error('max_upload_size') aria-invalid="true" aria-describedby="admin-settings-max-size-error" @enderror
                                >
                                <span class="admin-field__label">Valeur par défaut : 10 Mo.</span>
                                @error('max_upload_size')
                                    <p class="admin-field__error" id="admin-settings-max-size-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="admin-card" aria-labelledby="admin-settings-security-title">
                    <div class="admin-card__head">
                        <h2 class="admin-card__title" id="admin-settings-security-title">Sécurité</h2>
                        <x-admin.icon name="shield" />
                    </div>
                    <div class="admin-card__body">
                        <div class="admin-form-grid">
                            <div class="admin-field @error('session_duration') admin-field--invalid @enderror">
                                <label class="admin-field__label" for="admin-settings-session">Durée de session (minutes)</label>
                                <input
                                    class="admin-input"
                                    id="admin-settings-session"
                                    name="session_duration"
                                    type="number"
                                    min="5"
                                    max="480"
                                    value="{{ old('session_duration', 60) }}"
                                    @error('session_duration') aria-invalid="true" aria-describedby="admin-settings-session-error" @enderror
                                >
                                @error('session_duration')
                                    <p class="admin-field__error" id="admin-settings-session-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="admin-field @error('password_complexity') admin-field--invalid @enderror">
                                <label class="admin-field__label" for="admin-settings-password">Complexité du mot de passe</label>
                                <select
                                    class="admin-select"
                                    id="admin-settings-password"
                                    name="password_complexity"
                                    @error('password_complexity') aria-invalid="true" aria-describedby="admin-settings-password-error" @enderror
                                >
                                    <option value="low" @selected(old('password_complexity', 'high') === 'low')>Faible</option>
                                    <option value="medium" @selected(old('password_complexity', 'high') === 'medium')>Moyenne</option>
                                    <option value="high" @selected(old('password_complexity', 'high') === 'high')>Élevée</option>
                                </select>
                                @error('password_complexity')
                                    <p class="admin-field__error" id="admin-settings-password-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="admin-field admin-form-grid__full @error('signed_link_validity') admin-field--invalid @enderror">
                                <label class="admin-field__label" for="admin-settings-link-validity">Validité par défaut des liens signés (heures)</label>
                                <input
                                    class="admin-input"
                                    id="admin-settings-link-validity"
                                    name="signed_link_validity"
                                    type="number"
                                    min="1"
                                    max="720"
                                    value="{{ old('signed_link_validity', 48) }}"
                                    @error('signed_link_validity') aria-invalid="true" aria-describedby="admin-settings-link-validity-error" @enderror
                                >
                                @error('signed_link_validity')
                                    <p class="admin-field__error" id="admin-settings-link-validity-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="admin-card" aria-labelledby="admin-settings-journal-title">
                    <div class="admin-card__head">
                        <h2 class="admin-card__title" id="admin-settings-journal-title">Journal</h2>
                        <x-admin.icon name="journal" />
                    </div>
                    <div class="admin-card__body">
                        <div class="admin-form-grid">
                            <div class="admin-field admin-form-grid__full @error('retention_months') admin-field--invalid @enderror">
                                <label class="admin-field__label" for="admin-settings-retention">Durée de conservation (mois)</label>
                                <input
                                    class="admin-input"
                                    id="admin-settings-retention"
                                    name="retention_months"
                                    type="number"
                                    min="3"
                                    max="120"
                                    value="{{ old('retention_months', 24) }}"
                                    @error('retention_months') aria-invalid="true" aria-describedby="admin-settings-retention-error" @enderror
                                >
                                @error('retention_months')
                                    <p class="admin-field__error" id="admin-settings-retention-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="admin-notice admin-form-grid__full">
                                <x-admin.icon name="info" />
                                <span>Les entrées antérieures à la durée de conservation sont archivées avant leur suppression définitive.</span>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="admin-form-actions admin-form-grid__full">
                    <a class="admin-button admin-button--secondary" href="{{ route('admin.dashboard') }}">Annuler</a>
                    <button class="admin-button" type="submit">
                        <x-admin.icon name="check" /> Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </section>
@endsection
