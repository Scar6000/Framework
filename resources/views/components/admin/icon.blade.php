@props(['name' => 'dashboard'])

<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['class' => 'icon-sm', 'aria-hidden' => 'true', 'focusable' => 'false']) }}>
    @switch($name)
        @case('dashboard')
            <rect x="3" y="3" width="7" height="7" rx="2"></rect>
            <rect x="14" y="3" width="7" height="7" rx="2"></rect>
            <rect x="3" y="14" width="7" height="7" rx="2"></rect>
            <rect x="14" y="14" width="7" height="7" rx="2"></rect>
            @break
        @case('users')
            <path d="M16 20v-1.5a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4V20"></path>
            <circle cx="9" cy="7" r="3.2"></circle>
            <path d="M22 20v-1.5a4 4 0 0 0-3-3.87"></path>
            <path d="M16 4.2a3.2 3.2 0 0 1 0 6.2"></path>
            @break
        @case('roles')
            <path d="M12 3 4.5 6v5.5c0 4.6 3.1 7.9 7.5 9.5 4.4-1.6 7.5-4.9 7.5-9.5V6Z"></path>
            <path d="m9 12 2 2 4-4"></path>
            @break
        @case('referentiels')
            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5Z"></path>
            <path d="M4 18.5A2.5 2.5 0 0 1 6.5 16H20"></path>
            <path d="M8 7h8M8 11h6"></path>
            @break
        @case('plan')
            <rect x="3" y="4" width="18" height="17" rx="3"></rect>
            <path d="M3 9h18M8 9v12M14 9v12"></path>
            <path d="m6 14 1.2 1.2L9 13.5M12.2 14l1.2 1.2 1.8-1.8"></path>
            @break
        @case('pv')
            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"></path>
            <path d="M14 3v5h5M8.5 13h6M8.5 17h4"></path>
            @break
        @case('search')
            <circle cx="10.8" cy="10.8" r="6.8"></circle>
            <path d="m16 16 4.5 4.5"></path>
            @break
        @case('journal')
            <path d="M5 4h13a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"></path>
            <path d="M8 9h8M8 13h8M8 17h5"></path>
            @break
        @case('exports')
            <path d="M12 15V3"></path>
            <path d="m7.5 7.5 4.5-4.5 4.5 4.5"></path>
            <path d="M4 15v3a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-3"></path>
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 1.56V21a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.56-1H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.33-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6h.1A1.7 1.7 0 0 0 10 3.04V3a2 2 0 1 1 4 0v.1A1.7 1.7 0 0 0 15 4.6a1.7 1.7 0 0 0 1.87-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9v.1a1.7 1.7 0 0 0 1.56 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5.9Z"></path>
            @break
        @case('logout')
            <path d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4"></path>
            <path d="M15 16l4-4-4-4M19 12H9"></path>
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16"></path>
            @break
        @case('bell')
            <path d="M18 9a6 6 0 1 0-12 0c0 5-2 6-2 6h16s-2-1-2-6Z"></path>
            <path d="M10 19a2 2 0 0 0 4 0"></path>
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6"></path>
            @break
        @case('chevron-left')
            <path d="m15 18-6-6 6-6"></path>
            @break
        @case('chevron-right')
            <path d="m9 18 6-6-6-6"></path>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"></path>
            @break
        @case('filter')
            <path d="M4 6h16M7 12h10M10 18h4"></path>
            @break
        @case('more')
            <circle cx="12" cy="5" r="1.2"></circle>
            <circle cx="12" cy="12" r="1.2"></circle>
            <circle cx="12" cy="19" r="1.2"></circle>
            @break
        @case('close')
            <path d="m6 6 12 12M18 6 6 18"></path>
            @break
        @case('check')
            <path d="m5 12.5 4.5 4.5L19 7"></path>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M12 7.5V12l3.5 2"></path>
            @break
        @case('alert')
            <path d="M12 4 3.5 19h17Z"></path>
            <path d="M12 9.5v4M12 16.5h.01"></path>
            @break
        @case('file')
            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"></path>
            <path d="M14 3v5h5M8.5 13h6M8.5 17h4"></path>
            @break
        @case('pdf')
            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"></path>
            <path d="M14 3v5h5"></path>
            <path d="M8 16.5h1.2a1.3 1.3 0 0 0 0-2.6H8v4M14.5 13.9h.8a1.4 1.4 0 0 1 0 2.8h-.8v-2.8Z"></path>
            @break
        @case('archive')
            <rect x="3" y="4" width="18" height="4" rx="1.5"></rect>
            <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8M10 12h4"></path>
            @break
        @case('lock')
            <rect x="5" y="10" width="14" height="10" rx="2.5"></rect>
            <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14.5v2"></path>
            @break
        @case('unlock')
            <rect x="5" y="10" width="14" height="10" rx="2.5"></rect>
            <path d="M8 10V7a4 4 0 0 1 7.5-2M12 14.5v2"></path>
            @break
        @case('eye')
            <path d="M2.5 12S6 6 12 6s9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
            <circle cx="12" cy="12" r="3"></circle>
            @break
        @case('download')
            <path d="M12 3v12M7.5 10.5 12 15l4.5-4.5"></path>
            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"></path>
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="3"></rect>
            <path d="M3 10h18M8 3v4M16 3v4"></path>
            @break
        @case('arrow-right')
            <path d="M4 12h16M14 6l6 6-6 6"></path>
            @break
        @case('shield')
            <path d="M12 3 5 6v5.5c0 4.4 2.9 7.6 7 9.5 4.1-1.9 7-5.1 7-9.5V6Z"></path>
            <path d="m9 12 2 2 4-4"></path>
            @break
        @case('user')
            <circle cx="12" cy="8" r="3.5"></circle>
            <path d="M5 20c0-3.3 3.1-5.5 7-5.5s7 2.2 7 5.5"></path>
            @break
        @case('chart')
            <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"></path>
            @break
        @case('spark')
            <path d="m12 3 1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9Z"></path>
            <path d="m18.5 16 .7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7Z"></path>
            @break
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="3"></rect>
            <path d="m4 7 8 6 8-6"></path>
            @break
        @case('key')
            <circle cx="8" cy="16" r="4"></circle>
            <path d="m11 13 8-8M16 8l2 2M14 10l2 2"></path>
            @break
        @case('upload')
            <path d="M12 16V4M7.5 8.5 12 4l4.5 4.5"></path>
            <path d="M4 15v3a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-3"></path>
            @break
        @case('external')
            <path d="M14 4h6v6M20 4l-9 9"></path>
            <path d="M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"></path>
            @break
        @case('info')
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M12 11v6M12 7.5h.01"></path>
            @break
        @case('pencil')
            <path d="M4 20h4l10-10a2.8 2.8 0 0 0-4-4L4 16Z"></path>
            <path d="m13.5 6.5 4 4"></path>
            @break
        @case('trash')
            <path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"></path>
            <path d="M10 11v5M14 11v5"></path>
            @break
        @case('link')
            <path d="M10 13.5a4 4 0 0 0 5.7 0l2-2a4 4 0 0 0-5.7-5.7L11 7"></path>
            <path d="M14 10.5a4 4 0 0 0-5.7 0l-2 2A4 4 0 0 0 12 18.2L13 17"></path>
            @break
        @case('sliders')
            <path d="M4 7h10M18 7h2M4 17h2M10 17h10"></path>
            <circle cx="16" cy="7" r="2"></circle>
            <circle cx="8" cy="17" r="2"></circle>
            @break
        @case('database')
            <ellipse cx="12" cy="6" rx="8" ry="3"></ellipse>
            <path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"></path>
            @break
        @case('graduation')
            <path d="m2 9 10-5 10 5-10 5Z"></path>
            <path d="M6 11v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5M22 9v6"></path>
            @break
        @case('layers')
            <path d="m12 3 9 5-9 5-9-5Z"></path>
            <path d="m3 12 9 5 9-5M3 16l9 5 9-5"></path>
            @break
        @case('refresh')
            <path d="M20 11a8 8 0 0 0-14-4.5L4 8"></path>
            <path d="M4 4v4h4M4 13a8 8 0 0 0 14 4.5L20 16"></path>
            <path d="M20 20v-4h-4"></path>
            @break
        @case('copy')
            <rect x="8" y="8" width="12" height="12" rx="2"></rect>
            <path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"></path>
            @break
        @case('printer')
            <path d="M7 8V3h10v5M7 17H5a2 2 0 0 1-2-2v-4a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v4a2 2 0 0 1-2 2h-2"></path>
            <rect x="7" y="14" width="10" height="7" rx="1.5"></rect>
            @break
        @case('circle')
            <circle cx="12" cy="12" r="7"></circle>
            @break
        @case('minus')
            <path d="M6 12h12"></path>
            @break
        @default
            <circle cx="12" cy="12" r="8"></circle>
            <path d="M12 8v8M8 12h8"></path>
    @endswitch
</svg>
