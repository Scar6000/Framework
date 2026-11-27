<?php

namespace App\Http\Requests;

use App\Models\Pv;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StorePvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Pv::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['draft', 'archive'])],
            'fichier' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],
            'filiere_id' => ['required', 'integer', 'exists:filieres,id'],
            'promotion_id' => [
                'required',
                'integer',
                Rule::exists('promotions', 'id')->where('filiere_id', $this->input('filiere_id')),
            ],
            'session' => ['required', Rule::in(['Normale', 'Rattrapage'])],
            'date_deliberation' => ['required', 'date'],
            'president_jury' => ['required', 'string', 'max:255'],
            'membres_jury' => [Rule::requiredIf($this->input('action') === 'archive'), 'nullable', 'array', 'max:20'],
            'membres_jury.*' => ['string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fichier.required' => 'Le scan du PV est obligatoire.',
            'fichier.mimes' => 'Le scan doit être un fichier PDF, JPG ou PNG.',
            'fichier.max' => 'Le scan ne doit pas dépasser 20 Mo.',
            'filiere_id.required' => 'Sélectionnez une filière.',
            'promotion_id.required' => 'Sélectionnez une promotion.',
            'session.required' => 'Sélectionnez une session.',
            'date_deliberation.required' => 'Indiquez la date de délibération.',
            'president_jury.required' => 'Indiquez le président du jury.',
            'membres_jury.required' => 'Ajoutez au moins un membre du jury pour archiver le PV.',
        ];
    }
}
