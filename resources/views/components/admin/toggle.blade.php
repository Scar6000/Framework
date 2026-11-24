@props(['name' => '', 'checked' => false, 'disabled' => false, 'label' => ''])

<label class="admin-switch">
    <input
        type="checkbox"
        name="{{ $name }}"
        value="1"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        @if ($label !== '') aria-label="{{ $label }}" @endif
        {{ $attributes }}
    >
    <span class="admin-switch__track" aria-hidden="true"></span>
</label>
