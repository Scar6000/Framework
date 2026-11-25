@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', 'Modifier le PV '.($reference ?? request()->route('reference', '')))

@section('content')
    @php
        $roleKey = $roleKey ?? 'admin';
        $reference = $reference ?? request()->route('reference', '');
        $pvs = collect(config('ui-demo.pvs', []));
        $pv = $pvs->firstWhere('reference', $reference);

        if (! is_array($pv)) {
            $fallback = $pvs->first() ?? [];
            $fallback['reference'] = $reference !== '' ? $reference : ($fallback['reference'] ?? 'PV-2026-IG-L3-0XX');
            $pv = $fallback;
        }

        $reference = $pv['reference'];
        $isArchived = ($pv['status'] ?? '') === 'Archivé';
        $canEdit = in_array($roleKey, ['admin', 'agent'], true) && (! $isArchived || $roleKey === 'admin');
        $indexRoute = \Illuminate\Support\Facades\Route::has($roleKey.'.pv.index')
            ? route($roleKey.'.pv.index')
            : route('admin.pv.index');
    @endphp

    <x-admin.page-header
        title="Modifier le PV {{ $reference }}"
        subtitle="Les champs modifiés sont enregistrés comme une nouvelle version du dossier."
        :breadcrumb="['Accueil', 'Registre des PV', 'Modifier le PV']"
    >
        <x-slot:actions>
            <x-ui.btn :href="$indexRoute" variant="secondary" icon="chevron-left">Retour au registre</x-ui.btn>
        </x-slot:actions>
    </x-admin.page-header>

    @include('shared.pv._form', [
        'mode' => 'edit',
        'roleKey' => $roleKey,
        'reference' => $reference,
        'pv' => $pv,
        'isArchived' => $isArchived,
        'canEdit' => $canEdit,
    ])
@endsection
