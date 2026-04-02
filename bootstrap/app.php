<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // <-- Agrega esto
use Illuminate\Session\TokenMismatchException; // <-- Agrega esto

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Agregas este bloque para atrapar el error 419 de sesión expirada
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()->route('login')->with('error', 'Tu sesión expiró por inactividad. Por favor, vuelve a iniciar sesión.');
        });
    })->create();
