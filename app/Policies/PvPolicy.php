<?php

namespace App\Policies;

use App\Enums\StatutPv;
use App\Models\Pv;
use App\Models\User;

class PvPolicy
{
    private function dansSaFiliere(User $user, Pv $pv): bool
    {
        return $user->isChef()
            && $user->filiere_id !== null
            && (int) $pv->promotion->filiere_id === (int) $user->filiere_id;
    }

    public function viewAny(User $user): bool
    {
        return true; // le périmètre est filtré par Pv::visiblePour()
    }

    public function view(User $user, Pv $pv): bool
    {
        if ($user->isAdmin() || $user->isScolarite()) {
            return true;
        }

        return $this->dansSaFiliere($user, $pv) && $pv->statut !== StatutPv::Brouillon;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isScolarite();
    }

    public function update(User $user, Pv $pv): bool
    {
        if ($user->isAdmin()) {
            return true; // exceptionnel, toujours journalisé
        }

        return $user->isScolarite() && $pv->statut === StatutPv::Brouillon;
    }

    public function gererResultats(User $user, Pv $pv): bool
    {
        return $this->update($user, $pv);
    }

    public function soumettre(User $user, Pv $pv): bool
    {
        return ($user->isAdmin() || $user->isScolarite()) && $pv->statut === StatutPv::Brouillon;
    }

    public function valider(User $user, Pv $pv): bool
    {
        if ($pv->statut !== StatutPv::Soumis) {
            return false;
        }

        return $user->isAdmin() || $this->dansSaFiliere($user, $pv);
    }

    public function rejeter(User $user, Pv $pv): bool
    {
        return $this->valider($user, $pv);
    }

    public function archiver(User $user, Pv $pv): bool
    {
        return $user->isAdmin() && $pv->statut === StatutPv::Valide;
    }

    public function delete(User $user, Pv $pv): bool
    {
        return $user->isAdmin();
    }

    public function partager(User $user, Pv $pv): bool
    {
        if (! $pv->estOfficiel()) {
            return false;
        }

        return $user->isAdmin() || $this->dansSaFiliere($user, $pv);
    }
}
