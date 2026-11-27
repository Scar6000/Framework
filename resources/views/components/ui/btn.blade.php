@props([
    'variant' => 'primary',
    'href' => null,
    'icon' => null,
    'type' => 'button',
    'label' => null,
])

@php
    $variant = in_array($variant, ['primary', 'secondary', 'compact', 'icon', 'ghost'], true) ? $variant : 'primary';
    $buttonClass = 'admin-button admin-button--'.$variant;
@endphp

@if ($href !== null)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $buttonClass]) }}>
        @if ($icon !== null)<x-admin.icon name="{{ $icon }}" />@endif
        @if ($label !== null){{ $label }}@else{{ $slot }}@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $buttonClass]) }}>
        @if ($icon !== null)<x-admin.icon name="{{ $icon }}" />@endif
        @if ($label !== null){{ $label }}@else{{ $slot }}@endif
    </button>
@endif
