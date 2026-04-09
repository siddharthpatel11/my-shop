<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckSingleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();

            if ($user->session_id !== Session::get('customer_single_session_token')) {

                Auth::guard('customer')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->ajax()) {
                    $request->session()->flash('error', 'Your session has expired due to login from another device or inactivity.');
                    return response()->json([
                        'message' => 'Your session has expired due to login from another device or inactivity.',
                        'redirect' => route('customer.login')
                    ], 401);
                }

                return redirect()
                    ->route('customer.login')
                    ->with('error', 'Your session has expired due to login from another device or inactivity.');
            }
        }

        return $next($request);
    }
}