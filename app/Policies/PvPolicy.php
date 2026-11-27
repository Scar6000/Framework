<?php

namespace App\Policies;

use App\Models\Pv;
use App\Models\User;

/** Matrice de droits : administrateur / agent de scolarité / chef de département. */
class PvPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // le périmètre est appliqué par Pv::visiblePour()
    }

    public function view(User $user, Pv $pv): bool
    {
        if ($user->isAdmin() || $user->isAgent()) {
            return true;
        }

        return $this->dansLeDepartement($user, $pv);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAgent();
    }

    public function update(User $user, Pv $pv): bool
    {
        if ($user->isAdmin()) {
            return true; // PV archivé : motif obligatoire, modification journalisée.
        }

        return $user->isAgent() && $pv->statut === 'brouillon';
    }

    public function archive(User $user, Pv $pv): bool
    {
        return ($user->isAdmin() || $user->isAgent()) && $pv->statut === 'brouillon';
    }

    public function download(User $user): bool
    {
        return $user->isAdmin() || $user->isAgent();
    }

    public function viewResults(User $user): bool
    {
        return true; // lecture seule pour le chef
    }

    public function manageResults(User $user, Pv $pv): bool
    {
        return $this->update($user, $pv);
    }

    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->isAgent();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    private function dansLeDepartement(User $user, Pv $pv): bool
    {
        $codes = $user->departementFilieres();

        if (! $user->isChef() || $codes === []) {
            return false;
        }

        $code = $pv->promotion()->with('filiere')->first()?->filiere?->code;

        return $code !== null && in_array($code, $codes, true);
    }
}
