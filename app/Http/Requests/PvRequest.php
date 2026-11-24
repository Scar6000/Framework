<?php

namespace App\Http\Requests;

use App\Models\Pv;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class PvRequest extends FormRequest
{
    abstract protected function pv(): ?Pv;

    public function authorize(): bool
    {
        return true; // droits vérifiés par PvPolicy dans le contrôleur
    }

    protected function reglesCommunes(bool $fichierObligatoire): array
    {
        $pv = $this->pv();

        return [
            'reference' => ['nullable', 'string', 'max:100', Rule::unique('pvs', 'reference')->ignore($pv?->id)],
            'date_deliberation' => ['required', 'date', 'before_or_equal:today'],
            'president_jury' => ['required', 'string', 'max:150'],
            'membres_jury' => ['nullable', 'string', 'max:2000'],
            'fichier' => [
                $fichierObligatoire ? 'required' : 'nullable',
                'file', 'mimes:pdf,jpg,jpeg,png',
                'mimetypes:application/pdf,image/jpeg,image/png',
                'max:'.config('pv.upload_max_ko', 20480),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fichier.mimes' => 'Le scan doit être un PDF ou une image (jpg, png).',
            'fichier.max' => 'Le scan est trop volumineux.',
        ];
    }
}
