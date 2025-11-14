<?php

use App\Http\Middleware\SessionTimeout;
use App\Http\Middleware\RegistrarHistorialMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'session.timeout' => SessionTimeout::class,
            'historial.admin' => RegistrarHistorialMiddleware::class,
        ]);
        
        // Aplicar el middleware de historial a todas las rutas API para usuarios administrativos
        $middleware->api(append: [
            RegistrarHistorialMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
