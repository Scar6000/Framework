<?php

namespace App\Http\Controllers;

use App\Enums\Decision;
use App\Enums\SessionPv;
use App\Enums\StatutPv;
use App\Http\Controllers\Concerns\RendersView;
use App\Models\Etudiant;
use App\Services\PvSearchService;
use App\Support\Referentiels;
use Illuminate\Http\Request;

/** Recherche multicritère : PV + étudiants. Le périmètre du chef est appliqué automatiquement. */
class RechercheController extends Controller
{
    use RendersView;

    public function index(Request $request, PvSearchService $recherche)
    {
        $user = $request->user();
        $filtres = $request->only(PvSearchService::FILTRES);

        $pvs = $recherche->query($user, $filtres)->paginate(15, ['*'], 'page')->withQueryString();

        $etudiants = null;
        if (filled($filtres['etudiant'] ?? null)) {
            $t = '%'.$filtres['etudiant'].'%';
            $etudiants = Etudiant::visiblePour($user)
                ->where(fn ($q) => $q->where('matricule', 'like', $t)->orWhere('nom', 'like', $t)->orWhere('prenoms', 'like', $t))
                ->orderBy('nom')->paginate(15, ['*'], 'page_etudiants')->withQueryString();
        }

        return $this->render('recherche.index', [
            'pvs' => $pvs,
            'etudiants' => $etudiants,
            'filtres' => $filtres,
            'references' => Referentiels::pourFiltres($user),
            'decisions' => Decision::cases(),
            'sessions' => SessionPv::cases(),
            'statuts' => StatutPv::cases(),
        ]);
    }
}
