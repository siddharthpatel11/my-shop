<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    protected array $supportedLocales = ['en', 'hi', 'gu', 'sa', 'bn'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale', 'en');

        if ($request->is('api/*')) {
            // For API: Explicit '?lang=' query param overrides 'Accept-Language' header
            if ($request->has('lang')) {
                $locale = $request->query('lang');
            } elseif ($request->hasHeader('Accept-Language')) {
                // E.g., 'en-US' -> 'en'
                $locale = substr($request->header('Accept-Language'), 0, 2);
            }
        } else {
            // For Web: Use explicitly set Session value
            if ($request->hasSession()) {
                $locale = $request->session()->get('locale', config('app.locale', 'en'));
            }
        }

        if (!in_array($locale, $this->supportedLocales)) {
            $locale = 'en';
        }

        // Force Admin backend routes to always evaluate in English to prevent CRUD conflicts
        $isFrontend = $request->is('/', 'frontend/*', 'cart*', 'customer*', 'contact*', 'gallery*', 'language/*');
        if (!$isFrontend && !$request->is('api/*')) {
            $locale = config('app.locale', 'en');
        }

        // $isAdmin = $request->is('admin/*', 'dashboard/*');
        // if ($isAdmin) {
        //     $locale = config('app.locale', 'en');
        // }

        App::setLocale($locale);

        return $next($request);
    }
}
