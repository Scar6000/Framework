<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    protected $fillable = [
        'annee_academique_id',
        'filiere_id',
        'semestre_id',
        'est_attendue',
        'date_limite',
    ];

    protected function casts(): array
    {
        return [
            'est_attendue' => 'boolean',
            'date_limite' => 'date',
        ];
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    /** Alias utilisé par les contrôleurs et les vues PV. */
    public function annee(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    public function pvs(): HasMany
    {
        return $this->hasMany(Pv::class);
    }
}
