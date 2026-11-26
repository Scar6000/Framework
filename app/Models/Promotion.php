<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use Auditable;

    protected $table = 'promotions';

    protected $fillable = [
        'annee_academique_id', 'filiere_id', 'semestre_id',
        'est_attendue', 'date_limite',
    ];

    protected $casts = [
        'est_attendue' => 'boolean',
        'date_limite' => 'date',
    ];

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

    /** Un chef ne voit que les promotions de sa filière. */
    public function scopeVisiblePour(Builder $q, User $user): Builder
    {
        if ($user->isChef()) {
            $q->where('promotions.filiere_id', $user->filiere_id);
        }

        return $q;
    }

    /** Ex. « IG · L3 · S5 · AIP · 2025-2026 » (relations chargées). */
    public function getLibelleAttribute(): string
    {
        return collect([
            $this->filiere?->code,
            $this->semestre?->niveau?->code,
            $this->semestre?->code,
            $this->annee?->libelle,
        ])->filter()->implode(' · ');
    }
}
