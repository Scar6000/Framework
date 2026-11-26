<?php

namespace App\Http\Requests;

use App\Enums\SessionPv;
use App\Models\Pv;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePvRequest extends PvRequest
{
    protected function pv(): ?Pv
    {
        return null;
    }

    public function rules(): array
    {
        return [
            'promotion_id' => ['required', 'exists:promotions,id'],
            'session' => ['required', Rule::enum(SessionPv::class)],
        ] + $this->reglesCommunes(true);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            // Un seul PV par promotion et par session
            $existe = Pv::where('promotion_id', $this->input('promotion_id'))
                ->where('session', $this->input('session'))->exists();
            if ($existe) {
                $v->errors()->add('promotion_id', 'Un PV existe déjà pour cette promotion et cette session.');
            }
        });
    }
}
