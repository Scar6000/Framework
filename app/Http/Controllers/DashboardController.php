<?php

namespace App\Http\Controllers;

use App\Enums\EtatPromotion;
use App\Enums\StatutPv;
use App\Http\Controllers\Concerns\RendersView;
use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Pv;
use App\Models\User;
use App\Services\PvManquantsService;
use App\Services\StatistiquesService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use RendersView;

    public function __invoke(Request $request, PvManquantsService $manquants, StatistiquesService $stats)
    {
        $user = $request->user();
        $annee = AnneeAcademique::enCours();
        $filiereId = $user->isChef() ? $user->filiere_id : null;

        $etats = $annee ? $manquants->etats($annee, $filiereId) : collect();
        $aSignaler = $etats->filter(fn ($e) => $e['etat'] === EtatPromotion::Manquant->value || $e['en_retard'])->values();

        $commun = [
            'annee_en_cours' => $annee,
            'resume_pv' => $annee ? $manquants->resume($annee, $filiereId) : null,
            'promotions_a_signaler' => $aSignaler->take(15),
        ];

        if ($user->isAdmin()) {
            return $this->render('dashboard.admin', $commun + [
                'taux_reussite' => $stats->tauxReussite(),
                'derniers_pv' => Pv::with('promotion.filiere', 'promotion.semestre')->latest()->limit(5)->get(),
                'alertes' => [
                    'brouillons_anciens' => Pv::where('statut', StatutPv::Brouillon->value)
                        ->where('created_at', '<', now()->subDays(config('pv.brouillon_alerte_jours', 14)))->count(),
                    'comptes_inactifs' => User::where('is_active', false)->count(),
                    'filieres_sans_chef' => Filiere::where('is_active', true)
                        ->whereDoesntHave('chef')->pluck('code'),
                ],
            ]);
        }

        if ($user->isScolarite()) {
            return $this->render('dashboard.scolarite', $commun + [
                'mes_brouillons' => Pv::where('created_by', $user->id)->where('statut', StatutPv::Brouillon->value)
                    ->whereNull('motif_rejet')->with('promotion.filiere', 'promotion.semestre')->latest()->get(),
                'pv_rejetes' => Pv::where('created_by', $user->id)->where('statut', StatutPv::Brouillon->value)
                    ->whereNotNull('motif_rejet')->with('promotion.filiere', 'promotion.semestre')->latest()->get(),
            ]);
        }

        // Chef de département : uniquement sa filière
        return $this->render('dashboard.chef', $commun + [
            'filiere' => $user->filiere,
            'pv_a_valider' => Pv::visiblePour($user)->where('statut', StatutPv::Soumis->value)
                ->with('promotion.semestre', 'promotion.specialite')->latest('submitted_at')->get(),
            'taux_reussite' => $stats->tauxReussite($filiereId),
        ]);
    }
}
