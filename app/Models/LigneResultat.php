<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneResultat extends Model
{
    protected $fillable = [
        'pv_deliberation_id',
        'matricule',
        'nom_prenoms',
        'moyenne',
        'credits_valides',
        'decision',
    ];

    protected function casts(): array
    {
        return [
            'moyenne' => 'decimal:2',
            'credits_valides' => 'integer',
        ];
    }

    public function pvDeliberation(): BelongsTo
    {
        return $this->belongsTo(PvDeliberation::class);
    }
}