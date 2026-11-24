<?php

namespace Database\Seeders;

use App\Models\Filiere;
use App\Models\Specialite;
use Illuminate\Database\Seeder;

/** Exemple : IG => AIP, ARI. Les autres spécialités sont saisies par l'admin dans l'interface. */
class SpecialiteSeeder extends Seeder
{
    public function run(): void
    {
        $ig = Filiere::where('code', 'IG')->first();
        if (! $ig) {
            return;
        }

        Specialite::updateOrCreate(['filiere_id' => $ig->id, 'code' => 'AIP'], ['nom' => 'Analyse Informatique et Programmation', 'is_active' => true]);
        Specialite::updateOrCreate(['filiere_id' => $ig->id, 'code' => 'ARI'], ['nom' => 'ARI (nom complet à renseigner)', 'is_active' => true]);
    }
}
