@props(['title' => '', 'subtitle' => '', 'breadcrumb' => []])

<header class="admin-page-header">
    <div>
        @if (count($breadcrumb) > 0)
            <nav class="admin-breadcrumb" aria-label="Fil d'Ariane">
                @foreach ($breadcrumb as $item)
                    @if (! $loop->first)
                        <x-admin.icon name="chevron-right" />
                    @endif
                    <span>{{ $item }}</span>
                @endforeach
            </nav>
        @endif

        <h1 class="admin-page-header__title">{{ $title }}</h1>

        @if ($subtitle !== '')
            <p class="admin-page-header__subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if (isset($actions) && trim($actions) !== '')
        <div class="admin-page-header__actions">{{ $actions }}</div>
    @endif
</header>
