@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Accès non autorisé')

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $dashboardRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.dashboard')
            ? route($roleKey.'.dashboard')
            : route('admin.dashboard');
    @endphp

    <section class="ui-result-screen" aria-labelledby="acces-refuse-title">
        <svg
            width="40"
            height="40"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
            role="img"
            aria-label="Cadenas fermé"
        >
            <rect x="4" y="10" width="16" height="11" rx="2"></rect>
            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
            <path d="M12 14.5v2.5"></path>
        </svg>

        <h1 id="acces-refuse-title">Accès non autorisé</h1>
        <p>{{ $deniedMessage ?? "Votre profil ne permet pas d'accéder à cette page." }}</p>

        <x-ui.btn :href="$dashboardRoute" variant="primary" icon="arrow-right" label="Retour au tableau de bord" />
    </section>
@endsection
