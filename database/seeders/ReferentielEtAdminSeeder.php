<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReferentielEtAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Filières
        $filieres = [
            ['code' => 'IG', 'nom' => 'Informatique de Gestion', 'is_active' => true],
            ['code' => 'BF', 'nom' => 'Banque et Finance', 'is_active' => true],
            ['code' => 'CG', 'nom' => 'Comptabilité et Gestion', 'is_active' => true],
            ['code' => 'MK', 'nom' => 'Marketing et Communication', 'is_active' => true],
        ];

        foreach ($filieres as $filiere) {
            DB::table('filieres')->updateOrInsert(
                ['code' => $filiere['code']],
                array_merge($filiere, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 2. Années académiques
        $annees = [
            [
                'libelle' => '2024-2025',
                'date_debut' => '2024-10-01',
                'date_fin' => '2025-07-31',
                'est_en_cours' => false,
            ],
            [
                'libelle' => '2025-2026',
                'date_debut' => '2025-10-01',
                'date_fin' => '2026-07-31',
                'est_en_cours' => true,
            ],
        ];

        foreach ($annees as $annee) {
            DB::table('annees_academiques')->updateOrInsert(
                ['libelle' => $annee['libelle']],
                array_merge($annee, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 3. Niveaux
        $niveaux = [
            ['code' => 'L1', 'libelle' => 'Licence 1', 'ordre' => 1],
            ['code' => 'L2', 'libelle' => 'Licence 2', 'ordre' => 2],
            ['code' => 'L3', 'libelle' => 'Licence 3', 'ordre' => 3],
        ];

        foreach ($niveaux as $niveau) {
            DB::table('niveaux')->updateOrInsert(
                ['code' => $niveau['code']],
                array_merge($niveau, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $niveauIds = DB::table('niveaux')->pluck('id', 'code');

        // 4. Semestres
        $semestres = [
            ['code' => 'S1', 'libelle' => 'Semestre 1', 'niveau_id' => $niveauIds['L1']],
            ['code' => 'S2', 'libelle' => 'Semestre 2', 'niveau_id' => $niveauIds['L1']],
            ['code' => 'S3', 'libelle' => 'Semestre 3', 'niveau_id' => $niveauIds['L2']],
            ['code' => 'S4', 'libelle' => 'Semestre 4', 'niveau_id' => $niveauIds['L2']],
            ['code' => 'S5', 'libelle' => 'Semestre 5', 'niveau_id' => $niveauIds['L3']],
            ['code' => 'S6', 'libelle' => 'Semestre 6', 'niveau_id' => $niveauIds['L3']],
        ];

        foreach ($semestres as $semestre) {
            DB::table('semestres')->updateOrInsert(
                ['code' => $semestre['code']],
                array_merge($semestre, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $igId = DB::table('filieres')->where('code', 'IG')->value('id');

        // 5. Utilisateurs de base (admin, scolarite, chef IG)
        $users = [
            [
                'nom' => 'ADMIN',
                'prenoms' => 'Système',
                'email' => 'admin@eneam.bj',
                'password' => Hash::make('Admin@2026'),
                'role' => 'admin',
                'filiere_id' => null,
                'is_active' => true,
                'must_change_password' => false,
            ],
            [
                'nom' => 'SCOLARITE',
                'prenoms' => 'Service',
                'email' => 'scolarite@eneam.bj',
                'password' => Hash::make('Scolarite@2026'),
                'role' => 'scolarite',
                'filiere_id' => null,
                'is_active' => true,
                'must_change_password' => false,
            ],
            [
                'nom' => 'CHEF_IG',
                'prenoms' => 'Département',
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
                array_merge($user, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}