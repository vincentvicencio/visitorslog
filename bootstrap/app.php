<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\UsertypeMiddleware;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\SingleSessionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Consolidate ALL aliases into one array
        $middleware->alias([
            'prevent-back'   => PreventBackHistory::class,
            'user_type'      => UsertypeMiddleware::class,
            'single.session' => SingleSessionMiddleware::class,
        ]);
    })

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'user_type' => UsertypeMiddleware::class,
        'single.session' => \App\Http\Middleware\SingleSessionMiddleware::class,
        ]);
        $middleware->alias([
        'single.session' => \App\Http\Middleware\SingleSessionMiddleware::class, // Replace with your actual class name
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

    
    
