<?php

return [
    // Disque de stockage des scans (privé : storage/app/private)
    'disk' => env('PV_DISK', 'local'),

    // Niveau où les spécialités s'appliquent (code du niveau)
    'niveau_specialite' => 'L3',

    // Taille max d'un scan en Ko
    'upload_max_ko' => 20480,

    // Autoriser le téléchargement du scan (l'aperçu en ligne reste toujours possible)
    'telechargement_autorise' => env('PV_TELECHARGEMENT', true),

    // Import des résultats : 'tout_ou_rien' ou 'valides_seulement'
    'import_mode' => 'tout_ou_rien',

    // Lien de partage signé (minutes)
    'partage_duree_defaut' => 60,
    'partage_duree_max' => 1440,

    // Alerte : un brouillon plus vieux que N jours est signalé
    'brouillon_alerte_jours' => 14,

    'password_temp_length' => 10,
];
