<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\RendersView;
use App\Http\Controllers\Controller;
use App\Models\User;
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

        $compteDesactive = User::where('email', $data['email'])->first()?->is_active === false
            && Auth::validate(['email' => $data['email'], 'password' => $data['password']]);

        if ($compteDesactive) {
            RateLimiter::hit($cle, 60);
            ActivityLogger::log('echec_connexion', null, null, ['email' => $data['email'], 'motif' => 'compte_desactive']);
            $message = 'Votre compte est désactivé. Pour demander sa réactivation, veuillez contacter l’administrateur de l’application.';

            if ($request->expectsJson()) {
                throw ValidationException::withMessages(['email' => $message]);
            }

            return redirect()->route('login')
                ->with('compte_desactive', $message)
                ->withInput();
        }

        $ok = Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password'], 'is_active' => true],
            $request->boolean('remember')
        );

        if (! $ok) {
            RateLimiter::hit($cle, 60);
            ActivityLogger::log('echec_connexion', null, null, ['email' => $data['email']]);
            throw ValidationException::withMessages(['email' => 'Identifiants incorrects.']);
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
