<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('customer.login')
                ->with('success', 'Please login to continue');
        }
         // Logged-in customer
        $customer = auth()->guard('customer')->user();

        // IP capture
        $ip = $request->header('X-Forwarded-For') ?? $request->ip();

        //  Update only if changed (optimization)
        if ($customer->ip_address !== $ip) {
            $customer->ip_address = $ip;
            $customer->save();
        }
        return $next($request);
    }
}
