<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class UserRequest extends FormRequest
{
    /** Utilisateur modifié (null à la création). */
    abstract protected function utilisateur(): ?User;

    public function authorize(): bool
    {
        return true; // protégé par le middleware role:admin
    }

    public function rules(): array
    {
        $user = $this->utilisateur();

        return [
            'name' => ['required', 'string', 'max:100'],
            'prenoms' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::enum(Role::class)],
            'filiere_id' => [
                Rule::requiredIf(fn () => $this->input('role') === Role::Chef->value),
                'nullable', 'exists:filieres,id',
            ],
        ];
    }

    public function messages(): array
    {
        return ['filiere_id.required' => 'Un chef de département doit être rattaché à une filière.'];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $user = $this->utilisateur();
            $role = $this->input('role');

            if ($role !== Role::Chef->value && filled($this->input('filiere_id'))) {
                $v->errors()->add('filiere_id', 'Seul un chef de département est rattaché à une filière.');
            }

            // Un seul chef actif par filière
            if ($role === Role::Chef->value) {
                $existe = User::where('role', Role::Chef->value)
                    ->where('filiere_id', $this->input('filiere_id'))
                    ->where('is_active', true)
                    ->when($user, fn ($q) => $q->where('id', '!=', $user->id))
                    ->exists();
                if ($existe && ($user === null || $user->is_active)) {
                    $v->errors()->add('filiere_id', 'Cette filière a déjà un chef de département actif.');
                }
            }

            // On ne retire pas le rôle admin au dernier admin actif
            if ($user && $user->isAdmin() && $role !== Role::Admin->value) {
                $autres = User::where('role', Role::Admin->value)->where('is_active', true)->where('id', '!=', $user->id)->exists();
                if (! $autres) {
                    $v->errors()->add('role', 'Impossible : c\'est le dernier administrateur actif.');
                }
            }
        });
    }
}
