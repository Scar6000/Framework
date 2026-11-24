<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Promotion;
use App\Models\Semestre;
use App\Models\Specialite;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Une seule page à 5 onglets : filieres · specialites · annees · niveaux · semestres
 * Routes : /admin/referentiels/{type}[/{id}]
 */
class ReferentielController extends Controller
{
    use RendersView;

    private const TYPES = [
        'filieres' => Filiere::class,
        'specialites' => Specialite::class,
        'annees' => AnneeAcademique::class,
        'niveaux' => Niveau::class,
        'semestres' => Semestre::class,
    ];

    public function index()
    {
        return $this->render('admin.referentiels.index', [
            'filieres' => Filiere::with('specialites')->orderBy('code')->get(),
            'specialites' => Specialite::with('filiere')->orderBy('filiere_id')->orderBy('code')->get(),
            'annees' => AnneeAcademique::orderByDesc('libelle')->get(),
            'niveaux' => Niveau::orderBy('ordre')->get(),
            'semestres' => Semestre::with('niveau')->orderBy('code')->get(),
        ]);
    }

    public function store(Request $request, string $type)
    {
        $modele = $this->modele($type);
        $data = $request->validate($this->regles($type, null, $request));

        if ($type === 'annees') {
            $data['est_en_cours'] = ! AnneeAcademique::where('est_en_cours', true)->exists();
        }

        $item = $modele::create($data);

        return $this->done('admin.referentiels.index', [], 'Élément créé.', ['item' => $item], 201);
    }

    public function update(Request $request, string $type, int $id)
    {
        $item = $this->modele($type)::findOrFail($id);
        $data = $request->validate($this->regles($type, $item, $request));

        $item->update($data);

        return $this->done('admin.referentiels.index', [], 'Élément mis à jour.', ['item' => $item->fresh()]);
    }

    public function destroy(string $type, int $id)
    {
        $item = $this->modele($type)::findOrFail($id);

        if ($raison = $this->estUtilise($type, $item)) {
            throw ValidationException::withMessages([
                'id' => "Suppression impossible : {$raison}. Désactivez-le plutôt (is_active = false).",
            ]);
        }

        $item->delete();

        return $this->done('admin.referentiels.index', [], 'Élément supprimé.');
    }

    /** @return class-string<Model> */
    private function modele(string $type): string
    {
        return self::TYPES[$type] ?? abort(404);
    }

    private function regles(string $type, ?Model $item, Request $request): array
    {
        $id = $item?->getKey();

        return match ($type) {
            'filieres' => [
                'code' => ['required', 'string', 'max:10', Rule::unique('filieres', 'code')->ignore($id)],
                'nom' => ['required', 'string', 'max:150'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'specialites' => [
                'filiere_id' => ['required', 'exists:filieres,id'],
                'code' => ['required', 'string', 'max:10',
                    Rule::unique('specialites', 'code')->where('filiere_id', $request->input('filiere_id', $item?->filiere_id))->ignore($id)],
                'nom' => ['required', 'string', 'max:150'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'annees' => [
                'libelle' => ['required', 'regex:/^\d{4}-\d{4}$/', Rule::unique('annees_academiques', 'libelle')->ignore($id)],
                'date_debut' => ['required', 'date'],
                'date_fin' => ['required', 'date', 'after:date_debut'],
            ],
            'niveaux' => [
                'code' => ['required', 'string', 'max:5', Rule::unique('niveaux', 'code')->ignore($id)],
                'libelle' => ['required', 'string', 'max:100'],
                'ordre' => ['required', 'integer', 'min:1', 'max:10'],
            ],
            'semestres' => [
                'code' => ['required', 'string', 'max:5', Rule::unique('semestres', 'code')->ignore($id)],
                'libelle' => ['required', 'string', 'max:100'],
                'niveau_id' => ['required', 'exists:niveaux,id'],
            ],
        };
    }

    /** Retourne la raison pour laquelle l'élément ne peut pas être supprimé, ou null. */
    private function estUtilise(string $type, Model $item): ?string
    {
        return match ($type) {
            'filieres' => User::where('filiere_id', $item->id)->exists() ? 'des utilisateurs y sont rattachés'
                : (Specialite::where('filiere_id', $item->id)->exists() ? 'elle a des spécialités'
                : (Promotion::where('filiere_id', $item->id)->exists() ? 'elle est utilisée par des promotions' : null)),
            'specialites' => Promotion::where('specialite_id', $item->id)->exists() ? 'elle est utilisée par des promotions' : null,
            'annees' => Promotion::where('annee_academique_id', $item->id)->exists() ? 'elle est utilisée par des promotions' : null,
            'niveaux' => Semestre::where('niveau_id', $item->id)->exists() ? 'il a des semestres' : null,
            'semestres' => Promotion::where('semestre_id', $item->id)->exists() ? 'il est utilisé par des promotions' : null,
            default => null,
        };
    }
}
