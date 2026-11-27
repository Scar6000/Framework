<?php

namespace App\Services;

use App\Enums\Decision;
use App\Enums\SessionPv;
use App\Enums\StatutPv;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Statistiques calculées sur les PV officiels (validés ou archivés) de session normale.
 * Taux de réussite = admis ÷ nombre de lignes de résultats.
 */
class StatistiquesService
{
    private function base(?int $filiereId, ?int $anneeId): Builder
    {
        return DB::table('lignes_resultats as l')
            ->join('pvs as p', 'p.id', '=', 'l.pv_id')
            ->join('promotions as pr', 'pr.id', '=', 'p.promotion_id')
            ->join('filieres as f', 'f.id', '=', 'pr.filiere_id')
            ->join('annees_academiques as a', 'a.id', '=', 'pr.annee_academique_id')
            ->whereNull('p.deleted_at')
            ->whereIn('p.statut', StatutPv::officiels())
            ->where('p.session', SessionPv::Normale->value)
            ->when($filiereId, fn ($q) => $q->where('pr.filiere_id', $filiereId))
            ->when($anneeId, fn ($q) => $q->where('pr.annee_academique_id', $anneeId));
    }

    /** Taux de réussite par filière et par année. */
    public function tauxReussite(?int $filiereId = null, ?int $anneeId = null): Collection
    {
        $rows = $this->base($filiereId, $anneeId)
            ->selectRaw(
                "f.id as filiere_id, f.code as filiere, a.id as annee_id, a.libelle as annee, count(*) as total,
                 sum(case when l.decision = 'admis' then 1 else 0 end) as admis,
                 sum(case when l.decision = 'ajourne' then 1 else 0 end) as ajournes,
                 sum(case when l.decision = 'rattrapage' then 1 else 0 end) as rattrapages,
                 sum(case when l.decision = 'exclu' then 1 else 0 end) as exclus"
            )
            ->groupBy('f.id', 'f.code', 'a.id', 'a.libelle')
            ->orderBy('a.libelle')->orderBy('f.code')
            ->get();

        return $rows->map(function ($r) {
            $total = (int) $r->total;

            return [
                'filiere_id' => (int) $r->filiere_id,
                'filiere' => $r->filiere,
                'annee_id' => (int) $r->annee_id,
                'annee' => $r->annee,
                'total' => $total,
                'admis' => (int) $r->admis,
                'ajournes' => (int) $r->ajournes,
                'rattrapages' => (int) $r->rattrapages,
                'exclus' => (int) $r->exclus,
                'taux_reussite' => $total > 0 ? round($r->admis / $total * 100, 1) : 0.0,
            ];
        });
    }

    /** Répartition globale des décisions (pour un graphique en secteurs). */
    public function repartitionDecisions(?int $filiereId = null, ?int $anneeId = null): array
    {
        $comptes = $this->base($filiereId, $anneeId)
            ->selectRaw('l.decision as decision, count(*) as n')
            ->groupBy('l.decision')
            ->pluck('n', 'decision');

        $res = [];
        foreach (Decision::cases() as $d) {
            $res[$d->value] = (int) ($comptes[$d->value] ?? 0);
        }

        return $res;
    }
}
