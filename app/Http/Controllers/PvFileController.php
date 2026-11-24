<?php

namespace App\Http\Controllers;

use App\Models\Pv;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * Les scans ne sont JAMAIS accessibles par un lien public :
 * ils passent par ce contrôleur qui vérifie les droits.
 */
class PvFileController extends Controller
{
    /** Aperçu en ligne (affichage dans le navigateur / PDF.js). */
    public function show(Pv $pv)
    {
        Gate::authorize('view', $pv);
        ActivityLogger::log('consultation_scan', $pv);

        return $this->disque()->response($pv->getRawOriginal('fichier_path'), $pv->fichier_nom_original, [
            'Content-Type' => $pv->fichier_mime,
        ], 'inline');
    }

    public function telecharger(Pv $pv)
    {
        Gate::authorize('view', $pv);
        abort_unless(config('pv.telechargement_autorise'), 403, 'Le téléchargement est désactivé.');
        ActivityLogger::log('telechargement_scan', $pv);

        return $this->disque()->download($pv->getRawOriginal('fichier_path'), $pv->fichier_nom_original);
    }

    private function disque()
    {
        return Storage::disk(config('pv.disk'));
    }
}
