<?php

namespace App\Models;

use App\Enums\Decision;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneResultat extends Model
{
    use Auditable;

    protected $table = 'lignes_resultats';

    protected $fillable = [
        'pv_id', 'etudiant_id', 'moyenne_generale', 'decision',
        'mention', 'rang', 'observations',
    ];

    protected $casts = [
        'moyenne_generale' => 'decimal:2',
        'rang' => 'integer',
        'decision' => Decision::class,
    ];

    public function pv(): BelongsTo
    {
        return $this->belongsTo(Pv::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }
}
