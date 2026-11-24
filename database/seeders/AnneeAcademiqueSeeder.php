<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use Illuminate\Database\Seeder;

class AnneeAcademiqueSeeder extends Seeder
{
    public function run(): void
    {
        AnneeAcademique::updateOrCreate(
            ['libelle' => '2025-2026'],
            ['date_debut' => '2025-10-01', 'date_fin' => '2026-09-30', 'est_en_cours' => true]
        );
    }
}
