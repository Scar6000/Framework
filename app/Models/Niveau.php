<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    protected $fillable = [
        'code',
        'libelle',
    ];

    public function semestres(): HasMany
    {
        return $this->hasMany(Semestre::class);
    }

    public function pvDeliberations(): HasMany
    {
        return $this->hasMany(PvDeliberation::class);
    }
}