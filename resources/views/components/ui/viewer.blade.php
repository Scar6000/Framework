@props([
    'reference' => '',
    'pages' => 1,
    'canDownload' => false,
    'id' => null,
])

@php
    $pageCount = max(1, min(12, (int) $pages));
    $viewerId = $id ?? 'viewer-'.preg_replace('/[^A-Za-z0-9_-]/', '', $reference).'-'.uniqid();
@endphp

<section class="ui-viewer" id="{{ $viewerId }}" data-viewer aria-label="Aperçu du scan {{ $reference }}">
    <div class="ui-viewer__toolbar">
        <span class="ui-viewer__label"><x-admin.icon name="eye" /> Aperçu du scan</span>
        <div class="ui-viewer__tools">
            <button class="admin-icon-button admin-icon-button--small" type="button" data-viewer-zoom="-1" aria-label="Réduire le zoom">
                <x-admin.icon name="minus" />
            </button>
            <button class="admin-icon-button admin-icon-button--small" type="button" data-viewer-zoom="1" aria-label="Augmenter le zoom">
                <x-admin.icon name="plus" />
            </button>
            <button class="ui-viewer__fit" type="button" data-viewer-fit>Ajuster</button>
            @if ($canDownload)
                {{-- TODO: brancher la route sécurisée de téléchargement du scan --}}
                <x-ui.btn href="#" variant="compact" icon="download" class="ui-viewer__download">Télécharger</x-ui.btn>
            @endif
        </div>
    </div>

    <div class="ui-viewer__stage">
        <button class="admin-icon-button ui-viewer__nav" type="button" data-viewer-prev aria-label="Page précédente" disabled>
            <x-admin.icon name="chevron-left" />
        </button>

        <div class="ui-viewer__track" data-viewer-track tabindex="0" aria-label="Pages du PV {{ $reference }}">
            @for ($page = 1; $page <= $pageCount; $page++)
                <article class="ui-viewer__page{{ $page === 1 ? ' is-active' : '' }}" data-viewer-page data-index="{{ $page }}"
                         aria-label="Page {{ $page }} sur {{ $pageCount }}">
                    <svg viewBox="0 0 300 390" width="300" height="390" role="img" aria-label="Page {{ $page }} du procès-verbal">
                        <rect x="0.5" y="0.5" width="299" height="389" rx="3" fill="#ffffff" stroke="#dce7ff"></rect>
                        <text x="150" y="30" text-anchor="middle" fill="#000000" font-family="Segoe UI, sans-serif" font-size="11" font-weight="700">RÉPUBLIQUE DU BÉNIN</text>
                        <text x="150" y="46" text-anchor="middle" fill="#000000" font-family="Segoe UI, sans-serif" font-size="8">ENEAM — PROCÈS-VERBAL DE DÉLIBÉRATION</text>
                        <text x="24" y="72" fill="#09090b" font-family="Segoe UI, sans-serif" font-size="9" font-weight="700">{{ $reference }}</text>
                        <line x1="24" y1="82" x2="276" y2="82" stroke="#0b3394" stroke-width="1"></line>
                        @for ($line = 0; $line < 11; $line++)
                            <rect x="24" y="{{ 96 + $line * 23 }}" width="{{ $line % 3 === 0 ? 210 : 252 }}" height="5" rx="2" fill="#e8efff"></rect>
                            <rect x="24" y="{{ 103 + $line * 23 }}" width="{{ $line % 4 === 0 ? 160 : 230 }}" height="3" rx="1.5" fill="#f0f4ff"></rect>
                        @endfor
                        <line x1="24" y1="358" x2="276" y2="358" stroke="#dce7ff" stroke-width="1"></line>
                        <text x="24" y="374" fill="#000000" font-family="Segoe UI, sans-serif" font-size="8">Page {{ $page }} / {{ $pageCount }}</text>
                    </svg>
                </article>
            @endfor
        </div>

        <button class="admin-icon-button ui-viewer__nav" type="button" data-viewer-next aria-label="Page suivante">
            <x-admin.icon name="chevron-right" />
        </button>
    </div>

    <div class="ui-viewer__meta">
        <span data-viewer-counter aria-live="polite">Page 1 / {{ $pageCount }}</span>
        <span class="ui-viewer__meta-hint">Flèches gauche et droite pour parcourir les pages</span>
    </div>

    <div class="ui-viewer__thumbs" role="group" aria-label="Miniatures des pages">
        @for ($page = 1; $page <= $pageCount; $page++)
            <button class="ui-viewer__thumb{{ $page === 1 ? ' is-active' : '' }}" type="button" data-viewer-thumb data-index="{{ $page }}"
                    aria-label="Afficher la page {{ $page }}" @if ($page === 1) aria-current="true" @endif>
                <svg viewBox="0 0 36 46" width="36" height="46" aria-hidden="true" focusable="false">
                    <rect x="0.5" y="0.5" width="35" height="45" rx="2" fill="#ffffff" stroke="#dce7ff"></rect>
                    <rect x="6" y="8" width="24" height="2" rx="1" fill="#dce7ff"></rect>
                    <rect x="6" y="15" width="20" height="2" rx="1" fill="#e8efff"></rect>
                    <rect x="6" y="22" width="24" height="2" rx="1" fill="#e8efff"></rect>
                    <rect x="6" y="29" width="17" height="2" rx="1" fill="#e8efff"></rect>
                    <rect x="6" y="36" width="22" height="2" rx="1" fill="#e8efff"></rect>
                </svg>
            </button>
        @endfor
    </div>
</section>
