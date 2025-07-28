<?php

namespace App\Install;

use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Illuminate\Support\Facades\DB;

class SEOSetup
{
    public function setupBasicSEO($data)
    {
        $this->setupEnvironmentSEO($data);
        $this->setupDatabaseSEO($data);
        $this->generateSitemap();
        
        return [
            'success' => true,
            'message' => 'SEO configuration completed successfully'
        ];
    }

    private function setupEnvironmentSEO($data)
    {
        $env = DotenvEditor::load();
        $env->autoBackup(false);

        // Basic SEO settings
        $siteName = $data->site_name ?? 'NEWREPOCITY Streaming';
        $siteUrl = $data->site_url ?? config('app.url');
        
        $env->setKey('APP_NAME', '"' . $siteName . '"');
        $env->setKey('APP_URL', $siteUrl);
        
        // SEO Meta defaults
        $env->setKey('SEO_TITLE', '"' . $siteName . ' - Filmes e Séries Online"');
        $env->setKey('SEO_DESCRIPTION', '"Assista filmes e séries online grátis em HD. A melhor plataforma de streaming com os lançamentos mais recentes."');
        $env->setKey('SEO_KEYWORDS', '"filmes online, séries online, streaming, assistir online, HD, grátis"');
        
        // Social Media
        $env->setKey('FACEBOOK_PAGE', $data->facebook_page ?? '');
        $env->setKey('TWITTER_HANDLE', $data->twitter_handle ?? '');
        $env->setKey('INSTAGRAM_HANDLE', $data->instagram_handle ?? '');
        
        // Analytics
        $env->setKey('GOOGLE_ANALYTICS_ID', $data->google_analytics ?? '');
        $env->setKey('GOOGLE_SEARCH_CONSOLE', $data->search_console ?? '');
        $env->setKey('FACEBOOK_PIXEL', $data->facebook_pixel ?? '');
        
        // Structured Data
        $env->setKey('ORGANIZATION_NAME', '"' . $siteName . '"');
        $env->setKey('ORGANIZATION_LOGO', $siteUrl . '/logo.png');
        $env->setKey('ORGANIZATION_URL', $siteUrl);
        
        $env->save();
    }

    private function setupDatabaseSEO($data)
    {
        try {
            // Check if settings table exists
            if (!DB::table('settings')->exists()) {
                return; // Skip if settings table doesn't exist yet
            }

            $seoSettings = [
                [
                    'key' => 'site_name',
                    'value' => $data->site_name ?? 'NEWREPOCITY Streaming',
                    'type' => 'text'
                ],
                [
                    'key' => 'site_description', 
                    'value' => $data->site_description ?? 'A melhor plataforma de streaming para assistir filmes e séries online em alta qualidade.',
                    'type' => 'textarea'
                ],
                [
                    'key' => 'meta_keywords',
                    'value' => 'filmes online, séries online, streaming, assistir online, HD, grátis, lançamentos',
                    'type' => 'text'
                ],
                [
                    'key' => 'robots_txt',
                    'value' => $this->generateRobotsTxt(),
                    'type' => 'textarea'
                ],
                [
                    'key' => 'sitemap_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'schema_org_enabled',
                    'value' => '1', 
                    'type' => 'boolean'
                ],
                [
                    'key' => 'open_graph_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'twitter_cards_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'canonical_urls',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'breadcrumbs_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ]
            ];

            foreach ($seoSettings as $setting) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    $setting
                );
            }
        } catch (\Exception $e) {
            // Silently continue if database operations fail
        }
    }

    private function generateRobotsTxt()
    {
        $siteUrl = config('app.url');
        
        return "User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /install/
Disallow: /storage/
Disallow: /*.json$
Disallow: /*?*sort=
Disallow: /*?*filter=

# Crawl-delay for courtesy
Crawl-delay: 1

# Sitemap location
Sitemap: {$siteUrl}/sitemap.xml
Sitemap: {$siteUrl}/sitemap_movies.xml
Sitemap: {$siteUrl}/sitemap_series.xml
Sitemap: {$siteUrl}/sitemap_episodes.xml

# Allow important bots
User-agent: Googlebot
Allow: /

User-agent: Bingbot  
Allow: /

User-agent: facebookexternalhit
Allow: /

User-agent: Twitterbot
Allow: /";
    }

    private function generateSitemap()
    {
        try {
            // Try to generate sitemap using artisan command
            \Artisan::call('sitemap:generate');
        } catch (\Exception $e) {
            // If sitemap command doesn't exist, create basic sitemap
            $this->createBasicSitemap();
        }
    }

    private function createBasicSitemap()
    {
        $siteUrl = config('app.url');
        $now = now()->toISOString();
        
        $basicSitemap = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>' . $siteUrl . '</loc>
        <lastmod>' . $now . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>' . $siteUrl . '/movies</loc>
        <lastmod>' . $now . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>' . $siteUrl . '/tv-shows</loc>
        <lastmod>' . $now . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>' . $siteUrl . '/browse</loc>
        <lastmod>' . $now . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
</urlset>';

        try {
            file_put_contents(public_path('sitemap.xml'), $basicSitemap);
        } catch (\Exception $e) {
            // Silently fail if can't write sitemap
        }
    }

    public function getDefaultSEOConfig()
    {
        return [
            'site_name' => 'NEWREPOCITY Streaming',
            'site_description' => 'A melhor plataforma de streaming para assistir filmes e séries online em alta qualidade.',
            'meta_keywords' => 'filmes online, séries online, streaming, assistir online, HD, grátis',
            'google_analytics' => '',
            'search_console' => '',
            'facebook_pixel' => '',
            'facebook_page' => '',
            'twitter_handle' => '',
            'instagram_handle' => '',
        ];
    }

    public function validateSEOSettings($data)
    {
        $errors = [];
        
        if (empty($data->site_name)) {
            $errors[] = 'Site name is required';
        }
        
        if (!empty($data->site_url) && !filter_var($data->site_url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Site URL must be a valid URL';
        }
        
        if (!empty($data->google_analytics) && !preg_match('/^(G-|UA-|GT-)/', $data->google_analytics)) {
            $errors[] = 'Google Analytics ID format is invalid';
        }
        
        return $errors;
    }

    public function setupAdvancedSEO()
    {
        // Create robots.txt
        $this->createRobotsTxt();
        
        // Create basic structured data
        $this->createStructuredData();
        
        // Setup htaccess optimizations
        $this->optimizeHtaccess();
    }

    private function createRobotsTxt()
    {
        try {
            $robotsContent = $this->generateRobotsTxt();
            file_put_contents(public_path('robots.txt'), $robotsContent);
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    private function createStructuredData()
    {
        $siteUrl = config('app.url');
        $siteName = config('app.name');
        
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $siteUrl,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => $siteUrl . '/search/{search_term_string}',
                'query-input' => 'required name=search_term_string'
            ]
        ];
        
        try {
            $jsonFile = public_path('structured-data.json');
            file_put_contents($jsonFile, json_encode($structuredData, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    private function optimizeHtaccess()
    {
        $htaccessPath = public_path('.htaccess');
        
        if (!file_exists($htaccessPath)) {
            return;
        }
        
        try {
            $htaccessContent = file_get_contents($htaccessPath);
            
            // Add SEO optimizations if not already present
            $seoOptimizations = '
# SEO and Performance Optimizations
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
</IfModule>

# Redirect www to non-www
RewriteEngine On
RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ https://%1/$1 [R=301,L]

';
            
            if (strpos($htaccessContent, 'SEO and Performance Optimizations') === false) {
                $htaccessContent = $seoOptimizations . $htaccessContent;
                file_put_contents($htaccessPath, $htaccessContent);
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}