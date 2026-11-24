<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\RendersView;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use RendersView;

    public function create()
    {
        return $this->render('auth.login');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $cle = Str::lower($data['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($cle, 5)) {
            $secondes = RateLimiter::availableIn($cle);
            throw ValidationException::withMessages([
                'email' => "Trop de tentatives. Réessayez dans {$secondes} secondes.",
            ]);
        }

        // is_active => true : un compte désactivé reçoit le même message qu'un mauvais mot de passe
        $ok = Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password'], 'is_active' => true],
            $request->boolean('remember')
        );

        if (! $ok) {
            RateLimiter::hit($cle, 60);
            ActivityLogger::log('echec_connexion', null, null, ['email' => $data['email']]);
            throw ValidationException::withMessages(['email' => 'Identifiants incorrects ou compte désactivé.']);
        }

        RateLimiter::clear($cle);
        $request->session()->regenerate();

        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        ActivityLogger::log('connexion', $user);

        if ($request->expectsJson()) {
            return response()->json(['user' => $user]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        ActivityLogger::log('deconnexion', $request->user());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Déconnecté.']);
        }

        return redirect()->route('login');
    }
}
