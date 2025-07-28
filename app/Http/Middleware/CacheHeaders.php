<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $cacheTime = 3600): Response
    {
        $response = $next($request);

        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $response;
        }

        // Don't cache if user is authenticated (for personalized content)
        if (auth()->check()) {
            return $response;
        }

        // Set cache headers based on content type
        $contentType = $response->headers->get('Content-Type');
        
        if (str_contains($contentType, 'text/html')) {
            // Cache HTML pages for shorter time
            $this->setHtmlCacheHeaders($response, 300); // 5 minutes
        } elseif (str_contains($contentType, 'application/json')) {
            // Cache API responses
            $this->setCacheHeaders($response, 600); // 10 minutes
        } elseif (str_contains($contentType, 'image/') || 
                  str_contains($contentType, 'text/css') || 
                  str_contains($contentType, 'application/javascript')) {
            // Cache static assets for longer
            $this->setCacheHeaders($response, 31536000); // 1 year
        }

        return $response;
    }

    /**
     * Set cache headers for HTML content
     */
    private function setHtmlCacheHeaders(Response $response, int $maxAge)
    {
        $response->headers->set('Cache-Control', "public, max-age={$maxAge}, s-maxage={$maxAge}");
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
        $response->headers->set('Vary', 'Accept-Encoding');
        
        // Add ETag for validation
        $etag = md5($response->getContent());
        $response->headers->set('ETag', $etag);
    }

    /**
     * Set cache headers for other content
     */
    private function setCacheHeaders(Response $response, int $maxAge)
    {
        $response->headers->set('Cache-Control', "public, max-age={$maxAge}, immutable");
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
        $response->headers->set('Vary', 'Accept-Encoding');
    }
}