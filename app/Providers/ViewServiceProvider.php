<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $routeName = Route::currentRouteName();
            
            // Default mapping
            $identifier = $routeName;

            // Handle CMS Pages
            if ($routeName === 'page.show') {
                $pageParam = request()->route('page');
                if ($pageParam) {
                    $slug = is_string($pageParam) ? $pageParam : $pageParam->slug;
                    $identifier = 'page:' . $slug;
                }
            }

            $metaTag = \App\Models\MetaTag::where('page_identifier', $identifier)->first();

            $view->with('globalMetaTag', $metaTag);
        });
    }
}
