<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\UsertypeMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your alias here inside the main chain
        $middleware->alias([
            'prevent-back' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'user_type' => UsertypeMiddleware::class,
        ]);
        $middleware->alias([
        'single.session' => \App\Http\Middleware\SingleSessionMiddleware::class, // Replace with your actual class name
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

    
    
