<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Première connexion : l'utilisateur doit remplacer son mot de passe temporaire. */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Vous devez changer votre mot de passe.'], 403);
            }

            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
