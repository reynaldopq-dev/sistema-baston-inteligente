<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckRol::class,
            'forzar.password' => \App\Http\Middleware\ForzarCambioPassword::class,
        ]);

        // Confiar en el proxy del túnel (cloudflared) para que Laravel
        // detecte correctamente que la conexión pública es HTTPS.
        $middleware->trustProxies(at: '*');

        // Cabeceras de seguridad (protección XSS y clickjacking)
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();