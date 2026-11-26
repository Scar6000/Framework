<?php

namespace App\Services;

use App\Enums\Decision;
use App\Enums\EtatPromotion;
use App\Enums\SessionPv;
use App\Enums\StatutPv;
use App\Models\AnneeAcademique;
use App\Models\Promotion;
use App\Models\Pv;
use Illuminate\Support\Collection;

/**
 * Détection des PV manquants.
 *  - Chaque promotion « attendue » doit avoir un PV de session normale.
 *  - Un PV de rattrapage n'est attendu que si le PV normal contient au moins
 *    un étudiant à la décision « rattrapage ».
 */
class PvManquantsService
{
    /** @return Collection<int, array> */
    public function etats(AnneeAcademique $annee, ?int $filiereId = null): Collection
    {
        $promotions = Promotion::query()
            ->with([
                'filiere', 'semestre.niveau',
                'pvs' => fn ($q) => $q->withCount([
                    'lignes as nb_rattrapage' => fn ($l) => $l->where('decision', Decision::Rattrapage->value),
                ]),
            ])
            ->where('annee_academique_id', $annee->id)
            ->when($filiereId, fn ($q) => $q->where('filiere_id', $filiereId))
            ->get()
            ->sortBy(fn ($p) => $p->filiere->code.'|'.$p->semestre->code)
            ->values();

        return $promotions->map(function (Promotion $p) {
            $normale = $p->pvs->first(fn (Pv $pv) => $pv->session === SessionPv::Normale);
            $rattrapage = $p->pvs->first(fn (Pv $pv) => $pv->session === SessionPv::Rattrapage);

            $etat = ! $p->est_attendue ? EtatPromotion::NonApplicable : $this->etatPour($normale);
            $retard = $p->est_attendue
                && $p->date_limite !== null
                && $p->date_limite->lt(today())
                && $etat !== EtatPromotion::Complet;

            $rattrapageAttendu = $p->est_attendue && $normale !== null && $normale->nb_rattrapage > 0;

            return [
                'promotion' => $p,
                'etat' => $etat->value,
                'etat_label' => $etat->label(),
                'en_retard' => $retard,
                'pv_normale' => $normale,
                'rattrapage_attendu' => $rattrapageAttendu,
                'etat_rattrapage' => $rattrapageAttendu ? $this->etatPour($rattrapage)->value : null,
                'pv_rattrapage' => $rattrapage,
            ];
        });
    }

    public function resume(AnneeAcademique $annee, ?int $filiereId = null): array
    {
        $etats = $this->etats($annee, $filiereId)->where('etat', '!=', EtatPromotion::NonApplicable->value);

        $attendus = $etats->count();
        $complets = $etats->where('etat', EtatPromotion::Complet->value)->count();

        return [
            'annee' => $annee->libelle,
            'attendus' => $attendus,
            'complets' => $complets,
            'en_cours' => $etats->where('etat', EtatPromotion::EnCours->value)->count(),
            'manquants' => $etats->where('etat', EtatPromotion::Manquant->value)->count(),
            'en_retard' => $etats->where('en_retard', true)->count(),
            'rattrapages_manquants' => $etats->filter(
                fn ($e) => $e['rattrapage_attendu'] && $e['etat_rattrapage'] !== EtatPromotion::Complet->value
            )->count(),
            'taux_numerisation' => $attendus > 0 ? round($complets / $attendus * 100, 1) : 0.0,
        ];
    }

    private function etatPour(?Pv $pv): EtatPromotion
    {
        if ($pv === null) {
            return EtatPromotion::Manquant;
        }

        return in_array($pv->statut, [StatutPv::Valide, StatutPv::Archive], true)
            ? EtatPromotion::Complet
            : EtatPromotion::EnCours;
    }
}
