@extends('layouts.admin')

@section('title', 'Mon profil')

@section('page-title', 'Mon profil')
@section('page-subtitle', 'Informations personnelles et sécurité du compte')

@section('content')
    @php
        $initiales = collect(preg_split('/\s+/', trim($user->nom_complet)) ?: [])
            ->filter()
            ->map(fn (string $partie) => mb_strtoupper(mb_substr($partie, 0, 1)))
            ->take(2)
            ->implode('');
    @endphp

    <x-admin.page-header title="Mon profil" subtitle="Mise à jour de vos informations personnelles." :breadcrumb="['Mon espace', 'Profil']" />

    <section class="admin-section" aria-labelledby="profil-identite-title">
        <header class="admin-student-head">
            <x-admin.avatar :initials="$initiales" size="lg" tone="gold" label="Initiales de {{ $user->nom_complet }}" />
            <div>
                <h1 class="admin-student-head__name" id="profil-identite-title">{{ $user->nom_complet }}</h1>
                <p class="admin-student-head__meta">{{ $user->email }}</p>
            </div>
        </header>

        <div class="admin-card">
            <div class="admin-card__head">
                <h2 class="admin-card__title">Informations du compte</h2>
            </div>
            <div class="admin-card__body">
                <dl class="admin-info-list">
                    <div class="admin-info-row">
                        <dt>Rôle</dt>
                        <dd>{{ $user->role?->label() }}</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Filière</dt>
                        <dd>{{ $user->filiere?->nom ?? '—' }}</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Dernière connexion</dt>
                        <dd>{{ $user->last_login_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div class="admin-info-row">
                        <dt>Mot de passe temporaire</dt>
                        <dd>{{ $user->must_change_password ? 'Oui — changez-le avant toute action' : 'Non' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="admin-section" aria-labelledby="profil-form-title">
        <div class="admin-card">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="profil-form-title">Modifier mes informations</h2>
            </div>
            <div class="admin-card__body">
                <form method="POST" action="{{ route('profil.update') }}" class="admin-form-grid">
                    @csrf
                    @method('PUT')

                    <label class="admin-field">
                        <span class="admin-field__label">Nom</span>
                        <input class="admin-input" name="nom" type="text" value="{{ old('nom', $user->nom) }}" required maxlength="100">
                    </label>

                    <label class="admin-field">
                        <span class="admin-field__label">Prénoms</span>
                        <input class="admin-input" name="prenoms" type="text" value="{{ old('prenoms', $user->prenoms) }}" required maxlength="150">
                    </label>

                    <button class="admin-button admin-button--gold admin-button--small" type="submit">
                        <x-admin.icon name="check" /> Enregistrer
                    </button>
                </form>

                <div class="admin-table__actions">
                    <a class="admin-button admin-button--small" href="{{ route('password.change') }}">
                        <x-admin.icon name="key" /> Changer le mot de passe
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
