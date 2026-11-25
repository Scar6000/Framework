@props([
    'reference' => '',
    'preview' => '#',
    'actions' => [],
    'emptyMessage' => '',
])

@php
    $menuId = 'ui-row-menu-'.uniqid();
@endphp

<div class="ui-table-actions">
    <x-ui.btn :href="$preview" variant="compact" class="ui-row-action" :title="trim($emptyMessage) !== '' ? $emptyMessage : null">Aperçu</x-ui.btn>

    @if (count($actions) > 0)
        <button class="admin-icon-button admin-icon-button--small ui-row-action" type="button"
                data-ui-menu-trigger aria-controls="{{ $menuId }}" aria-haspopup="menu" aria-expanded="false"
                aria-label="Actions secondaires pour {{ $reference }}">
            <x-admin.icon name="more" />
        </button>

        <div class="ui-menu" id="{{ $menuId }}" role="menu" aria-label="Actions pour {{ $reference }}" hidden>
            @foreach ($actions as $action)
                @if (! empty($action['modal']))
                    <button class="ui-menu__item" type="button" role="menuitem"
                            data-modal-open data-target="{{ $action['modal'] }}"
                            @if (! empty($action['reason'])) title="{{ $action['reason'] }}" @endif>
                        <x-admin.icon name="{{ $action['icon'] ?? 'pencil' }}" />
                        <span>{{ $action['label'] }}</span>
                    </button>
                @else
                    <a class="ui-menu__item" role="menuitem" href="{{ $action['url'] ?? '#' }}"
                       @if (! empty($action['reason'])) title="{{ $action['reason'] }}" @endif>
                        <x-admin.icon name="{{ $action['icon'] ?? 'pencil' }}" />
                        <span>{{ $action['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    @elseif (trim($emptyMessage) !== '')
        <span class="ui-table-actions__hint" tabindex="0" title="{{ $emptyMessage }}" aria-label="{{ $emptyMessage }}">
            <x-admin.icon name="info" />
        </span>
    @endif
</div>
