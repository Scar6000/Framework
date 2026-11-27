<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** Jeu de test des trois profils ENEAM, sans semestre ni statut « validé ». */
class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $filieres = [
            ['code' => 'IG', 'nom' => 'Informatique de gestion', 'is_active' => true],
            ['code' => 'STAT', 'nom' => 'Statistique', 'is_active' => true],
            ['code' => 'BF', 'nom' => 'Banque et finance', 'is_active' => true],
            ['code' => 'CG', 'nom' => 'Comptabilité et gestion', 'is_active' => true],
        ];

        foreach ($filieres as $filiere) {
            DB::table('filieres')->updateOrInsert(
                ['code' => $filiere['code']],
                $filiere + ['created_at' => $now, 'updated_at' => $now]
            );
        }

        DB::table('annees_academiques')->updateOrInsert(
            ['libelle' => '2025-2026'],
            [
                'libelle' => '2025-2026',
                'date_debut' => '2025-10-01',
                'date_fin' => '2026-07-31',
                'est_en_cours' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $niveaux = [
            'L1' => 'Licence 1',
            'L2' => 'Licence 2',
            'L3' => 'Licence 3',
        ];

        foreach ($niveaux as $code => $libelle) {
            DB::table('niveaux')->updateOrInsert(
                ['code' => $code],
                ['code' => $code, 'libelle' => $libelle, 'ordre' => (int) substr($code, 1), 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $niveauIds = DB::table('niveaux')->pluck('id', 'code');
        $semestreByNiveau = ['L1' => 'S1', 'L2' => 'S3', 'L3' => 'S5'];

        foreach ($semestreByNiveau as $niveau => $code) {
            DB::table('semestres')->updateOrInsert(
                ['code' => $code],
                ['code' => $code, 'libelle' => $code, 'niveau_id' => $niveauIds[$niveau], 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $anneeId = DB::table('annees_academiques')->where('libelle', '2025-2026')->value('id');
        $semestreIds = DB::table('semestres')->pluck('id', 'code');

        foreach ($filieres as $filiere) {
            $filiereId = DB::table('filieres')->where('code', $filiere['code'])->value('id');

            foreach ($semestreByNiveau as $niveau => $codeSemestre) {
                DB::table('promotions')->updateOrInsert(
                    [
                        'annee_academique_id' => $anneeId,
                        'filiere_id' => $filiereId,
                        'semestre_id' => $semestreIds[$codeSemestre],
                    ],
                    [
                        'est_attendue' => true,
                        'date_limite' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        $igId = DB::table('filieres')->where('code', 'IG')->value('id');

        $users = [
            [
                'nom' => 'Agadamè',
                'prenoms' => 'Dr Jean Théophile',
                'email' => 'admin@eneam.bj',
                'password' => Hash::make('Admin@2026'),
                'role' => 'admin',
                'filiere_id' => null,
                'is_active' => true,
                'must_change_password' => false,
            ],
            [
                'nom' => 'Dossou',
                'prenoms' => 'Mme Marcelle',
                'email' => 'scolarite@eneam.bj',
                'password' => Hash::make('Scolarite@2026'),
                'role' => 'scolarite',
                'filiere_id' => null,
                'is_active' => true,
                'must_change_password' => false,
            ],
            [
                'nom' => 'Hounton',
                'prenoms' => 'M. Basile',
                'email' => 'chef.ig@eneam.bj',
                'password' => Hash::make('ChefIg@2026'),
                'role' => 'chef',
                'filiere_id' => $igId,
                'is_active' => true,
                'must_change_password' => false,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user + ['last_login_at' => null, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $adminId = DB::table('users')->where('email', 'admin@eneam.bj')->value('id');
        $agentId = DB::table('users')->where('email', 'scolarite@eneam.bj')->value('id');

        $pvs = [
            [
                'reference' => 'PV-2026-IG-L3-014',
                'code_filiere' => 'IG', 'niveau' => 'L3', 'session' => 'normale',
                'date_deliberation' => '2026-07-14', 'president_jury' => 'Pr Aurélien Kossi',
                'membres_jury' => ['Dr Nadine Sossou', 'M. Coffi Djedje'], 'statut' => 'archive', 'created_by' => $agentId,
            ],
            [
                'reference' => 'PV-2026-IG-L2-008',
                'code_filiere' => 'IG', 'niveau' => 'L2', 'session' => 'normale',
                'date_deliberation' => '2026-07-10', 'president_jury' => 'Pr Émile Dagnon',
                'membres_jury' => ['Dr Lise Ahouandjinou'], 'statut' => 'brouillon', 'created_by' => $agentId,
            ],
            [
                'reference' => 'PV-2026-STAT-L3-005',
                'code_filiere' => 'STAT', 'niveau' => 'L3', 'session' => 'normale',
                'date_deliberation' => '2026-07-16', 'president_jury' => 'Dr Basile Hounton',
                'membres_jury' => ['Mme Clarisse Gbènamè'], 'statut' => 'archive', 'created_by' => $adminId,
            ],
            [
                'reference' => 'PV-2026-BF-L2-003',
                'code_filiere' => 'BF', 'niveau' => 'L2', 'session' => 'rattrapage',
                'date_deliberation' => '2026-09-05', 'president_jury' => 'Dr Chantal Gbaguidi',
                'membres_jury' => ['M. Théophile Hounsa'], 'statut' => 'archive', 'created_by' => $agentId,
            ],
        ];

        foreach ($pvs as $pv) {
            $filiereId = DB::table('filieres')->where('code', $pv['code_filiere'])->value('id');
            $promotionId = DB::table('promotions')
                ->where('filiere_id', $filiereId)
                ->where('annee_academique_id', $anneeId)
                ->where('semestre_id', $semestreIds[$semestreByNiveau[$pv['niveau']]])
                ->value('id');

            DB::table('pvs')->updateOrInsert(
                ['reference' => $pv['reference']],
                [
                    'promotion_id' => $promotionId,
                    'session' => $pv['session'],
                    'date_deliberation' => $pv['date_deliberation'],
                    'president_jury' => $pv['president_jury'],
                    'membres_jury' => json_encode($pv['membres_jury'], JSON_UNESCAPED_UNICODE),
                    'fichier_path' => 'pv/demo/'.$pv['reference'].'.pdf',
                    'fichier_nom_original' => $pv['reference'].'.pdf',
                    'fichier_mime' => 'application/pdf',
                    'fichier_taille' => 245760,
                    'statut' => $pv['statut'],
                    'created_by' => $pv['created_by'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $etudiants = [
            [
                'matricule' => '2026-IG-018',
                'nom' => 'Soglo',
                'prenoms' => 'Aminata',
                'resultats' => [
                    ['reference' => 'PV-2026-IG-L3-014', 'moyenne' => '14.25', 'decision' => 'admis'],
                    ['reference' => 'PV-2026-IG-L2-008', 'moyenne' => '13.10', 'decision' => 'admis'],
                ],
            ],
            [
                'matricule' => '2026-STAT-007',
                'nom' => 'Assogba',
                'prenoms' => 'Yannick',
                'resultats' => [
                    ['reference' => 'PV-2026-STAT-L3-005', 'moyenne' => '10.20', 'decision' => 'rattrapage'],
                ],
            ],
            [
                'matricule' => '2026-BF-011',
                'nom' => 'Diallo',
                'prenoms' => 'Mamadou',
                'resultats' => [
                    ['reference' => 'PV-2026-BF-L2-003', 'moyenne' => '08.90', 'decision' => 'ajourne'],
                ],
            ],
        ];

        foreach ($etudiants as $etudiant) {
            DB::table('etudiants')->updateOrInsert(
                ['matricule' => $etudiant['matricule']],
                [
                    'nom' => $etudiant['nom'],
                    'prenoms' => $etudiant['prenoms'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $etudiantId = DB::table('etudiants')->where('matricule', $etudiant['matricule'])->value('id');

            foreach ($etudiant['resultats'] as $resultat) {
                $pvId = DB::table('pvs')->where('reference', $resultat['reference'])->value('id');

                if ($pvId === null) {
                    continue;
                }

                DB::table('lignes_resultats')->updateOrInsert(
                    ['pv_id' => $pvId, 'etudiant_id' => $etudiantId],
                    [
                        'moyenne_generale' => $resultat['moyenne'],
                        'decision' => $resultat['decision'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        foreach (DB::table('pvs')->pluck('id', 'reference') as $reference => $pvId) {
            foreach (['creation' => 'Enregistrement du PV et du scan associé', 'archivage' => 'PV archivé'] as $action => $description) {
                DB::table('activity_logs')->updateOrInsert(
                    ['entite' => 'pv', 'entite_id' => $pvId, 'action' => $action],
                    [
                        'user_id' => $agentId,
                        'description' => $description.' — '.$reference,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }
}
