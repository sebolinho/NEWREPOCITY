<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PerformanceOptimization
{
    /**
     * Handle an incoming request with advanced performance optimizations.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only apply to HTML responses
        if (!$this->shouldOptimize($response)) {
            return $response;
        }

        $content = $response->getContent();
        
        // Apply performance optimizations
        $content = $this->optimizeHTML($content);
        $content = $this->addResourceHints($content);
        $content = $this->optimizeImages($content);
        $content = $this->addCriticalCSS($content);
        $content = $this->optimizeFonts($content);
        $content = $this->addPerformanceHeaders($content);
        
        $response->setContent($content);
        
        // Add performance headers
        $this->addHeaders($response);
        
        return $response;
    }

    /**
     * Check if response should be optimized
     */
    private function shouldOptimize($response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        return $contentType === 'text/html; charset=UTF-8' ||
               strpos($contentType, 'text/html') !== false;
    }

    /**
     * Optimize HTML content
     */
    private function optimizeHTML(string $content): string
    {
        // Remove unnecessary whitespace and comments
        $content = preg_replace('/<!--(?!<!)[^\[>].*?-->/s', '', $content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/>\s+</', '><', $content);
        
        // Remove empty attributes
        $content = preg_replace('/\s+(?:id|class|style)=""\s*/', ' ', $content);
        
        return trim($content);
    }

    /**
     * Add resource hints for better performance
     */
    private function addResourceHints(string $content): string
    {
        $hints = [
            '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>',
            '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>',
            '<link rel="preconnect" href="https://image.tmdb.org" crossorigin>',
            '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">',
            '<link rel="dns-prefetch" href="//unpkg.com">',
            '<link rel="dns-prefetch" href="//ajax.googleapis.com">',
        ];

        // Add critical resource preloads using Vite
        $preloads = [];
        
        // Try to get Vite manifest for asset URLs
        try {
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            if ($manifest) {
                // Get CSS asset from manifest
                foreach ($manifest as $key => $asset) {
                    if (substr($key, -5) === '.scss' || substr($key, -4) === '.css') {
                        $preloads[] = '<link rel="preload" href="/build/' . $asset['file'] . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
                        break;
                    }
                }
                // Get JS asset from manifest  
                foreach ($manifest as $key => $asset) {
                    if (substr($key, -3) === '.js' && isset($asset['isEntry']) && $asset['isEntry']) {
                        $preloads[] = '<link rel="preload" href="/build/' . $asset['file'] . '" as="script">';
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            // Fallback to basic preload without specific asset URLs
            \Log::info('Vite manifest not found, skipping asset preloads: ' . $e->getMessage());
        }
        
        // Add font preload regardless
        $preloads[] = '<link rel="preload" href="/fonts/inter-var.woff2" as="font" type="font/woff2" crossorigin>';

        $allHints = array_merge($hints, $preloads);
        $hintsHtml = implode("\n    ", $allHints);

        // Insert after opening head tag
        return preg_replace(
            '/(<head[^>]*>)/',
            '$1' . "\n    " . $hintsHtml,
            $content,
            1
        );
    }

    /**
     * Optimize images for better performance
     */
    private function optimizeImages(string $content): string
    {
        // Add loading="lazy" to images below the fold
        $content = preg_replace_callback(
            '/<img([^>]*?)src=(["\'])([^"\']*?)\2([^>]*?)>/i',
            function ($matches) {
                $beforeSrc = $matches[1];
                $quote = $matches[2];
                $src = $matches[3];
                $afterSrc = $matches[4];
                
                // Skip if already has loading attribute or is likely above the fold
                if (strpos($beforeSrc . $afterSrc, 'loading=') !== false ||
                    strpos($beforeSrc . $afterSrc, 'data-src=') !== false) {
                    return $matches[0];
                }
                
                // Add lazy loading and optimize attributes
                $optimizedAttrs = ' loading="lazy" decoding="async"';
                
                // Add WebP source if supported
                $webpSrc = $this->getWebPVersion($src);
                if ($webpSrc !== $src) {
                    $optimizedAttrs .= ' data-webp="' . $webpSrc . '"';
                }
                
                return '<img' . $beforeSrc . 'src=' . $quote . $src . $quote . $afterSrc . $optimizedAttrs . '>';
            },
            $content
        );

        return $content;
    }

    /**
     * Get WebP version of image URL
     */
    private function getWebPVersion(string $url): string
    {
        // Simple WebP URL generation
        if (preg_match('/\.(jpg|jpeg|png)$/i', $url)) {
            return preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $url);
        }
        return $url;
    }

    /**
     * Add critical CSS inline
     */
    private function addCriticalCSS(string $content): string
    {
        $criticalCSS = $this->getCriticalCSS();
        
        if (!empty($criticalCSS)) {
            $criticalStyle = '<style id="critical-css">' . $criticalCSS . '</style>';
            $content = preg_replace(
                '/(<\/head>)/',
                $criticalStyle . "\n$1",
                $content,
                1
            );
        }

        return $content;
    }

    /**
     * Get critical CSS for above-the-fold content
     */
    private function getCriticalCSS(): string
    {
        return '
        body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;margin:0;background:#0f172a;color:#e2e8f0;line-height:1.6}
        .header{background:#1e293b;padding:1rem;position:fixed;top:0;width:100%;z-index:50;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
        .nav{display:flex;align-items:center;justify-content:space-between;max-width:1200px;margin:0 auto}
        .logo{font-size:1.5rem;font-weight:bold;color:#3b82f6;text-decoration:none}
        .main{margin-top:80px;min-height:calc(100vh - 80px)}
        .container{max-width:1200px;margin:0 auto;padding:0 1rem}
        .loading{opacity:0;transition:opacity 0.3s ease}
        .loaded{opacity:1}
        @media(max-width:768px){
            .container{padding:0 0.5rem}
            .nav{flex-direction:column;gap:1rem}
            .header{padding:0.5rem}
        }
        .btn{display:inline-block;padding:0.5rem 1rem;background:#3b82f6;color:white;text-decoration:none;border-radius:0.25rem;transition:background 0.2s ease}
        .btn:hover{background:#2563eb}
        .card{background:#1e293b;border-radius:0.5rem;padding:1.5rem;margin-bottom:1rem;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
        .img-lazy{background:#374151;display:block;width:100%;height:auto}
        ';
    }

    /**
     * Optimize font loading
     */
    private function optimizeFonts(string $content): string
    {
        // Add font-display: swap to Google Fonts
        $content = preg_replace(
            '/(fonts\.googleapis\.com\/css[^"\']*?)(["\'])/i',
            '$1&display=swap$2',
            $content
        );

        // Add font preloading for critical fonts
        $fontPreload = '<link rel="preload" href="/fonts/inter-var.woff2" as="font" type="font/woff2" crossorigin>';
        
        if (strpos($content, 'font-preload-added') === false) {
            $content = preg_replace(
                '/(<head[^>]*>)/',
                '$1' . "\n    " . $fontPreload . "\n    <!-- font-preload-added -->",
                $content,
                1
            );
        }

        return $content;
    }

    /**
     * Add performance-related headers to content
     */
    private function addPerformanceHeaders(string $content): string
    {
        // Add viewport meta tag if missing
        if (strpos($content, 'viewport') === false) {
            $viewport = '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">';
            $content = preg_replace(
                '/(<head[^>]*>)/',
                '$1' . "\n    " . $viewport,
                $content,
                1
            );
        }

        // Add charset if missing
        if (strpos($content, 'charset') === false) {
            $charset = '<meta charset="UTF-8">';
            $content = preg_replace(
                '/(<head[^>]*>)/',
                '$1' . "\n    " . $charset,
                $content,
                1
            );
        }

        return $content;
    }

    /**
     * Add performance headers to response
     */
    private function addHeaders($response): void
    {
        // Cache headers for static assets
        if (request()->is('css/*') || request()->is('js/*') || request()->is('images/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        // Performance headers
        $response->headers->set('X-DNS-Prefetch-Control', 'on');
        $response->headers->set('X-Preload', 'css=/css/app.css; rel=preload; as=style');
        
        // Enable HTTP/2 Server Push if supported
        if (request()->isSecure()) {
            $response->headers->set('Link', '</css/app.css>; rel=preload; as=style, </js/app.js>; rel=preload; as=script');
        }

        // Compression hints
        $response->headers->set('Vary', 'Accept-Encoding');
        
        // Performance timing
        $response->headers->set('Server-Timing', 'total;dur=' . ((microtime(true) - LARAVEL_START) * 1000));
    }
}