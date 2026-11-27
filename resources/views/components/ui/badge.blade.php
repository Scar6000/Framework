@props(['label' => '', 'tone' => 'neutral'])

@php
    $tone = in_array($tone, ['neutral', 'blue', 'dark', 'gold', 'red'], true) ? $tone : 'neutral';
@endphp

<span {{ $attributes->merge(['class' => 'admin-badge admin-badge--'.$tone]) }}>{{ $label !== '' ? $label : $slot }}</span>
