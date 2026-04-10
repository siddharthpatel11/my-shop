<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCustomerSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            $refreshToken = $request->header('X-Refresh-Token');

            if (! $refreshToken) {
                return response()->json([
                    'message' => 'Refresh token missing',
                ], 401);
            }

            if ($user->refresh_token !== hash('sha256', $refreshToken)) {
                return response()->json([
                    'message' => 'Session expired',
                ], 401);
            }

            if (! $user->refresh_token_expires_at || now()->gt($user->refresh_token_expires_at)) {
                return response()->json([
                    'message' => 'Refresh token expired',
                ], 401);
            }
        }

        return $next($request);
    }
}
// public function handle(Request $request, Closure $next): Response
// {
//     $user = $request->user();

//     if ($user) {

//         // Refresh token header mathi lo
//         $refreshToken = $request->header('X-Refresh-Token');

//         if (! $refreshToken) {
//             return response()->json([
//                 'message' => 'Refresh token missing',
//             ], 401);
//         }

//         //  Hash compare karo
//         if ($user->refresh_token !== hash('sha256', $refreshToken)) {
//             return response()->json([
//                 'message' => 'Session expired. Please login again.',
//             ], 401);
//         }

//         //  Expiry check
//         if (! $user->refresh_token_expires_at || now()->gt($user->refresh_token_expires_at)) {
//             return response()->json([
//                 'message' => 'Refresh token expired',
//             ], 401);
//         }
//     }

//     return $next($request);
// }
// }
