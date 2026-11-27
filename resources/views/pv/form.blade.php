@extends('layouts.admin', ['roleKey' => $roleKey ?? 'admin'])

@section('title', ($mode ?? 'create') === 'edit' ? 'Modifier le PV' : 'Nouveau PV')

@section('content')
    @php
        $isEdit = ($mode ?? 'create') === 'edit';
        $indexRoute = \Illuminate\Support\Facades\Route::has(($roleKey ?? 'admin').'.pv.index')
            ? route(($roleKey ?? 'admin').'.pv.index')
            : route('admin.pv.index');
    @endphp

    <x-admin.page-header
        :title="$isEdit ? 'Modifier le procès-verbal' : 'Nouveau procès-verbal'"
        :subtitle="$isEdit ? 'Corrigez les informations du PV et journalisez la modification.' : 'Enregistrez le scan et les informations de délibération.'"
        :breadcrumb="['Accueil', 'PV de délibération', $isEdit ? 'Modifier' : 'Nouveau']"
    >
        <x-slot:actions>
            <x-ui.btn :href="$indexRoute" variant="ghost">Retour au registre</x-ui.btn>
        </x-slot:actions>
    </x-admin.page-header>

    @if (session('success'))
        <div class="ui-notice ui-notice--success" role="status">
            <x-admin.icon name="check" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="ui-notice" role="alert">
            <x-admin.icon name="alert" />
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    @include('shared.pv._form', [
        'mode' => $mode ?? 'create',
        'pv' => $pv ?? null,
        'filiere' => $filiere ?? null,
        'promotion' => $promotion ?? null,
        'reference' => $reference ?? '',
        'isArchived' => $isArchived ?? false,
        'canEdit' => $canEdit ?? true,
        'roleKey' => $roleKey ?? 'admin',
    ])
@endsection
