@php
    $menuId = $menuId ?? 'admin-notifications-menu';
    $triggerId = $triggerId ?? 'admin-notifications-trigger';
    $utilisateur = auth()->user();
    $nonLues = $utilisateur?->unreadNotifications()->count() ?? 0;
    $notifications = $utilisateur?->unreadNotifications()->latest()->limit(3)->get() ?? collect();
@endphp

<div class="admin-dropdown">
    <button class="admin-icon-button admin-icon-button--notification" id="{{ $triggerId }}" type="button" data-dropdown aria-controls="{{ $menuId }}" aria-expanded="false" aria-label="{{ $nonLues }} notification(s) non lue(s)">
        <x-admin.icon name="bell" />
        @if ($nonLues > 0)
            <span class="admin-notification-count">{{ $nonLues }}</span>
        @endif
    </button>

    <div class="admin-dropdown__menu admin-notifications" id="{{ $menuId }}" hidden>
        <div class="admin-notifications__head">
            <span>Notifications</span>
            <span>{{ $nonLues }} non lue(s)</span>
        </div>

        @forelse ($notifications as $notification)
            @php
                $donnees = $notification->data ?? [];
                $lien = isset($donnees['pv_id']) ? route('pv.show', $donnees['pv_id']) : route('notifications.index');
                $icone = match ($donnees['type'] ?? null) {
                    'soumis' => 'clock',
                    'valide' => 'check',
                    'rejete' => 'alert',
                    default => 'info',
                };
            @endphp

            <a class="admin-notification" href="{{ $lien }}">
                <span class="admin-notification__icon"><x-admin.icon name="{{ $icone }}" /></span>
                <span class="admin-notification__text">
                    <strong>{{ $donnees['message'] ?? 'Nouvelle notification' }}</strong>
                    <span>{{ $notification->created_at->diffForHumans() }}@if (isset($donnees['reference'])) · {{ $donnees['reference'] }}@endif</span>
                </span>
            </a>
        @empty
            <span class="admin-empty">Aucune notification non lue.</span>
        @endforelse

        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button class="admin-dropdown__item" type="submit">
                <x-admin.icon name="check" /> Tout marquer comme lu
            </button>
        </form>

        <a class="admin-dropdown__item" href="{{ route('notifications.index') }}">
            <x-admin.icon name="bell" /> Voir toutes les notifications
        </a>
    </div>
</div>
