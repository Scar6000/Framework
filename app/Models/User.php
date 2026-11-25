<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenoms',
        'email',
        'password',
        'role',
        'filiere_id',
        'is_active',
        'must_change_password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function pvsCreated(): HasMany
    {
        return $this->hasMany(Pv::class, 'created_by');
    }

    public function pvsValidated(): HasMany
    {
        return $this->hasMany(Pv::class, 'validated_by');
    }

    public function liensPartage(): HasMany
    {
        return $this->hasMany(LienPartage::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function pvDeliberationsCrees(): HasMany
    {
        return $this->hasMany(PvDeliberation::class, 'createur_id');
    }

    public function pvDeliberationsValides(): HasMany
    {
        return $this->hasMany(PvDeliberation::class, 'valide_par');
    }

    public function journalActions(): HasMany
    {
        return $this->hasMany(JournalAction::class);
    }
}