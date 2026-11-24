<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeAcademique extends Model
{
    protected $fillable = [
        'libelle',
        'est_active',
    ];

    protected function casts(): array
    {
        return [
            'est_active' => 'boolean',
        ];
    }

    public function memoires(): HasMany
    {
        return $this->hasMany(Memoire::class);
    }
}