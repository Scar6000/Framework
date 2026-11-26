<?php

namespace App\Http\Requests;

use App\Models\Pv;

class UpdatePvRequest extends PvRequest
{
    protected function pv(): ?Pv
    {
        return $this->route('pv');
    }

    public function rules(): array
    {
        return $this->reglesCommunes(false);
    }
}
