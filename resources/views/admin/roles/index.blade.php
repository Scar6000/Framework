@extends('layouts.admin')

@section('title', 'Rôles et permissions')

@section('content')
    @php
        $roleCards = [
            ['icon' => 'shield', 'name' => 'Administrateur', 'description' => 'Accès complet : archive les PV, administre les comptes, les rôles et les référentiels.', 'count' => '4 comptes'],
            ['icon' => 'file', 'name' => 'Agent de scolarité', 'description' => 'Enregistre et importe les PV, les scans et les résultats pour chaque promotion.', 'count' => '12 comptes'],
            ['icon' => 'roles', 'name' => 'Chef de département', 'description' => 'Consulte le registre des PV, les fiches étudiantes et les tableaux de bord statistiques de son département.', 'count' => '8 comptes'],
        ];

        $permissionGroups = [
            'PV et scans' => [
                'Voir le registre des PV',
                'Aperçu du scan en ligne',
                'Télécharger le scan',
                'Créer / enregistrer un PV',
                'Import en masse de PV',
                'Modifier un PV en brouillon',
                'Archiver un PV',
                'Modifier un PV archivé (avec motif)',
                'Déverrouiller un PV archivé',
            ],
            'Résultats' => ['Saisir / importer les résultats'],
            'Recherche et consultation' => ['Recherche multicritère et fiche étudiant'],
            'Statistiques et exports' => ['Tableau de bord statistique', 'Exports Excel / PDF'],
            'Administration' => ['Journal, comptes, rôles, référentiels, paramètres'],
        ];

        $permissions = [];
        foreach (config('ui-roles.permissions', []) as $permissionIndex => $permission) {
            $permissions[] = ['key' => 'p'.$permissionIndex] + $permission;
        }

        $permissionCount = count($permissions);
    @endphp

    <x-admin.page-header title="Rôles et permissions" subtitle="Définir précisément les droits de chaque profil sur les PV, les résultats et l'administration." :breadcrumb="['Accueil', 'Administration', 'Rôles et permissions']">
        @slot('actions')
            <x-ui.btn variant="secondary" :href="route('admin.users.index')" icon="users">Voir les utilisateurs</x-ui.btn>
        @endslot
    </x-admin.page-header>

    <section class="admin-section" aria-labelledby="admin-roles-cards-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-roles-cards-title">Profils disponibles</h2>
                <p class="admin-section__hint">Trois profils couvrent l'ensemble des comptes de l'application.</p>
            </div>
        </div>

        <div class="admin-role-grid">
            @foreach ($roleCards as $card)
                <article class="admin-role-card">
                    <div class="admin-role-card__head">
                        <span class="admin-role-card__icon"><x-admin.icon name="{{ $card['icon'] }}" /></span>
                        <h3 class="admin-role-card__name">{{ $card['name'] }}</h3>
                    </div>
                    <p class="admin-role-card__description">{{ $card['description'] }}</p>
                    <span class="admin-role-card__count">{{ $card['count'] }}</span>
                </article>
            @endforeach
        </div>
    </section>

    <section class="admin-section" aria-labelledby="admin-matrix-title">
        <div class="admin-section__head">
            <div>
                <h2 class="admin-section__title" id="admin-matrix-title">Matrice des permissions</h2>
                <p class="admin-section__hint">{{ $permissionCount }} permissions réparties en {{ count($permissionGroups) }} domaines d'activité.</p>
            </div>
        </div>

        <div class="admin-notice">
            <x-admin.icon name="info" />
            <span>Les droits de l'Administrateur sont verrouillés : ils ne peuvent pas être modifiés depuis cette matrice.</span>
        </div>

        <form action="#" method="POST" class="admin-section">
            {{-- TODO: relier à la route de mise à jour des permissions --}}
            @csrf
            @method('PUT')

            <div class="admin-card admin-card--flush">
                <div class="admin-card__head">
                    <h3 class="admin-card__title">Droits par profil</h3>
                    <span class="admin-muted admin-tiny">3 profils · {{ $permissionCount }} permissions</span>
                </div>

                <div class="admin-card__body">
                    <div class="admin-table-wrap">
                        <table class="admin-matrix">
                            <caption class="sr-only" hidden>Matrice des permissions par profil</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Permission</th>
                                    <th scope="col">Administrateur</th>
                                    <th scope="col">Agent de scolarité</th>
                                    <th scope="col">Chef de département</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissionGroups as $groupTitle => $groupLabels)
                                    <tr class="admin-matrix__group">
                                        <th colspan="4" scope="colgroup">{{ $groupTitle }}</th>
                                    </tr>

                                    @foreach ($permissions as $permission)
                                        @continue(! in_array($permission['label'], $groupLabels, true))
                                        <tr>
                                            <th scope="row">{{ $permission['label'] }}</th>
                                            <td>
                                                <x-admin.toggle name="perm_admin_{{ $permission['key'] }}" :checked="true" :disabled="true" :label="'Permission Administrateur : '.$permission['label'].' (verrouillée)'" />
                                                <span class="admin-matrix__locked"><x-admin.icon name="lock" /> Verrouillé</span>
                                            </td>
                                            <td>
                                                <x-admin.toggle name="perm_agent_{{ $permission['key'] }}" :checked="$permission['agent']" :label="'Permission Agent de scolarité : '.$permission['label']" />
                                            </td>
                                            <td>
                                                <x-admin.toggle name="perm_chef_{{ $permission['key'] }}" :checked="$permission['chef']" :label="'Permission Chef de département : '.$permission['label']" />
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="admin-save-bar">
                <span class="admin-muted">Les modifications prennent effet après enregistrement.</span>
                <x-ui.btn variant="primary" type="submit" icon="check">Enregistrer les modifications</x-ui.btn>
            </div>
        </form>
    </section>
@endsection
