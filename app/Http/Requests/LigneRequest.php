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

    public function rules(): array
    {
        $creation = $this->isMethod('POST');

        return [
            'matricule' => [$creation ? 'required' : 'prohibited', 'string', 'max:30'],
            'nom' => [$creation ? 'required' : 'prohibited', 'string', 'max:150'],
            'prenoms' => [$creation ? 'required' : 'prohibited', 'string', 'max:150'],
            'moyenne' => ['required', 'numeric', 'between:0,20'],
            'credits_valides' => ['required', 'integer', 'min:0', 'max:300'],
            'decision' => ['required', Rule::enum(Decision::class)],
        ];
    }
}
