<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/frontend.php',
        ],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    //     web: __DIR__ . '/../routes/web.php',
    //     commands: __DIR__ . '/../routes/console.php',
    //     health: '/up',
    //     then: function () {
    //         \Illuminate\Support\Facades\Route::middleware('web')
    //             ->group(base_path('routes/frontend.php'));
    //     }
    // )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Enable session authentication globally for the web group
        $middleware->authenticateSessions();

        $middleware->api(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->alias([
            'customer.auth' => \App\Http\Middleware\CustomerAuthMiddleware::class,
            'customer.2fa' => \App\Http\Middleware\Customer2FAMiddleware::class,
            'prevent-back' => \App\Http\Middleware\PreventBackHistory::class,
            '2fa' => \App\Http\Middleware\TwoFactorMiddleware::class,
            'setlocale' => \App\Http\Middleware\SetLocale::class,
            'single.session' => \App\Http\Middleware\CheckSingleSession::class,
            'validate.session' => \App\Http\Middleware\Api\ValidateCustomerSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, \Throwable $exception, \Illuminate\Http\Request $request) {
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                // For AJAX/Heartbeat requests, return 401 for instant detection
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => 'Unauthenticated.'], 401);
                }

                return redirect()->guest(route('login'))->with('status', 'Your session has expired due to login from another device or inactivity.');
            }

            return $response;
        });
    })->create();
