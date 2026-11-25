@props(['reference' => ''])

<span {{ $attributes->merge(['class' => 'pv-ref']) }} title="{{ $reference }}">{{ $reference }}</span>
