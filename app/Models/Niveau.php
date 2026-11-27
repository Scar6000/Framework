<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    use Auditable;

    protected $table = 'niveaux';

    protected $fillable = ['code', 'libelle', 'ordre'];

    public function semestres(): HasMany
    {
        return $this->hasMany(Semestre::class);
    }
}
