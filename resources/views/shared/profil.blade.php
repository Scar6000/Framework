@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Mon profil')

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $roleConfig = config('ui-roles.roles.'.$roleKey, config('ui-roles.roles.admin'));
        $profile = $roleConfig['profile'];
        $profileRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.profil') ? route($roleKey.'.profil') : '#';
        $dashboardRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.dashboard')
            ? route($roleKey.'.dashboard')
            : route('admin.dashboard');

        $passwordFields = [
            [
                'name' => 'ancien_mot_de_passe',
                'label' => 'Ancien mot de passe',
                'autocomplete' => 'current-password',
            ],
            [
                'name' => 'nouveau_mot_de_passe',
                'label' => 'Nouveau mot de passe',
                'autocomplete' => 'new-password',
            ],
            [
                'name' => 'nouveau_mot_de_passe_confirmation',
                'label' => 'Confirmer le nouveau mot de passe',
                'autocomplete' => 'new-password',
            ],
        ];
    @endphp

    <x-admin.page-header
        title="Mon profil"
        subtitle="Consultez vos informations et mettez à jour votre mot de passe."
        :breadcrumb="['Accueil', 'Mon profil']"
    />

    <section class="admin-section" aria-labelledby="shared-profil-infos-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-profil-infos-title">Informations personnelles</h2>
                <p class="admin-section__hint">Ces informations sont renseignées par l'administration.</p>
            </div>
            <div class="admin-section__actions">
                <x-ui.badge label="Lecture seule" tone="neutral" />
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <div class="admin-card__heading">
                    <x-admin.icon name="user" />
                    <h3 class="admin-card__title">Identité</h3>
                </div>
            </div>
            <div class="admin-card__body">
                <dl class="admin-info-list">
                    <div class="admin-info-row">
                        <dt>Nom</dt>
                        <dd>{{ $profile['name'] }}</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>E-mail</dt>
                        <dd>{{ $profile['email'] }}</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Rôle</dt>
                        <dd>{{ $profile['role'] }}</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Département</dt>
                        <dd>{{ $profile['department'] }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="shared-profil-password-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="shared-profil-password-title">Changer le mot de passe</h2>
                <p class="admin-section__hint">Renseignez votre mot de passe actuel, puis le nouveau à deux reprises.</p>
            </div>
        </div>

        <form method="POST" action="{{ $profileRoute }}">
            @csrf
            @method('PUT')

            <div class="admin-card">
                <div class="admin-card__head">
                    <div class="admin-card__heading">
                        <x-admin.icon name="key" />
                        <h3 class="admin-card__title">Sécurité du compte</h3>
                    </div>
                </div>
                <div class="admin-card__body">
                    <div class="admin-form-grid">
                        @foreach ($passwordFields as $field)
                            @php
                                $fieldId = 'shared-profil-'.$field['name'];
                                $hasError = $errors->has($field['name']);
                            @endphp
                            <div class="admin-field admin-form-grid__full {{ $hasError ? 'admin-field--invalid' : '' }}">
                                <label class="admin-field__label" for="{{ $fieldId }}">{{ $field['label'] }}</label>
                                <input
                                    class="admin-input"
                                    id="{{ $fieldId }}"
                                    name="{{ $field['name'] }}"
                                    type="password"
                                    autocomplete="{{ $field['autocomplete'] }}"
                                    value="{{ old($field['name']) }}"
                                    @if ($hasError) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif
                                >
                                @error($field['name'])
                                    <p class="admin-field__error" id="{{ $fieldId }}-error">
                                        <x-admin.icon name="alert" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="admin-form-actions">
                <x-ui.btn :href="$dashboardRoute" variant="compact" class="admin-button--secondary" label="Annuler" />
                <x-ui.btn variant="compact" icon="check" type="submit" label="Mettre à jour" />
            </div>
        </form>
    </section>
@endsection
