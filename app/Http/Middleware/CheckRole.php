<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Usage : ->middleware('role:admin') ou ->middleware('role:admin,scolarite'). */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! in_array((string) $user->role, $roles, true)) {
            abort(403, 'Accès refusé');
        }

        return $next($request);
    }
}
