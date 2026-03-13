<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Customer2FAMiddleware
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
        // Guard check: only apply to customer guard
        if (!Auth::guard('customer')->check()) {
            return $next($request);
        }

        $customer = Auth::guard('customer')->user();

        // If 2FA is enabled but not verified in session
        if ($customer->google2fa_enabled && !$request->session()->has('customer_2fa_verified')) {
            // Exclude 2FA verification routes to prevent infinite loops
            $excludedRoutes = [
                'customer.2fa.verify',
                'customer.2fa.post-verify',
                'customer.logout'
            ];

            if (!$request->is('customer/2fa/*') && !in_array($request->route()->getName(), $excludedRoutes)) {
                return redirect()->route('customer.2fa.verify');
            }
        }

        return $next($request);
    }
}
