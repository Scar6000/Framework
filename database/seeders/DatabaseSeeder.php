<?php

namespace Database\Seeders;

use App\Services\ActivityLogger;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        ActivityLogger::$actif = false; // pas de bruit dans le journal pendant le seed

        $this->call([
            FiliereSeeder::class,
            NiveauSemestreSeeder::class,
            SpecialiteSeeder::class,
            AnneeAcademiqueSeeder::class,
            AdminSeeder::class,
        ]);

        ActivityLogger::$actif = true;
    }
}
