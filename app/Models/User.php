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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'scolarite';
    }

    public function isChef(): bool
    {
        return $this->role === 'chef';
    }

    /** Clé de configuration d'interface : admin, agent ou chef. */
    public function roleKey(): string
    {
        return match ($this->role) {
            'scolarite' => 'agent',
            'chef' => 'chef',
            default => 'admin',
        };
    }

    /** Filières du département d'un chef (le jeu de données de test est dans ui-roles). */
    public function departementFilieres(): array
    {
        if (! $this->isChef()) {
            return [];
        }

        $code = $this->relationLoaded('filiere') ? $this->filiere?->code : $this->filiere()->value('code');

        foreach (config('ui-roles.departements', []) as $codes) {
            if (in_array($code, $codes, true)) {
                return $codes;
            }
        }

        return $code !== null && $code !== '' ? [$code] : [];
    }

    public function departementName(): string
    {
        if ($this->isAgent()) {
            return 'Service des examens';
        }

        if ($this->isChef()) {
            $code = $this->relationLoaded('filiere') ? $this->filiere?->code : $this->filiere()->value('code');

            foreach (config('ui-roles.departements', []) as $nom => $codes) {
                if (in_array($code, $codes, true)) {
                    return $nom;
                }
            }
        }

        return 'Administration générale';
    }

    /** Civilité stockée en tête des prénoms dans les comptes de test (Dr, Mme, M., Pr). */
    public function civilite(): string
    {
        $first = strtok(trim((string) $this->prenoms), " \t\n");

        return in_array($first, ['Dr', 'Pr', 'Mme', 'M.'], true) ? $first : '';
    }

    public function displayName(): string
    {
        return trim($this->civilite().' '.$this->nom);
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->prenoms.' '.(string) $this->nom)) ?: [];
        $words = array_values(array_filter($words, fn (string $word): bool => ! in_array($word, ['Dr', 'Pr', 'Mme', 'M.'], true)));
        $initials = '';

        foreach ([$words[0] ?? '', end($words) ?: ''] as $word) {
            if ($word !== '') {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }

        return $initials;
    }
}
