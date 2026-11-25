<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etudiant extends Model
{
    protected $fillable = [
        'matricule',
        'nom',
        'prenoms',
        'date_naissance',
        'email',
        'telephone',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }

    public function lignesResultats(): HasMany
    {
        return $this->hasMany(LigneResultat::class);
    }
}