<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    protected $fillable = [
        'code',
        'libelle',
    ];

    public function memoires(): HasMany
    {
        return $this->hasMany(Memoire::class);
    }
}