<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LayoutSetting;
use Illuminate\Support\Facades\View;

class ShareLayoutSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get active layout settings
        $layoutSettings = LayoutSetting::getActive();

        // Share with all views
        View::share('layoutSettings', $layoutSettings);

        return $next($request);
    }
}
