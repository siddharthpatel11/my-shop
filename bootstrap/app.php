<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
          web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/frontend.php', // ✅ ADD THIS
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // ->withRouting(
    //     web: __DIR__ . '/../routes/web.php',
    //     commands: __DIR__ . '/../routes/console.php',
    //     health: '/up',
    //     then: function () {
    //         require base_path('routes/frontend.php');
    //     }
    // )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'customer.auth' => \App\Http\Middleware\CustomerAuthMiddleware  ::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
