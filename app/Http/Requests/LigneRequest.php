<?php

namespace App\Http\Requests;

use App\Enums\Decision;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LigneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // PvPolicy::gererResultats dans le contrôleur
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('moyenne') && ! $this->has('moyenne_generale')) {
            $this->merge(['moyenne_generale' => $this->input('moyenne')]);
        }
    }

    public function rules(): array
    {
        $creation = $this->isMethod('POST');

        return [
            'matricule' => [$creation ? 'required' : 'prohibited', 'string', 'max:30'],
            'nom' => [$creation ? 'required' : 'prohibited', 'string', 'max:150'],
            'prenoms' => [$creation ? 'required' : 'prohibited', 'string', 'max:150'],
            'moyenne_generale' => ['required', 'numeric', 'between:0,20'],
            'mention' => ['nullable', 'string', 'max:100'],
            'rang' => ['nullable', 'integer', 'min:1'],
            'observations' => ['nullable', 'string', 'max:1000'],
            'decision' => ['required', Rule::enum(Decision::class)],
        ];
    }
}
