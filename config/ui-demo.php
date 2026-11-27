<?php

/*
 * Données fictives de démonstration, partagées par les trois espaces.
 * TODO: remplacer par les données de l'application lors du branchement du back-end.
 */

return [
    'annees' => ['2025-2026', '2024-2025', '2023-2024'],
    'niveaux' => ['L1', 'L2', 'L3', 'M1', 'M2'],
    'sessions' => ['Normale', 'Rattrapage'],

    'filieres' => [
        ['code' => 'IG', 'label' => 'Informatique de gestion', 'department' => 'Informatique et Statistique'],
        ['code' => 'BF', 'label' => 'Banque et finance', 'department' => 'Économie et gestion'],
        ['code' => 'CCA', 'label' => 'Comptabilité, contrôle et audit', 'department' => 'Économie et gestion'],
        ['code' => 'GRH', 'label' => 'Gestion des ressources humaines', 'department' => 'Économie et gestion'],
        ['code' => 'MKT', 'label' => 'Marketing', 'department' => 'Économie et gestion'],
        ['code' => 'STAT', 'label' => 'Statistique', 'department' => 'Informatique et Statistique'],
        ['code' => 'PLAN', 'label' => 'Planification', 'department' => 'Informatique et Statistique'],
    ],

    'pvs' => [
        [
            'reference' => 'PV-2026-IG-L3-014',
            'annee' => '2025-2026', 'filiere' => 'IG', 'niveau' => 'L3', 'session' => 'Normale',
            'date' => '2026-07-14', 'president' => 'Pr Aurélien Kossi', 'membres' => ['Dr Nadine Sossou', 'M. Coffi Djedje'],
            'status' => 'Archivé', 'enregistre_par' => 'Marcelle Dossou', 'date_enregistrement' => '2026-07-15', 'pages' => 6,
        ],
        [
            'reference' => 'PV-2026-BF-L2-008',
            'annee' => '2025-2026', 'filiere' => 'BF', 'niveau' => 'L2', 'session' => 'Normale',
            'date' => '2026-07-10', 'president' => 'Pr Émile Dagnon', 'membres' => ['Dr Lise Ahouandjinou'],
            'status' => 'Brouillon', 'enregistre_par' => 'Marcelle Dossou', 'date_enregistrement' => '2026-07-11', 'pages' => 4,
        ],
        [
            'reference' => 'PV-2026-CCA-L3-021',
            'annee' => '2025-2026', 'filiere' => 'CCA', 'niveau' => 'L3', 'session' => 'Rattrapage',
            'date' => '2026-09-05', 'president' => 'Dr Chantal Gbaguidi', 'membres' => ['M. Théophile Hounsa', 'Mme Alice Zohoun'],
            'status' => 'Archivé', 'enregistre_par' => 'Marcelle Dossou', 'date_enregistrement' => '2026-09-06', 'pages' => 5,
        ],
        [
            'reference' => 'PV-2026-GRH-L1-006',
            'annee' => '2025-2026', 'filiere' => 'GRH', 'niveau' => 'L1', 'session' => 'Normale',
            'date' => '2026-07-08', 'president' => 'Dr Rosine Ayedoun', 'membres' => ['M. Kofi Assogba'],
            'status' => 'Brouillon', 'enregistre_par' => 'Marcelle Dossou', 'date_enregistrement' => '2026-07-09', 'pages' => 3,
        ],
        [
            'reference' => 'PV-2026-MKT-L2-012',
            'annee' => '2025-2026', 'filiere' => 'MKT', 'niveau' => 'L2', 'session' => 'Normale',
            'date' => '2026-07-13', 'president' => 'Promain Hounkpatin', 'membres' => ['Dr Giséle Toklo'],
            'status' => 'Archivé', 'enregistre_par' => 'Marcelle Dossou', 'date_enregistrement' => '2026-07-14', 'pages' => 5,
        ],
        [
            'reference' => 'PV-2026-STAT-L3-005',
            'annee' => '2025-2026', 'filiere' => 'STAT', 'niveau' => 'L3', 'session' => 'Normale',
            'date' => '2026-07-16', 'president' => 'Dr Basile Hounton', 'membres' => ['Mme Clarisse Gbènamè', 'M. Abel Kikirê'],
            'status' => 'Archivé', 'enregistre_par' => 'Marcelle Dossou', 'date_enregistrement' => '2026-07-17', 'pages' => 6,
        ],
        [
            'reference' => 'PV-2026-STAT-L2-003',
            'annee' => '2025-2026', 'filiere' => 'STAT', 'niveau' => 'L2', 'session' => 'Rattrapage',
            'date' => '2026-09-04', 'president' => 'Dr Basile Hounton', 'membres' => ['Mme Clarisse Gbènamè'],
            'status' => 'Brouillon', 'enregistre_par' => 'Marcelle Dossou', 'date_enregistrement' => '2026-09-05', 'pages' => 4,
        ],
        [
            'reference' => 'PV-2025-IG-L2-017',
            'annee' => '2024-2025', 'filiere' => 'IG', 'niveau' => 'L2', 'session' => 'Normale',
            'date' => '2025-07-11', 'president' => 'Pr Aurélien Kossi', 'membres' => ['Dr Nadine Sossou'],
            'status' => 'Archivé', 'enregistre_par' => 'Marcelle Dossou', 'date_enregistrement' => '2025-07-12', 'pages' => 5,
        ],
    ],

    'resultats' => [
        ['matricule' => '2024-IG-018', 'nom' => 'Aminata Soglo', 'moyenne' => '14,25', 'credits' => 60, 'decision' => 'Admis'],
        ['matricule' => '2024-IG-024', 'nom' => 'Koffi Ahouansou', 'moyenne' => '11,80', 'credits' => 54, 'decision' => 'Admis'],
        ['matricule' => '2024-IG-031', 'nom' => 'Chantal Dossou-Yovo', 'moyenne' => '09,45', 'credits' => 42, 'decision' => 'Rattrapage'],
        ['matricule' => '2024-IG-042', 'nom' => 'Yannick Assogba', 'moyenne' => '07,60', 'credits' => 30, 'decision' => 'Ajourné'],
    ],

    'etudiants' => [
        [
            'matricule' => '2024-IG-018', 'nom' => 'Aminata Soglo', 'filiere' => 'IG', 'niveau' => 'L3', 'department' => 'Informatique et Statistique',
            'parcours' => [
                ['annee' => '2025-2026', 'filiere' => 'IG', 'niveau' => 'L3', 'session' => 'Normale', 'moyenne' => '14,25', 'credits' => 60, 'decision' => 'Admis', 'reference' => 'PV-2026-IG-L3-014'],
                ['annee' => '2024-2025', 'filiere' => 'IG', 'niveau' => 'L2', 'session' => 'Normale', 'moyenne' => '13,10', 'credits' => 60, 'decision' => 'Admis', 'reference' => 'PV-2025-IG-L2-017'],
                ['annee' => '2023-2024', 'filiere' => 'IG', 'niveau' => 'L1', 'session' => 'Normale', 'moyenne' => '12,75', 'credits' => 60, 'decision' => 'Admis', 'reference' => 'PV-2024-IG-L1-011'],
            ],
        ],
        [
            'matricule' => '2024-STAT-007', 'nom' => 'Yannick Assogba', 'filiere' => 'STAT', 'niveau' => 'L3', 'department' => 'Informatique et Statistique',
            'parcours' => [
                ['annee' => '2025-2026', 'filiere' => 'STAT', 'niveau' => 'L3', 'session' => 'Rattrapage', 'moyenne' => '10,20', 'credits' => 48, 'decision' => 'Rattrapage', 'reference' => 'PV-2026-STAT-L3-009'],
                ['annee' => '2024-2025', 'filiere' => 'STAT', 'niveau' => 'L3', 'session' => 'Normale', 'moyenne' => '08,90', 'credits' => 36, 'decision' => 'Ajourné', 'reference' => 'PV-2025-STAT-L3-004'],
                ['annee' => '2023-2024', 'filiere' => 'STAT', 'niveau' => 'L2', 'session' => 'Normale', 'moyenne' => '12,05', 'credits' => 60, 'decision' => 'Admis', 'reference' => 'PV-2024-STAT-L2-008'],
            ],
        ],
    ],

    'journal' => [
        ['type' => 'Création', 'objet' => 'PV-2026-IG-L3-014', 'detail' => 'Enregistrement du PV et du scan associé', 'auteur' => 'Marcelle Dossou', 'date' => '2026-07-15 09:12'],
        ['type' => 'Import en masse (12 PV)', 'objet' => 'Import en masse', 'detail' => '12 PV enregistrés et archivés', 'auteur' => 'Marcelle Dossou', 'date' => '2026-07-16 14:05'],
        ['type' => 'Modification', 'objet' => 'PV-2026-BF-L2-008', 'detail' => 'Correction de la date de délibération', 'auteur' => 'Marcelle Dossou', 'date' => '2026-07-18 10:34'],
        ['type' => 'Archivage', 'objet' => 'PV-2026-STAT-L3-005', 'detail' => 'PV enregistré définitivement', 'auteur' => 'Marcelle Dossou', 'date' => '2026-07-17 16:20'],
        ['type' => 'Déverrouillage', 'objet' => 'PV-2026-MKT-L2-012', 'detail' => 'Motif : correction du président du jury', 'auteur' => 'Dr Jean Théophile Agadamè', 'date' => '2026-08-02 11:40'],
        ['type' => 'Consultation', 'objet' => 'PV-2026-IG-L3-014', 'detail' => 'Aperçu du scan en ligne', 'auteur' => 'Basile Hounton', 'date' => '2026-08-05 08:55'],
    ],
];
