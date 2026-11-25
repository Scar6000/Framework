@props(['label' => '', 'tone' => 'blue'])

@php
    $toneMap = [
        'slate' => 'neutral',
        'lightgold' => 'gold',
        'gold' => 'gold',
        'blue' => 'blue',
        'dark' => 'dark',
        'red' => 'red',
    ];
@endphp

<x-ui.badge :label="$label" :tone="$toneMap[$tone] ?? 'neutral'" {{ $attributes }} />
