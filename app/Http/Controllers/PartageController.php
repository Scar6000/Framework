<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use App\Models\LigneResultat;
use App\Models\Pv;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/** Lien signé et temporaire pour partager le scan d'un PV validé (ou l'extrait d'un étudiant). */
class PartageController extends Controller
{
    use RendersView;

    public function creer(Request $request, Pv $pv)
    {
        Gate::authorize('partager', $pv);

        $data = $request->validate([
            'duree_minutes' => ['nullable', 'integer', 'min:5', 'max:'.config('pv.partage_duree_max', 1440)],
            'etudiant_id' => ['nullable', 'integer', 'exists:etudiants,id'],
        ]);

        $minutes = $data['duree_minutes'] ?? config('pv.partage_duree_defaut', 60);
        $params = array_filter(['pv' => $pv->id, 'etudiant' => $data['etudiant_id'] ?? null]);

        $url = URL::temporarySignedRoute('partage.show', now()->addMinutes($minutes), $params);
        ActivityLogger::log('creation_lien_partage', $pv, null, ['expire_dans_minutes' => $minutes, 'etudiant_id' => $data['etudiant_id'] ?? null]);

        return $this->done('pv.show', ['pv' => $pv], 'Lien de partage créé.', ['url' => $url, 'expire_dans_minutes' => $minutes], 201, ['lien_partage' => $url]);
    }

    /** Route publique protégée par la signature (middleware « signed »). */
    public function show(Request $request, Pv $pv)
    {
        abort_unless($pv->estOfficiel(), 404);
        ActivityLogger::log('consultation_lien_partage', $pv, null, ['etudiant_id' => $request->query('etudiant')], null);

        if ($request->filled('etudiant')) {
            $ligne = LigneResultat::with('etudiant')->where('pv_id', $pv->id)->where('etudiant_id', $request->query('etudiant'))->firstOrFail();
            $pv->load('promotion.filiere', 'promotion.annee', 'promotion.semestre.niveau');

            return $this->render('partage.extrait', ['pv' => $pv, 'ligne' => $ligne]);
        }

        return Storage::disk(config('pv.disk'))->response($pv->getRawOriginal('fichier_path'), $pv->fichier_nom_original, ['Content-Type' => $pv->fichier_mime], 'inline');
    }
}
