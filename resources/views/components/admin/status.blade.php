@props(['label' => '', 'tone' => 'blue'])

<span {{ $attributes->merge(['class' => 'admin-status admin-status--'.$tone]) }}>
    <span class="admin-status__dot" aria-hidden="true"></span>
    {{ $label }}
</span>
