<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\ShareLayoutSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        DB::statement("SET time_zone='+05:30'");

        // Register ShareLayoutSettings middleware globally
        app('router')->aliasMiddleware('share.layout', ShareLayoutSettings::class);
        app('router')->pushMiddlewareToGroup('web', ShareLayoutSettings::class);
    }
}
