<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Log the check for debugging
            Log::info('2FA Middleware Check', [
                'path' => $request->path(),
                'user' => $user->email,
                '2fa_enabled' => $user->google2fa_enabled,
                '2fa_verified' => $request->session()->has('2fa_verified')
            ]);

            if ($user->google2fa_enabled && !$request->session()->has('2fa_verified')) {
                // If it's not the verify route, setup route, or logout, redirect to verify
                if (!$request->is('admin/2fa/verify*') && 
                    !$request->is('admin/2fa/setup*') && 
                    !$request->is('logout')) {
                    
                    Log::info('2FA required. Redirecting to verification.', ['from' => $request->path()]);
                    return redirect()->route('admin.2fa.verify');
                }
            }
        }

        return $next($request);
    }
}
