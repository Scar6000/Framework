@props(['value' => 0, 'label' => 'Progression', 'tone' => 'blue', 'large' => false])

@php
    $percentage = max(0, min(100, (int) $value));
    $progressClass = 'admin-progress'.($large ? ' admin-progress--large' : '');
@endphp

<div {{ $attributes->merge([
    'class' => $progressClass,
    'role' => 'progressbar',
    'aria-label' => $label,
    'aria-valuemin' => '0',
    'aria-valuemax' => '100',
    'aria-valuenow' => (string) $percentage,
]) }}>
    <span class="admin-progress__fill{{ $tone === 'gold' ? ' admin-progress__fill--gold' : '' }}" style="width: {{ $percentage }}%"></span>
</div>
