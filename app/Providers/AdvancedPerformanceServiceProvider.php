<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class AdvancedPerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register performance monitoring services
        $this->app->singleton('performance.monitor', function ($app) {
            return new \App\Services\PerformanceMonitor();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add performance optimizations to all views
        View::composer('*', function ($view) {
            $this->addPerformanceData($view);
        });

        // Register response macros for performance
        $this->registerResponseMacros();
        
        // Add performance middleware globally
        $this->addPerformanceMiddleware();
        
        // Register console commands
        $this->registerCommands();
    }

    /**
     * Add performance data to views
     */
    private function addPerformanceData($view): void
    {
        $performanceData = [
            'critical_css' => $this->getCriticalCSS(),
            'preload_resources' => $this->getPreloadResources(),
            'resource_hints' => $this->getResourceHints(),
            'page_speed_score' => $this->calculatePageSpeedScore(),
            'core_web_vitals' => $this->getCoreWebVitals()
        ];

        $view->with('performance', $performanceData);
    }

    /**
     * Get critical CSS for current page
     */
    private function getCriticalCSS(): string
    {
        $route = request()->route();
        $routeName = $route ? $route->getName() : 'default';
        
        // Cache critical CSS per route
        return cache()->remember("critical_css_{$routeName}", 3600, function () use ($routeName) {
            return $this->generateCriticalCSS($routeName);
        });
    }

    /**
     * Generate critical CSS based on route
     */
    private function generateCriticalCSS(string $routeName): string
    {
        $baseCriticalCSS = '
        *,::before,::after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}
        html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"}
        body{margin:0;line-height:inherit;background-color:#0f172a;color:#e2e8f0}
        .header{position:fixed;top:0;z-index:50;width:100%;background-color:#1e293b;padding:1rem;box-shadow:0 1px 3px 0 rgba(0,0,0,0.1)}
        .nav{display:flex;align-items:center;justify-content:space-between;max-width:80rem;margin-left:auto;margin-right:auto}
        .logo{font-size:1.5rem;line-height:2rem;font-weight:700;color:#3b82f6;text-decoration:none}
        .main{margin-top:5rem;min-height:calc(100vh - 5rem)}
        .container{max-width:80rem;margin-left:auto;margin-right:auto;padding-left:1rem;padding-right:1rem}
        ';

        // Add route-specific critical CSS
        switch ($routeName) {
            case 'index':
            case 'home':
                $baseCriticalCSS .= $this->getHomeCriticalCSS();
                break;
            case 'movie':
            case 'tv':
                $baseCriticalCSS .= $this->getWatchCriticalCSS();
                break;
            case 'browse':
            case 'movies':
            case 'tvshows':
                $baseCriticalCSS .= $this->getBrowseCriticalCSS();
                break;
        }

        return $this->minifyCSS($baseCriticalCSS);
    }

    /**
     * Get critical CSS for home page
     */
    private function getHomeCriticalCSS(): string
    {
        return '
        .hero{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:4rem 0;text-align:center}
        .hero h1{font-size:3rem;font-weight:700;margin-bottom:1rem;color:white}
        .hero p{font-size:1.25rem;margin-bottom:2rem;color:#e2e8f0}
        .btn{display:inline-block;padding:0.75rem 1.5rem;background-color:#3b82f6;color:white;text-decoration:none;border-radius:0.5rem;font-weight:500;transition:background-color 0.2s ease}
        .btn:hover{background-color:#2563eb}
        .grid{display:grid;gap:1.5rem}
        .card{background-color:#1e293b;border-radius:0.5rem;padding:1.5rem;box-shadow:0 1px 3px 0 rgba(0,0,0,0.1)}
        ';
    }

    /**
     * Get critical CSS for watch pages
     */
    private function getWatchCriticalCSS(): string
    {
        return '
        .video-container{position:relative;width:100%;height:0;padding-bottom:56.25%;background-color:#000;border-radius:0.5rem;overflow:hidden}
        .video-player{position:absolute;top:0;left:0;width:100%;height:100%}
        .movie-header{display:flex;gap:2rem;margin-bottom:2rem}
        .movie-poster{width:300px;height:450px;border-radius:0.5rem;object-fit:cover}
        .movie-info h1{font-size:2.5rem;font-weight:700;margin-bottom:1rem}
        .movie-meta{display:flex;gap:1rem;margin-bottom:1rem;font-size:0.875rem;color:#9ca3af}
        ';
    }

    /**
     * Get critical CSS for browse pages
     */
    private function getBrowseCriticalCSS(): string
    {
        return '
        .filter-bar{background-color:#1e293b;padding:1rem;border-radius:0.5rem;margin-bottom:2rem}
        .filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem}
        .movie-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem}
        .movie-card{background-color:#1e293b;border-radius:0.5rem;overflow:hidden;transition:transform 0.2s ease}
        .movie-card:hover{transform:scale(1.05)}
        .movie-poster{width:100%;height:300px;object-fit:cover}
        .movie-title{padding:1rem;font-weight:600}
        ';
    }

    /**
     * Minify CSS
     */
    private function minifyCSS(string $css): string
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        // Remove unnecessary characters
        $css = str_replace(['; ', ' {', '{ ', ' }', '} ', ': ', ' :'], [';', '{', '{', '}', '}', ':', ':'], $css);
        return trim($css);
    }

    /**
     * Get preload resources for current page
     */
    private function getPreloadResources(): array
    {
        $resources = [
            [
                'href' => mix('css/app.css'),
                'as' => 'style',
                'type' => 'text/css'
            ],
            [
                'href' => mix('js/app.js'),
                'as' => 'script',
                'type' => 'text/javascript'
            ]
        ];

        // Add route-specific preloads
        $route = request()->route();
        if ($route && $route->getName() === 'index') {
            $resources[] = [
                'href' => '/api/featured-content',
                'as' => 'fetch',
                'type' => 'application/json'
            ];
        }

        return $resources;
    }

    /**
     * Get resource hints
     */
    private function getResourceHints(): array
    {
        return [
            'preconnect' => [
                'https://fonts.googleapis.com',
                'https://fonts.gstatic.com',
                'https://image.tmdb.org'
            ],
            'dns-prefetch' => [
                '//cdnjs.cloudflare.com',
                '//unpkg.com',
                '//ajax.googleapis.com'
            ]
        ];
    }

    /**
     * Calculate page speed score
     */
    private function calculatePageSpeedScore(): array
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        $currentTime = microtime(true);
        $responseTime = ($currentTime - $startTime) * 1000;

        // Calculate score based on response time
        $score = 100;
        if ($responseTime > 1000) $score -= 20; // -20 for > 1s
        if ($responseTime > 2000) $score -= 30; // -50 total for > 2s
        if ($responseTime > 3000) $score -= 30; // -80 total for > 3s

        return [
            'score' => max(0, $score),
            'response_time' => round($responseTime, 2),
            'grade' => $this->getPerformanceGrade($score)
        ];
    }

    /**
     * Get performance grade
     */
    private function getPerformanceGrade(int $score): string
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * Get Core Web Vitals metrics
     */
    private function getCoreWebVitals(): array
    {
        return [
            'largest_contentful_paint' => [
                'target' => '< 2.5s',
                'description' => 'Time for largest content element to load'
            ],
            'first_input_delay' => [
                'target' => '< 100ms',
                'description' => 'Time from first user interaction to browser response'
            ],
            'cumulative_layout_shift' => [
                'target' => '< 0.1',
                'description' => 'Visual stability during page load'
            ]
        ];
    }

    /**
     * Register response macros
     */
    private function registerResponseMacros(): void
    {
        Response::macro('withPerformanceHeaders', function ($content) {
            return response($content)
                ->header('X-DNS-Prefetch-Control', 'on')
                ->header('X-Preload', 'css=' . mix('css/app.css') . '; rel=preload; as=style')
                ->header('Link', '<' . mix('css/app.css') . '>; rel=preload; as=style, <' . mix('js/app.js') . '>; rel=preload; as=script')
                ->header('Server-Timing', 'total;dur=' . ((microtime(true) - LARAVEL_START) * 1000));
        });

        Response::macro('withCacheHeaders', function ($content, $maxAge = 3600) {
            return response($content)
                ->header('Cache-Control', "public, max-age={$maxAge}")
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT')
                ->header('ETag', md5($content));
        });
    }

    /**
     * Add performance middleware
     */
    private function addPerformanceMiddleware(): void
    {
        // This would be done in Http/Kernel.php typically
        // but showing here for reference
    }

    /**
     * Register console commands
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\OptimizePerformanceCommand::class,
                \App\Console\Commands\GenerateCriticalCSSCommand::class,
                \App\Console\Commands\AnalyzePageSpeedCommand::class,
            ]);
        }
    }
}