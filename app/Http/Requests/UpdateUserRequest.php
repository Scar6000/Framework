<?php

namespace App\Http\Requests;

use App\Models\User;

class UpdateUserRequest extends UserRequest
{
    protected function utilisateur(): ?User
    {
        return $this->route('user');
    }
}
