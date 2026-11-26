<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeAcademique extends Model
{
    use Auditable;

    protected $table = 'annees_academiques';

    protected $fillable = ['libelle', 'date_debut', 'date_fin', 'est_en_cours'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'est_en_cours' => 'boolean',
    ];

    public static function enCours(): ?self
    {
        return static::where('est_en_cours', true)->first();
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class, 'annee_academique_id');
    }
}
