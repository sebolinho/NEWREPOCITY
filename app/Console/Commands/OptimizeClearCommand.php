<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class OptimizeClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all optimization caches and rebuild';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Clearing all optimization caches...');

        // Clear Laravel caches
        $this->info('Clearing application caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        // Clear sitemap caches
        $this->info('Clearing sitemap caches...');
        $sitemapKeys = [
            'sitemap_index',
            'sitemap_post_page_*',
            'sitemap_genre_page_*', 
            'sitemap_episode_page_*'
        ];

        foreach ($sitemapKeys as $key) {
            if (str_contains($key, '*')) {
                // For wildcard keys, we'd need to implement cache tag clearing
                // For now, flush all cache
                Cache::flush();
                break;
            } else {
                Cache::forget($key);
            }
        }

        // Rebuild optimizations
        $this->info('Rebuilding optimizations...');
        try {
            Artisan::call('config:cache');
            $this->info('✅ Config cached');
        } catch (\Exception $e) {
            $this->error('❌ Error caching config: ' . $e->getMessage());
        }

        try {
            Artisan::call('route:cache');
            $this->info('✅ Routes cached');
        } catch (\Exception $e) {
            $this->error('❌ Error caching routes: ' . $e->getMessage());
        }

        try {
            Artisan::call('view:cache');
            $this->info('✅ Views cached');
        } catch (\Exception $e) {
            $this->error('❌ Error caching views: ' . $e->getMessage());
        }

        $this->info('🎉 All optimization caches cleared and rebuilt!');
        $this->info('');
        $this->info('💡 Pro tip: Run this command after:');
        $this->info('   - Deploying new code');
        $this->info('   - Changing configuration');
        $this->info('   - Adding new routes');
        $this->info('   - Updating content that affects sitemaps');

        return 0;
    }
}