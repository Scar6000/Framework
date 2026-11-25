<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pv extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'promotion_id',
        'session',
        'date_deliberation',
        'president_jury',
        'membres_jury',
        'fichier_path',
        'fichier_nom_original',
        'fichier_mime',
        'fichier_taille',
        'statut',
        'motif_rejet',
        'created_by',
        'submitted_at',
        'validated_by',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'date_deliberation' => 'date',
            'membres_jury' => 'array',
            'fichier_taille' => 'integer',
            'submitted_at' => 'datetime',
            'validated_at' => 'datetime',
        ];
    }

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

    public function lignesResultats(): HasMany
    {
        return $this->hasMany(LigneResultat::class);
    }

    public function liensPartage(): HasMany
    {
        return $this->hasMany(LienPartage::class);
    }
}