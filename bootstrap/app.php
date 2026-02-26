<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // App Middleware Aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'cook' => \App\Http\Middleware\EnsureUserIsCook::class,
            'delivery_driver' => \App\Http\Middleware\EnsureUserIsDeliveryDriver::class,
            'can_sell' => \App\Http\Middleware\EnsureCookCanSell::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'api/mercadopago/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
