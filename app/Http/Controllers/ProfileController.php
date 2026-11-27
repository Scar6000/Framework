<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'ancien_mot_de_passe' => ['required', 'current_password'],
            'nouveau_mot_de_passe' => ['required', 'confirmed', 'min:8'],
        ], [
            'ancien_mot_de_passe.required' => 'Indiquez votre mot de passe actuel.',
            'ancien_mot_de_passe.current_password' => 'Le mot de passe actuel est incorrect.',
            'nouveau_mot_de_passe.required' => 'Indiquez le nouveau mot de passe.',
            'nouveau_mot_de_passe.confirmed' => 'La confirmation ne correspond pas au nouveau mot de passe.',
            'nouveau_mot_de_passe.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['nouveau_mot_de_passe']),
            'must_change_password' => false,
        ])->save();

        return redirect()->route($user->roleKey().'.profil')->with('success', 'Mot de passe mis à jour.');
    }
}
