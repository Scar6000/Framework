<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use App\Models\AnneeAcademique;
use App\Models\Promotion;
use App\Support\Referentiels;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    use RendersView;

    public function index(Request $request)
    {
        $anneeId = $request->input('annee_id', AnneeAcademique::enCours()?->id);

        $promotions = Promotion::with(['annee', 'filiere', 'semestre.niveau'])
            ->when($anneeId, fn ($q) => $q->where('annee_academique_id', $anneeId))
            ->when($request->filled('filiere_id'), fn ($q) => $q->where('filiere_id', $request->input('filiere_id')))
            ->orderBy('filiere_id')->orderBy('semestre_id')
            ->paginate(60)->withQueryString();

        return $this->render('admin.promotions.index', [
            'promotions' => $promotions,
            'annee_id' => (int) $anneeId,
            'references' => Referentiels::pourFiltres($request->user()),
        ]);
    }

    /** L'admin désactive une promotion inexistante ou fixe une date limite. */
    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'est_attendue' => ['sometimes', 'boolean'],
            'date_limite' => ['nullable', 'date'],
        ]);
        $promotion->update($data);

        return $this->done('admin.promotions.index', ['annee_id' => $promotion->annee_academique_id], 'Promotion mise à jour.', ['promotion' => $promotion->fresh()]);
    }
}
