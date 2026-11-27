<?php

namespace Database\Seeders;

use App\Models\Niveau;
use App\Models\Semestre;
use Illuminate\Database\Seeder;

class NiveauSemestreSeeder extends Seeder
{
    public function run(): void
    {
        $niveaux = ['L1' => 'Licence 1', 'L2' => 'Licence 2', 'L3' => 'Licence 3'];
        $ordre = 1;
        $ids = [];
        foreach ($niveaux as $code => $libelle) {
            $ids[$code] = Niveau::updateOrCreate(['code' => $code], ['libelle' => $libelle, 'ordre' => $ordre++])->id;
        }

        // S1,S2 => L1 · S3,S4 => L2 · S5,S6 => L3
        foreach (range(1, 6) as $n) {
            $niveau = 'L'.(int) ceil($n / 2);
            Semestre::updateOrCreate(['code' => 'S'.$n], ['libelle' => 'Semestre '.$n, 'niveau_id' => $ids[$niveau]]);
        }
    }
}
