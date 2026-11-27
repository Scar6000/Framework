<?php

namespace App\Support;

use App\Models\Pv;

/** Forme plate attendue par les partials Blade historiques (`shared.pv.*`). */
class PvPresentation
{
    /**
     * @return array<string, string|int|array<int, string>>
     */
    public static function toArray(Pv $pv): array
    {
        $promotion = $pv->relationLoaded('promotion') ? $pv->promotion : $pv->load('promotion')->promotion;
        $createur = $pv->relationLoaded('createur') ? $pv->createur : $pv->createur;

        return [
            'reference' => $pv->reference,
            'annee' => $promotion->annee->libelle,
            'filiere' => $promotion->filiere->code,
            'niveau' => $promotion->semestre->niveau->code,
            'session' => $pv->session === 'rattrapage' ? 'Rattrapage' : 'Normale',
            'date' => $pv->date_deliberation->toDateString(),
            'president' => $pv->president_jury,
            'membres' => $pv->membres_jury ?? [],
            'status' => $pv->statut === 'archive' ? 'Archivé' : 'Brouillon',
            'enregistre_par' => $createur !== null ? $createur->displayName() : '—',
            'date_enregistrement' => $pv->created_at->toDateString(),
            'pages' => 5,
        ];
    }
}
