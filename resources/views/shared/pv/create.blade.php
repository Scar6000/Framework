@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Nouveau PV')

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $indexRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.pv.index')
            ? route($roleKey.'.pv.index')
            : route('admin.pv.index');
        $reference = 'PV-2026-IG-L3-0XX';
    @endphp

    <x-admin.page-header
        title="Nouveau PV"
        subtitle="Associez le scan et les informations de délibération avant l'enregistrement."
        :breadcrumb="['Accueil', 'Registre des PV', 'Nouveau PV']"
    >
        <x-slot:actions>
            <x-ui.btn :href="$indexRoute" variant="secondary" icon="chevron-left">Retour au registre</x-ui.btn>
        </x-slot:actions>
    </x-admin.page-header>

    @include('shared.pv._form', [
        'mode' => 'create',
        'roleKey' => $roleKey,
        'reference' => $reference,
        'pv' => [],
        'isArchived' => false,
        'canEdit' => true,
    ])
@endsection
