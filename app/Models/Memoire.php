<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Memoire extends Model
{
    protected $fillable = [
        'titre',
        'resume',
        'mots_cles',
        'fichier_pdf',
        'statut',
        'motif_rejet',
        'acces_restreint',
        'autoriser_telechargement',
        'embargo_jusqua',
        'etudiant_id',
        'encadreur_id',
        'filiere_id',
        'annee_academique_id',
    ];

    protected function casts(): array
    {
        return [
            'acces_restreint' => 'boolean',
            'autoriser_telechargement' => 'boolean',
            'embargo_jusqua' => 'date',
        ];
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function encadreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encadreur_id');
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    public function validations(): HasMany
    {
        return $this->hasMany(ValidationMemoire::class);
    }
}