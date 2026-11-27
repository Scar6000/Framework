<?php

namespace App\Http\Controllers\Concerns;

/**
 * Rend la vue Blade si elle existe ET si la requête n'attend pas du JSON,
 * sinon renvoie les mêmes données en JSON.
 * => le back fonctionne (et se teste) avant que le front ait créé ses vues.
 */
trait RendersView
{
    protected function render(string $view, array $data = [], int $status = 200)
    {
        if (request()->expectsJson() || ! view()->exists($view)) {
            return response()->json($data, $status);
        }

        return response()->view($view, $data, $status);
    }

    /** Après une action : redirection avec message flash (web) ou JSON (API / tests). */
    protected function done(string $route, array $params, string $message, array $data = [], int $status = 200, array $flash = [])
    {
        if (request()->expectsJson()) {
            return response()->json(['message' => $message, 'data' => $data], $status);
        }

        $redirect = redirect()->route($route, $params)->with('success', $message);
        foreach ($flash as $k => $v) {
            $redirect->with($k, $v);
        }

        return $redirect;
    }
}
