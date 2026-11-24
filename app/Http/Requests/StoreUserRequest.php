<?php

namespace App\Http\Requests;

use App\Models\User;

class StoreUserRequest extends UserRequest
{
    protected function utilisateur(): ?User
    {
        return null;
    }
}
