<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'departement',
    ];

    public function pvDeliberations(): HasMany
    {
        return $this->hasMany(PvDeliberation::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}