<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register performance monitoring services
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Monitor slow database queries in production
        if (!app()->environment('local') && config('app.debug') === false) {
            DB::listen(function (QueryExecuted $query) {
                if ($query->time > 1000) { // Log queries taking more than 1 second
                    Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                        'connection' => $query->connectionName,
                        'url' => request()->url()
                    ]);
                }
            });
        }

        // Add global view data for SEO
        View::composer('*', function ($view) {
            $viewName = $view->getName();
            
            // Skip for admin views to avoid overhead
            if (str_starts_with($viewName, 'admin.')) {
                return;
            }

            // Add global SEO data
            $view->with([
                'globalSeo' => [
                    'site_name' => config('settings.site_name', 'Stream Platform'),
                    'base_url' => config('app.url'),
                    'locale' => app()->getLocale(),
                ]
            ]);
        });

        // Optimize image loading with lazy loading attributes
        View::composer(['watch.*', 'browse.*', 'home.*'], function ($view) {
            $view->with('lazyLoadImages', true);
        });
    }
}