<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Etudiant extends Model
{
    use Auditable;

    protected $table = 'etudiants';

    protected $fillable = ['matricule', 'nom', 'prenoms'];

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneResultat::class);
    }

    /** Retrouve par matricule ; crée sinon (le premier nom enregistré est conservé). */
    public static function ouCreer(string $matricule, string $nom, string $prenoms): self
    {
        return static::firstOrCreate(
            ['matricule' => Str::upper(trim($matricule))],
            ['nom' => trim($nom), 'prenoms' => trim($prenoms)]
        );
    }

    /** Un chef ne voit que les étudiants présents dans un PV visible de sa filière. */
    public function scopeVisiblePour(Builder $q, User $user): Builder
    {
        if ($user->isChef()) {
            $q->whereHas('lignes', fn ($l) => $l->whereHas('pv', fn ($p) => $p->visiblePour($user)));
        }

        return $q;
    }
}
