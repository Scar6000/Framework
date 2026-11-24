<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Filiere extends Model
{
    use Auditable;

    protected $table = 'filieres';

    protected $fillable = ['code', 'nom', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function chef(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'chef')->where('is_active', true);
    }

    public function specialites(): HasMany
    {
        return $this->hasMany(Specialite::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }
}
