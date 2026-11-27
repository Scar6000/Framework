<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\RendersView;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    use RendersView;

    public function edit(Request $request)
    {
        return $this->render('auth.change-password', [
            'obligatoire' => $request->user()->must_change_password,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ]);

        $user = $request->user();
        $user->forceFill(['password' => $request->input('password'), 'must_change_password' => false])->save();
        ActivityLogger::log('changement_mot_de_passe', $user);

        return $this->done('dashboard', [], 'Mot de passe modifié.');
    }
}
