@props([
    'initials' => '',
    'size' => 'md',
    'tone' => 'blue',
    'label' => '',
])

@php
    $avatarClass = 'admin-avatar';
    $avatarClass .= $size !== 'md' ? ' admin-avatar--'.$size : '';
    $avatarClass .= in_array($tone, ['gold', 'soft'], true) ? ' admin-avatar--'.$tone : '';
@endphp

<span {{ $attributes->merge([
    'class' => $avatarClass,
    'title' => $label !== '' ? $label : null,
    'aria-label' => $label !== '' ? $label : null,
    'aria-hidden' => $label === '' ? 'true' : null,
]) }}>{{ $initials }}</span>
