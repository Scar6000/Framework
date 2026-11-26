<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $message = 'Votre compte est désactivé. Pour demander sa réactivation, veuillez contacter l’administrateur de l’application.';

            if ($request->expectsJson()) {
                abort(403, $message);
            }

            return redirect()->route('login')->with('compte_desactive', $message);
        }

        return $next($request);
    }
}
