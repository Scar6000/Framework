<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Auditable, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'prenoms', 'email', 'password', 'role', 'filiere_id',
        'is_active', 'must_change_password', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function pvsCrees(): HasMany
    {
        return $this->hasMany(Pv::class, 'created_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isScolarite(): bool
    {
        return $this->role === Role::Scolarite;
    }

    public function isChef(): bool
    {
        return $this->role === Role::Chef;
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->name.' '.$this->prenoms);
    }

    protected $appends = ['nom_complet'];
}
