<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    protected $fillable = [
        'code',
        'libelle',
        'ordre',
    ];

    public function semestres(): HasMany
    {
        return $this->hasMany(Semestre::class);
    }
}