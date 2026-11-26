@extends('layouts.admin')

@section('title', 'Rôles et permissions')

@section('content')
    @php
        $roleCards = [
            ['icon' => 'shield', 'name' => 'Administrateur', 'description' => 'Accès complet : archive les PV, administre les comptes, les rôles et les référentiels.', 'count' => '4 comptes'],
            ['icon' => 'file', 'name' => 'Agent de scolarité', 'description' => 'Enregistre et importe les PV, les scans et les résultats pour chaque promotion.', 'count' => '12 comptes'],
            ['icon' => 'roles', 'name' => 'Chef de département / Direction', 'description' => 'Valide les PV, consulte les fiches et les exports, et suit les statistiques de son département.', 'count' => '8 comptes'],
        ];

        $permissionGroups = [
            [
                'title' => 'PV et scans',
                'permissions' => [
                    ['key' => 'view_pv', 'label' => 'Consulter un PV', 'agent' => true, 'chef' => true],
                    ['key' => 'preview_scan', 'label' => "Voir l'aperçu du scan", 'agent' => true, 'chef' => true],
                    ['key' => 'download_scan', 'label' => 'Télécharger le scan', 'agent' => true, 'chef' => true],
                    ['key' => 'record_pv', 'label' => 'Enregistrer un PV', 'agent' => true, 'chef' => false],
                    ['key' => 'validate_pv', 'label' => 'Valider un PV', 'agent' => false, 'chef' => true],
                    ['key' => 'archive_pv', 'label' => 'Archiver un PV', 'agent' => false, 'chef' => false],
                    ['key' => 'unlock_pv', 'label' => 'Déverrouiller un PV', 'agent' => false, 'chef' => false],
                ],
            ],
            [
                'title' => 'Résultats',
                'permissions' => [
                    ['key' => 'import_results', 'label' => 'Importer les résultats', 'agent' => true, 'chef' => false],
                    ['key' => 'correct_results', 'label' => 'Corriger les résultats', 'agent' => true, 'chef' => false],
                ],
            ],
            [
                'title' => 'Recherche et consultation',
                'permissions' => [
                    ['key' => 'search_student', 'label' => 'Rechercher un étudiant', 'agent' => true, 'chef' => true],
                    ['key' => 'view_student', 'label' => 'Consulter une fiche étudiant', 'agent' => true, 'chef' => true],
                ],
            ],
            [
                'title' => 'Statistiques et exports',
                'permissions' => [
                    ['key' => 'view_dashboard', 'label' => 'Consulter le tableau de bord', 'agent' => true, 'chef' => true],
                    ['key' => 'export_data', 'label' => 'Exporter les données', 'agent' => false, 'chef' => true],
                    ['key' => 'create_signed_link', 'label' => 'Créer un lien signé', 'agent' => false, 'chef' => false],
                ],
            ],
            [
                'title' => 'Administration',
                'permissions' => [
                    ['key' => 'manage_users', 'label' => 'Gérer les utilisateurs', 'agent' => false, 'chef' => false],
                    ['key' => 'manage_referentiels', 'label' => 'Gérer les référentiels', 'agent' => false, 'chef' => false],
                    ['key' => 'manage_plan', 'label' => 'Gérer le plan de numérisation', 'agent' => false, 'chef' => false],
                    ['key' => 'view_audit', 'label' => "Consulter le journal d'audit", 'agent' => false, 'chef' => true],
                    ['key' => 'manage_settings', 'label' => 'Modifier les paramètres', 'agent' => false, 'chef' => false],
                ],
            ],
        ];
    @endphp

    <x-admin.page-header title="Rôles et permissions" subtitle="Définir précisément les droits de chaque profil sur les PV, les résultats et l'administration." :breadcrumb="['Accueil', 'Administration', 'Rôles et permissions']">
        @slot('actions')
            <a class="admin-button admin-button--secondary" href="{{ route('admin.users.index') }}"><x-admin.icon name="users" /> Voir les utilisateurs</a>
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
                <p class="admin-section__hint">19 permissions réparties en cinq domaines d'activité.</p>
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
                    <span class="admin-muted admin-tiny">3 profils · 19 permissions</span>
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
                                    <th scope="col">Chef de département / Direction</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissionGroups as $group)
                                    <tr class="admin-matrix__group">
                                        <th colspan="4" scope="colgroup">{{ $group['title'] }}</th>
                                    </tr>

                                    @foreach ($group['permissions'] as $permission)
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
                                                <x-admin.toggle name="perm_chef_{{ $permission['key'] }}" :checked="$permission['chef']" :label="'Permission Chef de département / Direction : '.$permission['label']" />
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
                <button class="admin-button" type="submit"><x-admin.icon name="check" /> Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection
