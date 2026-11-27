<?php

namespace App\Http\Requests;

use App\Models\Pv;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdatePvRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Pv $pv */
        $pv = $this->route('pv');

        return Gate::allows('update', $pv);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Pv $pv */
        $pv = $this->route('pv');
        $isArchived = $pv->statut === 'archive';

        return [
            'action' => ['required', Rule::in(['draft', 'archive'])],
            'fichier' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],
            'date_deliberation' => ['required', 'date'],
            'president_jury' => ['required', 'string', 'max:255'],
            'membres_jury' => [
                $isArchived || $this->input('action') === 'archive' ? 'required' : 'nullable',
                'array',
                'max:20',
            ],
            'membres_jury.*' => ['string', 'max:255'],
            'motif' => [$isArchived ? 'required' : 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'motif.required' => 'Le motif de la modification est obligatoire pour un PV archivé.',
            'fichier.mimes' => 'Le scan doit être un fichier PDF, JPG ou PNG.',
            'fichier.max' => 'Le scan ne doit pas dépasser 20 Mo.',
            'membres_jury.required' => 'Ajoutez au moins un membre du jury pour archiver le PV.',
        ];
    }
}
