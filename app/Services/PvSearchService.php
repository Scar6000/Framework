<?php

namespace App\Services;

use App\Models\Pv;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/** Recherche multicritère de PV (liste des PV, page de recherche, exports). */
class PvSearchService
{
    public const FILTRES = [
        'annee_id', 'filiere_id', 'niveau_id', 'semestre_id', 'specialite_id',
        'session', 'statut', 'decision', 'etudiant', 'q', 'mes_pv',
    ];

    public function query(User $user, array $f): Builder
    {
        $q = Pv::query()
            ->visiblePour($user)
            ->with(['promotion.filiere', 'promotion.annee', 'promotion.semestre.niveau', 'promotion.specialite', 'createur:id,name,prenoms']);

        // Critères portant sur la promotion
        $promo = array_filter([
            'annee_academique_id' => $f['annee_id'] ?? null,
            'filiere_id' => $f['filiere_id'] ?? null,
            'semestre_id' => $f['semestre_id'] ?? null,
            'specialite_id' => $f['specialite_id'] ?? null,
        ], fn ($v) => filled($v));

        if ($promo) {
            $q->whereHas('promotion', function ($p) use ($promo) {
                foreach ($promo as $col => $val) {
                    $p->where($col, $val);
                }
            });
        }

        if (filled($f['niveau_id'] ?? null)) {
            $q->whereHas('promotion.semestre', fn ($s) => $s->where('niveau_id', $f['niveau_id']));
        }

        if (filled($f['session'] ?? null)) {
            $q->where('session', $f['session']);
        }
        if (filled($f['statut'] ?? null)) {
            $q->where('statut', $f['statut']);
        }
        if (filled($f['q'] ?? null)) {
            $q->where('reference', 'like', '%'.$f['q'].'%');
        }
        if (! empty($f['mes_pv'])) {
            $q->where('created_by', $user->id);
        }

        // Critères portant sur les lignes de résultats (même étudiant, même ligne)
        if (filled($f['etudiant'] ?? null) || filled($f['decision'] ?? null)) {
            $q->whereHas('lignes', function ($l) use ($f) {
                if (filled($f['decision'] ?? null)) {
                    $l->where('decision', $f['decision']);
                }
                if (filled($f['etudiant'] ?? null)) {
                    $t = '%'.$f['etudiant'].'%';
                    $l->whereHas('etudiant', fn ($e) => $e
                        ->where('matricule', 'like', $t)
                        ->orWhere('nom', 'like', $t)
                        ->orWhere('prenoms', 'like', $t));
                }
            });
        }

        return $q->orderByDesc('date_deliberation')->orderByDesc('id');
    }
}
