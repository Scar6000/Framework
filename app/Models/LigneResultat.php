<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneResultat extends Model
{
    protected $table = 'lignes_resultats';

    protected $fillable = [
        'pv_id',
        'etudiant_id',
        'moyenne_generale',
        'decision',
        'mention',
        'rang',
        'observations',
    ];

    protected function casts(): array
    {
        return [
            'moyenne_generale' => 'decimal:2',
            'rang' => 'integer',
        ];
    }

    public function pv(): BelongsTo
    {
        return $this->belongsTo(Pv::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }
}