@extends('layouts.admin')

@section('title', 'Notifications')

@section('page-title', 'Notifications')
@section('page-subtitle', 'Suivi des événements liés aux procès-verbaux')

@section('content')
    <x-admin.page-header title="Notifications" subtitle="{{ $non_lues }} notification(s) non lue(s)." :breadcrumb="['Mon espace', 'Notifications']">
        @slot('actions')
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button class="admin-button admin-button--small" type="submit">
                    <x-admin.icon name="check" /> Tout marquer comme lu
                </button>
            </form>
        @endslot
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="notifications-list-title">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head">
                <h2 class="admin-card__title" id="notifications-list-title">Historique</h2>
                <span class="admin-muted admin-tiny">{{ $notifications->total() }} notification(s)</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="sr-only" hidden>Liste des notifications</caption>
                    <thead>
                        <tr>
                            <th scope="col">Message</th>
                            <th scope="col">Référence</th>
                            <th scope="col">Date</th>
                            <th scope="col">État</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notification)
                            @php
                                $donnees = $notification->data ?? [];
                            @endphp
                            <tr class="{{ $notification->read_at === null ? 'is-highlighted' : '' }}">
                                <td>{{ $donnees['message'] ?? 'Nouvelle notification' }}</td>
                                <td>
                                    @if (isset($donnees['pv_id']))
                                        <a class="admin-table__link" href="{{ route('pv.show', $donnees['pv_id']) }}">{{ $donnees['reference'] ?? 'PV' }}</a>
                                    @else
                                        <span class="admin-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $notification->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <x-admin.status :label="$notification->read_at ? 'Lue' : 'Non lue'" :tone="$notification->read_at ? 'slate' : 'gold'" />
                                </td>
                                <td>
                                    @if ($notification->read_at === null)
                                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                            @csrf
                                            <button class="admin-table__filter" type="submit" aria-label="Marquer comme lue">
                                                <x-admin.icon name="check" />
                                            </button>
                                        </form>
                                    @else
                                        <span class="admin-muted admin-tiny">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><span class="admin-empty">Aucune notification à afficher.</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-pagination">{{ $notifications->links() }}</div>
    </section>
@endsection
