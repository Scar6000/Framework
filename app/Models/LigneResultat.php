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

    protected $fillable = ['pv_id', 'etudiant_id', 'moyenne', 'credits_valides', 'decision'];

    protected $casts = [
        'moyenne' => 'decimal:2',
        'credits_valides' => 'integer',
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
