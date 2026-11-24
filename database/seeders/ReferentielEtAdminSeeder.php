<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReferentielEtAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anneeActive = AnneeAcademique::updateOrCreate(
            ['libelle' => '2025-2026'],
            ['est_active' => true]
        );

        AnneeAcademique::updateOrCreate(
            ['libelle' => '2026-2027'],
            ['est_active' => false]
        );

        $filieres = [
            ['code' => 'IG', 'nom' => 'Informatique de Gestion', 'departement' => 'Gestion'],
            ['code' => 'GFC', 'nom' => 'Gestion Finance et Comptabilité', 'departement' => 'Gestion'],
            ['code' => 'STAT', 'nom' => 'Statistique', 'departement' => 'Planification et Statistique'],
        ];

        foreach ($filieres as $filiere) {
            Filiere::updateOrCreate(
                ['code' => $filiere['code']],
                [
                    'nom' => $filiere['nom'],
                    'departement' => $filiere['departement'],
                ]
            );
        }

        $niveaux = [
            ['code' => 'L1', 'libelle' => 'Licence 1'],
            ['code' => 'L2', 'libelle' => 'Licence 2'],
            ['code' => 'L3', 'libelle' => 'Licence 3'],
        ];

        $niveauxCrees = [];
        foreach ($niveaux as $niveau) {
            $niveauxCrees[$niveau['code']] = Niveau::updateOrCreate(
                ['code' => $niveau['code']],
                ['libelle' => $niveau['libelle']]
            );
        }

        $semestres = [
            ['code' => 'S1', 'libelle' => 'Semestre 1', 'niveau_id' => $niveauxCrees['L1']->id],
            ['code' => 'S2', 'libelle' => 'Semestre 2', 'niveau_id' => $niveauxCrees['L1']->id],
            ['code' => 'S3', 'libelle' => 'Semestre 3', 'niveau_id' => $niveauxCrees['L2']->id],
            ['code' => 'S4', 'libelle' => 'Semestre 4', 'niveau_id' => $niveauxCrees['L2']->id],
            ['code' => 'S5', 'libelle' => 'Semestre 5', 'niveau_id' => $niveauxCrees['L3']->id],
            ['code' => 'S6', 'libelle' => 'Semestre 6', 'niveau_id' => $niveauxCrees['L3']->id],
        ];

        foreach ($semestres as $semestre) {
            Semestre::updateOrCreate(
                ['code' => $semestre['code']],
                [
                    'libelle' => $semestre['libelle'],
                    'niveau_id' => $semestre['niveau_id'],
                ]
            );
        }

        User::updateOrCreate(
            ['email' => 'admin@eneam.bj'],
            [
                'name' => 'Administrateur ENEAM',
                'role' => 'admin',
                'filiere_id' => null,
                'password' => Hash::make('Admin@2026'),
            ]
        );
    }
}