<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'matricule',
        'role',
        'filiere_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function memoires(): HasMany
    {
        return $this->hasMany(Memoire::class, 'etudiant_id');
    }

    public function memoiresEncadres(): HasMany
    {
        return $this->hasMany(Memoire::class, 'encadreur_id');
    }

    public function validations(): HasMany
    {
        return $this->hasMany(ValidationMemoire::class, 'validateur_id');
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