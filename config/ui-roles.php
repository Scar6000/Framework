<?php

/*
 * Configuration d'affichage des trois profils de l'application.
 * TODO: remplacer la lecture de cette configuration par l'utilisateur authentifié
 * et les autorisations réelles lors du branchement du back-end.
 */

return [
    'roles' => [
        'admin' => [
            'label' => 'Administrateur',
            'profile' => [
                'name' => 'Dr Jean Théophile Agadamè',
                'role' => 'Administrateur',
                'initials' => 'JA',
                'email' => 'j.agadame@eneam.bj',
                'department' => 'Administration générale',
            ],
            'dashboard' => 'admin.dashboard',
            'restricted' => false,
            'sidebar' => [
                ['label' => 'Tableau de bord', 'route' => 'admin.dashboard', 'patterns' => ['admin.dashboard'], 'icon' => 'dashboard'],
                ['label' => 'Utilisateurs', 'route' => 'admin.users.index', 'patterns' => ['admin.users.*'], 'icon' => 'users'],
                ['label' => 'Rôles et permissions', 'route' => 'admin.roles.index', 'patterns' => ['admin.roles.*'], 'icon' => 'roles'],
                ['label' => 'Référentiels', 'route' => 'admin.referentiels.index', 'patterns' => ['admin.referentiels.*'], 'icon' => 'referentiels'],
                ['label' => 'Plan de numérisation', 'route' => 'admin.plan.index', 'patterns' => ['admin.plan.*'], 'icon' => 'plan'],
                ['label' => 'Registre des PV', 'route' => 'admin.pv.index', 'patterns' => ['admin.pv.*'], 'icon' => 'pv'],
                ['label' => 'Recherche', 'route' => 'admin.recherche.index', 'patterns' => ['admin.recherche.*', 'admin.etudiants.*'], 'icon' => 'search'],
                ['label' => "Journal d'audit", 'route' => 'admin.journal.index', 'patterns' => ['admin.journal.*'], 'icon' => 'journal'],
                ['label' => 'Exports et partages', 'route' => 'admin.exports.index', 'patterns' => ['admin.exports.*'], 'icon' => 'exports'],
            ],
            'addMenu' => [
                ['label' => 'Nouveau PV', 'route' => 'admin.pv.create', 'icon' => 'file'],
                ['label' => 'Import en masse de PV', 'route' => 'admin.pv.import', 'icon' => 'upload'],
                ['label' => 'Nouvel utilisateur', 'route' => 'admin.users.index', 'query' => ['creer' => 1], 'icon' => 'users'],
                ['label' => 'Nouvelle année académique', 'route' => 'admin.referentiels.index', 'query' => ['onglet' => 'annees'], 'icon' => 'calendar'],
                ['label' => 'Nouvelle filière', 'route' => 'admin.referentiels.index', 'query' => ['onglet' => 'filieres'], 'icon' => 'graduation'],
            ],
        ],
        'agent' => [
            'label' => 'Agent de scolarité',
            'profile' => [
                'name' => 'Marcelle Dossou',
                'role' => 'Agent de scolarité',
                'initials' => 'MD',
                'email' => 'm.dossou@eneam.bj',
                'department' => 'Service des examens',
            ],
            'dashboard' => 'agent.dashboard',
            'restricted' => false,
            'sidebar' => [
                ['label' => 'Tableau de bord', 'route' => 'agent.dashboard', 'patterns' => ['agent.dashboard'], 'icon' => 'dashboard'],
                ['label' => 'Registre des PV', 'route' => 'agent.pv.index', 'patterns' => ['agent.pv.index'], 'icon' => 'pv'],
                ['label' => 'Nouveau PV', 'route' => 'agent.pv.create', 'patterns' => ['agent.pv.create'], 'icon' => 'file'],
                ['label' => 'Import en masse de PV', 'route' => 'agent.pv.import', 'patterns' => ['agent.pv.import'], 'icon' => 'upload'],
                ['label' => 'Recherche', 'route' => 'agent.recherche.index', 'patterns' => ['agent.recherche.*', 'agent.etudiants.*'], 'icon' => 'search'],
            ],
            'addMenu' => [
                ['label' => 'Nouveau PV', 'route' => 'agent.pv.create', 'icon' => 'file'],
                ['label' => 'Import en masse de PV', 'route' => 'agent.pv.import', 'icon' => 'upload'],
                ['label' => 'Importer des résultats', 'route' => 'agent.pv.index', 'query' => ['resultats' => 1], 'icon' => 'layers'],
            ],
        ],
        'chef' => [
            'label' => 'Chef de département',
            'profile' => [
                'name' => 'Basile Hounton',
                'role' => 'Chef de département',
                'initials' => 'BH',
                'email' => 'b.hounton@eneam.bj',
                'department' => 'Informatique et Statistique',
                'filieres' => ['IG', 'STAT'],
            ],
            'dashboard' => 'chef.dashboard',
            'restricted' => true,
            'sidebar' => [
                ['label' => 'Tableau de bord', 'route' => 'chef.dashboard', 'patterns' => ['chef.dashboard'], 'icon' => 'dashboard'],
                ['label' => 'Registre des PV', 'route' => 'chef.pv.index', 'patterns' => ['chef.pv.index'], 'icon' => 'pv'],
                ['label' => 'Recherche', 'route' => 'chef.recherche.index', 'patterns' => ['chef.recherche.*', 'chef.etudiants.*'], 'icon' => 'search'],
            ],
            'addMenu' => [],
        ],
    ],

    // Matrice d'affichage des droits. La colonne administrateur reste verrouillée.
    'permissions' => [
        ['label' => 'Voir le registre des PV', 'admin' => true, 'agent' => true, 'chef' => true],
        ['label' => 'Aperçu du scan en ligne', 'admin' => true, 'agent' => true, 'chef' => true],
        ['label' => 'Télécharger le scan', 'admin' => true, 'agent' => true, 'chef' => false],
        ['label' => 'Créer / enregistrer un PV', 'admin' => true, 'agent' => true, 'chef' => false],
        ['label' => 'Import en masse de PV', 'admin' => true, 'agent' => true, 'chef' => false],
        ['label' => 'Modifier un PV en brouillon', 'admin' => true, 'agent' => true, 'chef' => false],
        ['label' => 'Archiver un PV', 'admin' => true, 'agent' => true, 'chef' => false],
        ['label' => 'Modifier un PV archivé (avec motif)', 'admin' => true, 'agent' => false, 'chef' => false],
        ['label' => 'Déverrouiller un PV archivé', 'admin' => true, 'agent' => false, 'chef' => false],
        ['label' => 'Saisir / importer les résultats', 'admin' => true, 'agent' => true, 'chef' => false],
        ['label' => 'Recherche multicritère et fiche étudiant', 'admin' => true, 'agent' => true, 'chef' => true],
        ['label' => 'Tableau de bord statistique', 'admin' => true, 'agent' => true, 'chef' => true],
        ['label' => 'Exports Excel / PDF', 'admin' => true, 'agent' => true, 'chef' => false],
        ['label' => 'Journal, comptes, rôles, référentiels, paramètres', 'admin' => true, 'agent' => false, 'chef' => false],
    ],
];
