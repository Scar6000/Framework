<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidationMemoire extends Model
{
    protected $fillable = [
        'memoire_id',
        'validateur_id',
        'ancien_statut',
        'nouveau_statut',
        'commentaire',
    ];

    public function memoire(): BelongsTo
    {
        return $this->belongsTo(Memoire::class);
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }
}