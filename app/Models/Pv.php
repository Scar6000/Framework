<?php

namespace App\Models;

use App\Enums\SessionPv;
use App\Enums\StatutPv;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pv extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'pvs';

    protected $fillable = [
        'reference', 'promotion_id', 'session', 'date_deliberation', 'president_jury', 'membres_jury',
        'fichier_path', 'fichier_nom_original', 'fichier_mime', 'fichier_taille',
        'statut', 'motif_rejet', 'created_by', 'submitted_at', 'validated_by', 'validated_at',
    ];

    protected $hidden = ['fichier_path'];

    protected $casts = [
        'session' => SessionPv::class,
        'statut' => StatutPv::class,
        'date_deliberation' => 'date',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneResultat::class);
    }

    /**
     * Règle centrale : un chef ne voit que les PV de SA filière,
     * et jamais les brouillons (pas encore soumis).
     */
    public function scopeVisiblePour(Builder $q, User $user): Builder
    {
        if ($user->isChef()) {
            $q->where($this->getTable().'.statut', '!=', StatutPv::Brouillon->value)
                ->whereHas('promotion', fn ($p) => $p->where('filiere_id', $user->filiere_id));
        }

        return $q;
    }

    public function estOfficiel(): bool
    {
        return in_array($this->statut, [StatutPv::Valide, StatutPv::Archive], true);
    }
}
