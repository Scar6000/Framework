<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\StatutPv;
use App\Models\Pv;
use App\Models\User;
use App\Notifications\PvNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

/**
 * Machine à états d'un PV :
 *   brouillon ──soumettre──► soumis ──valider──► valide ──archiver──► archive
 *                               └──rejeter (motif)──► brouillon
 * Les droits sont vérifiés AVANT (PvPolicy) ; ici on contrôle l'état et les règles métier.
 */
class PvWorkflow
{
    public function soumettre(Pv $pv, User $user): Pv
    {
        $this->exiger($pv->statut === StatutPv::Brouillon, 'Seul un PV en brouillon peut être soumis.');
        $this->exiger($pv->lignes()->exists(), 'Ajoutez ou importez les résultats avant de soumettre le PV.');

        $pv->loadMissing('promotion');
        $this->changer($pv, 'soumission', [
            'statut' => StatutPv::Soumis,
            'submitted_at' => now(),
            'motif_rejet' => null,
        ]);

        $chefs = User::where('role', Role::Chef->value)
            ->where('filiere_id', $pv->promotion->filiere_id)
            ->where('is_active', true)->get();
        Notification::send($chefs, new PvNotification($pv, 'soumis', "Le PV {$pv->reference} attend votre validation."));

        return $pv;
    }

    public function valider(Pv $pv, User $user): Pv
    {
        $this->exiger($pv->statut === StatutPv::Soumis, 'Seul un PV soumis peut être validé.');

        $this->changer($pv, 'validation', [
            'statut' => StatutPv::Valide,
            'validated_by' => $user->id,
            'validated_at' => now(),
            'motif_rejet' => null,
        ]);
        $pv->createur?->notify(new PvNotification($pv, 'valide', "Le PV {$pv->reference} a été validé."));

        return $pv;
    }

    public function rejeter(Pv $pv, User $user, string $motif): Pv
    {
        $this->exiger($pv->statut === StatutPv::Soumis, 'Seul un PV soumis peut être renvoyé en brouillon.');

        $this->changer($pv, 'rejet', [
            'statut' => StatutPv::Brouillon,
            'motif_rejet' => $motif,
            'submitted_at' => null,
        ]);
        $pv->createur?->notify(new PvNotification($pv, 'rejete', "Le PV {$pv->reference} a été renvoyé : {$motif}"));

        return $pv;
    }

    public function archiver(Pv $pv, User $user): Pv
    {
        $this->exiger($pv->statut === StatutPv::Valide, 'Seul un PV validé peut être archivé.');
        $this->changer($pv, 'archivage', ['statut' => StatutPv::Archive]);

        return $pv;
    }

    private function changer(Pv $pv, string $action, array $valeurs): void
    {
        $anciennes = ['statut' => $pv->statut->value];
        $pv->forceFill($valeurs)->saveQuietly();
        ActivityLogger::log($action, $pv, $anciennes, ['statut' => $pv->statut->value] + array_filter(
            ['motif_rejet' => $valeurs['motif_rejet'] ?? null]
        ));
    }

    private function exiger(bool $condition, string $message): void
    {
        if (! $condition) {
            throw ValidationException::withMessages(['statut' => $message]);
        }
    }
}
