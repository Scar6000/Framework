<?php

namespace Database\Seeders;

use App\Models\Filiere;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        $filieres = [
            'IG' => 'Informatique de Gestion',
            'GCO' => 'Gestion Commerciale',
            'GBA' => 'Gestion des Banques et Assurances',
            'GTL' => 'Gestion des Transports et Logistiques',
            'GFC' => 'Gestion Financière et Comptable',
            'STAT' => 'Statistiques',
            'PLAN' => 'Planification',
            'GRH' => 'Gestion des Ressources Humaines',
        ];

        foreach ($filieres as $code => $nom) {
            Filiere::updateOrCreate(['code' => $code], ['nom' => $nom, 'is_active' => true]);
        }
    }
}
