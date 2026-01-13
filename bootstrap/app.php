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
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // TRUST PROXIES (Required for ngrok/LB)
        // $middleware->trustProxies(at: '*');
        
        // Exclude Midtrans webhook from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'midtrans/*',
            'midtrans/notification', 
        ]);

        // Redirect authenticated users based on role
        $middleware->redirectUsersTo(function () {
            if (auth()->check() && auth()->user()->role === 'admin') {
                return route('admin.dashboard');
            }
            return route('menu.index');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
