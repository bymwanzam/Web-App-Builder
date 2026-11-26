<?php

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
            'builder.auth' => \App\Http\Middleware\ValidateBuilderAuth::class,
            'not-installed' => \App\Http\Middleware\NotInstalled::class,
            'installed' => \App\Http\Middleware\Installed::class,
            'api.access' => \App\Http\Middleware\EnsureApiAccess::class,
        ]);

        // Use custom CSRF token verification middleware to exempt webhook routes
        $middleware->validateCsrfTokens(except: [
            'payment-gateways/*/webhook',
        ]);

        // Exclude cookie_consent from encryption so it can be set via JavaScript
        $middleware->encryptCookies(except: [
            'cookie_consent',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
