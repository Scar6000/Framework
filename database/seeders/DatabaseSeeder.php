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
            ReferentielEtAdminSeeder::class,
        ]);

        ActivityLogger::$actif = true;
    }
}
