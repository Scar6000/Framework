<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\SessionPv;
use App\Enums\StatutPv;
use App\Models\AnneeAcademique;
use App\Models\Etudiant;
use App\Models\Filiere;
use App\Models\Promotion;
use App\Models\Pv;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\PromotionGenerator;
use Illuminate\Database\Seeder;

/**
 * Données de démo pour le front : php artisan db:seed --class=DemoSeeder
 * Tous les comptes ont le mot de passe « password ».
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        ActivityLogger::$actif = false;

        $agent = User::updateOrCreate(['email' => 'agent@eneam.test'], [
            'name' => 'AGBO', 'prenoms' => 'Mireille', 'password' => 'password',
            'role' => Role::Scolarite, 'is_active' => true, 'must_change_password' => false,
        ]);

        foreach (Filiere::all() as $f) {
            User::updateOrCreate(['email' => 'chef.'.strtolower($f->code).'@eneam.test'], [
                'name' => 'CHEF', 'prenoms' => $f->code, 'password' => 'password',
                'role' => Role::Chef, 'filiere_id' => $f->id, 'is_active' => true, 'must_change_password' => false,
            ]);
        }

        $annee = AnneeAcademique::enCours();
        app(PromotionGenerator::class)->generer($annee);

        $ig = Filiere::where('code', 'IG')->first();
        $promos = Promotion::where('annee_academique_id', $annee->id)->where('filiere_id', $ig->id)
            ->whereHas('semestre', fn ($q) => $q->whereIn('code', ['S1', 'S2']))->get();

        $etudiants = collect([
            ['IG2025001', 'ADJOVI', 'Koffi'], ['IG2025002', 'HOUNKPATIN', 'Aïcha'],
            ['IG2025003', 'DOSSOU', 'Marc'], ['IG2025004', 'TOGBE', 'Sylvie'], ['IG2025005', 'AKPO', 'Jean'],
        ])->map(fn ($e) => Etudiant::ouCreer(...$e));

        $decisions = ['admis', 'admis', 'rattrapage', 'ajourne', 'admis'];

        foreach ($promos as $i => $promo) {
            $statut = $i === 0 ? StatutPv::Valide : StatutPv::Soumis;
            $pv = Pv::create([
                'reference' => 'PV-DEMO-IG-'.$promo->semestre->code,
                'promotion_id' => $promo->id,
                'session' => SessionPv::Normale,
                'date_deliberation' => now()->subDays(20 + $i)->toDateString(),
                'president_jury' => 'Pr. HOUESSOU',
                'membres_jury' => 'Dr. A, Dr. B, Dr. C',
                'fichier_path' => 'pv/demo.pdf',
                'fichier_nom_original' => 'demo.pdf',
                'fichier_mime' => 'application/pdf',
                'fichier_taille' => 1024,
                'statut' => $statut,
                'created_by' => $agent->id,
                'submitted_at' => now()->subDays(10),
                'validated_at' => $statut === StatutPv::Valide ? now()->subDays(5) : null,
            ]);

            foreach ($etudiants as $k => $e) {
                $pv->lignes()->create([
                    'etudiant_id' => $e->id,
                    'moyenne' => [14.5, 12.0, 9.5, 7.25, 11.0][$k],
                    'credits_valides' => [30, 30, 24, 18, 30][$k],
                    'decision' => $decisions[$k],
                ]);
            }
        }

        ActivityLogger::$actif = true;
    }
}
