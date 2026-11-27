<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => CheckRole::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthorizationException|HttpException $exception, Request $request) {
            $status = $exception instanceof AuthorizationException ? 403 : $exception->getStatusCode();

            if ($status !== 403 || $request->expectsJson()) {
                return null;
            }

            $message = $exception->getMessage();

            return response()->view('errors.403', [
                'deniedMessage' => $message !== '' && $message !== 'Accès refusé' ? $message : null,
            ], 403);
        });
    })->create();
