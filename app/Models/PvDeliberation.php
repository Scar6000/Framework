<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PvDeliberation extends Model
{
    protected $fillable = [
        'annee_academique_id',
        'filiere_id',
        'niveau_id',
        'semestre_id',
        'createur_id',
        'valide_par',
        'reference',
        'session',
        'date_deliberation',
        'president_jury',
        'membres_jury',
        'fichier_scan',
        'statut',
        'valide_le',
    ];

    protected function casts(): array
    {
        return [
            'date_deliberation' => 'date',
            'valide_le' => 'datetime',
        ];
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function lignesResultats(): HasMany
    {
        return $this->hasMany(LigneResultat::class);
    }

    public function journalActions(): HasMany
    {
        return $this->hasMany(JournalAction::class);
    }
}