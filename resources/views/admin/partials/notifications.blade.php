@php
    $menuId = $menuId ?? 'admin-notifications-menu';
    $triggerId = $triggerId ?? 'admin-notifications-trigger';
@endphp

<div class="admin-dropdown">
    <button class="admin-icon-button admin-icon-button--notification" id="{{ $triggerId }}" type="button" data-dropdown aria-controls="{{ $menuId }}" aria-expanded="false" aria-label="3 notifications">
        <x-admin.icon name="bell" />
        <span class="admin-notification-count">3</span>
    </button>

    <div class="admin-dropdown__menu admin-notifications" id="{{ $menuId }}" hidden>
        <div class="admin-notifications__head">
            <span>Notifications</span>
            <span>3 nouvelles</span>
        </div>

        <a class="admin-notification" href="{{ route('admin.plan.index') }}">
            <span class="admin-notification__icon"><x-admin.icon name="alert" /></span>
            <span class="admin-notification__text">
                <strong>PV manquant détecté</strong>
                <span>Comptabilité, L3, S2 — session normale.</span>
            </span>
        </a>

        <a class="admin-notification" href="{{ route('admin.users.index') }}">
            <span class="admin-notification__icon"><x-admin.icon name="users" /></span>
            <span class="admin-notification__text">
                <strong>Nouveau compte créé</strong>
                <span>Agent de scolarité — service des examens.</span>
            </span>
        </a>

        <a class="admin-notification" href="{{ route('admin.journal.index') }}">
            <span class="admin-notification__icon"><x-admin.icon name="shield" /></span>
            <span class="admin-notification__text">
                <strong>3 tentatives de connexion échouées</strong>
                <span>Journal d'audit — aujourd'hui, 08 h 42.</span>
            </span>
        </a>
    </div>
</div>
