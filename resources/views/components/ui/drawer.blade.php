@props([
    'id' => '',
    'title' => '',
    'subtitle' => '',
    'wide' => false,
])

@php
    $headingId = $id.'-title';
@endphp

<div class="admin-drawer ui-drawer{{ $wide ? ' admin-drawer--wide' : '' }}" id="{{ $id }}" hidden
     role="dialog" aria-modal="true" aria-labelledby="{{ $headingId }}">
    <div class="admin-drawer__head">
        <div class="ui-drawer__heading">
            <h2 class="admin-drawer__title" id="{{ $headingId }}">{{ $title }}</h2>
            @if ($subtitle !== '')
                <p class="admin-drawer__subtitle">{{ $subtitle }}</p>
            @endif
            @if (trim($header ?? '') !== '')
                <div class="ui-drawer__header-slot">{{ $header }}</div>
            @endif
        </div>
        <button class="admin-icon-button" type="button" data-drawer-close aria-label="Fermer le tiroir">
            <x-admin.icon name="close" />
        </button>
    </div>

    <div class="admin-drawer__body">{{ $slot }}</div>

    @if (trim($footer ?? '') !== '')
        <div class="admin-drawer__footer">{{ $footer }}</div>
    @endif
</div>
