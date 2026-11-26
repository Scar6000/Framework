<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\RendersView;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use RendersView;

    public function show(Request $request)
    {
        return $this->render('profil.show', ['user' => $request->user()->load('filiere')]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenoms' => ['required', 'string', 'max:150'],
        ]);

        $request->user()->update($data);

        return $this->done('profil.show', [], 'Profil mis à jour.');
    }
}
