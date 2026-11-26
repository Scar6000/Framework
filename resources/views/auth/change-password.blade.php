@extends('layouts.admin')

@section('title', 'Changer le mot de passe')

@section('page-title', 'Changer le mot de passe')
@section('page-subtitle', 'Sécurisation de l’accès à votre espace')

@section('content')
    <x-admin.page-header title="Changer le mot de passe" subtitle="Choisissez un mot de passe d’au moins 8 caractères comportant des lettres et des chiffres." :breadcrumb="['Mon espace', 'Sécurité']" />

    @if ($obligatoire)
        <div class="admin-notice admin-notice--error" role="alert">
            <x-admin.icon name="alert" />
            <span>Votre mot de passe est temporaire. Vous devez le modifier avant de poursuivre.</span>
        </div>
    @endif

    <section class="admin-section" aria-labelledby="password-form-title">
        <div class="admin-card">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="password-form-title">Nouveau mot de passe</h2>
            </div>
            <div class="admin-card__body">
                <form method="POST" action="{{ route('password.change.update') }}" class="admin-form-grid">
                    @csrf
                    @method('PUT')

                    <label class="admin-field admin-field--grow">
                        <span class="admin-field__label">Mot de passe actuel</span>
                        <input class="admin-input" name="current_password" type="password" required autocomplete="current-password">
                    </label>

                    <label class="admin-field">
                        <span class="admin-field__label">Nouveau mot de passe</span>
                        <input class="admin-input" name="password" type="password" required minlength="8" autocomplete="new-password">
                    </label>

                    <label class="admin-field">
                        <span class="admin-field__label">Confirmation</span>
                        <input class="admin-input" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
                    </label>

                    <button class="admin-button admin-button--gold admin-button--small" type="submit">
                        <x-admin.icon name="key" /> Valider le changement
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
