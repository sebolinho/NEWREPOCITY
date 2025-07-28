<?php

namespace App\Helpers;

class SEOHelper
{
    /**
     * Generate comprehensive meta tags for a movie/TV show with advanced optimizations
     */
    public static function generateMovieMeta($post, $request = null)
    {
        $title = $post->title . ' - ' . config('settings.site_name', 'Stream Platform');
        $description = $post->overview ? strip_tags($post->overview) : 'Watch ' . $post->title . ' online for free';
        $description = substr($description, 0, 155) . (strlen($description) > 155 ? '...' : '');
        
        $image = $post->image ? asset($post->image) : asset('images/default-poster.jpg');
        $url = $request ? $request->url() : url()->current();
        
        // Generate WebP version for better performance
        $webpImage = self::generateWebPUrl($image);
        
        return [
            'title' => $title,
            'description' => $description,
            'keywords' => self::generateKeywords($post),
            'canonical' => $url,
            'viewport' => 'width=device-width, initial-scale=1, viewport-fit=cover',
            'robots' => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
            'language' => app()->getLocale(),
            'charset' => 'UTF-8',
            
            // Open Graph optimized
            'og_title' => $post->title,
            'og_description' => $description,
            'og_image' => $webpImage,
            'og_image_alt' => $post->title . ' poster',
            'og_image_width' => '1200',
            'og_image_height' => '630',
            'og_url' => $url,
            'og_type' => $post->type === 'movie' ? 'video.movie' : 'video.tv_show',
            'og_site_name' => config('settings.site_name', 'Stream Platform'),
            'og_locale' => app()->getLocale(),
            
            // Twitter Card optimized
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $post->title,
            'twitter_description' => $description,
            'twitter_image' => $webpImage,
            'twitter_image_alt' => $post->title . ' poster',
            'twitter_site' => config('settings.twitter_username', '@streamplatform'),
            'twitter_creator' => config('settings.twitter_username', '@streamplatform'),
            
            // Article/Video specific
            'article_published_time' => $post->created_at ? $post->created_at->toISOString() : null,
            'article_modified_time' => $post->updated_at ? $post->updated_at->toISOString() : null,
            'video_duration' => $post->runtime ? 'PT' . $post->runtime . 'M' : null,
            'video_release_date' => $post->release_date,
            
            // Schema.org JSON-LD
            'schema' => self::generateAdvancedMovieSchema($post, $url),
            
            // Critical resource hints
            'preconnect' => [
                'https://fonts.googleapis.com',
                'https://fonts.gstatic.com',
                'https://image.tmdb.org',
                'https://www.youtube.com'
            ],
            'dns_prefetch' => [
                '//cdnjs.cloudflare.com',
                '//unpkg.com',
                '//ajax.googleapis.com'
            ],
            'preload' => [
                [
                    'href' => mix('css/app.css'),
                    'as' => 'style'
                ],
                [
                    'href' => mix('js/app.js'),
                    'as' => 'script'
                ]
            ]
        ];
    }

    /**
     * Generate advanced keywords from post data with semantic optimization
     */
    private static function generateKeywords($post)
    {
        $keywords = [];
        
        // Primary title keywords
        $titleWords = explode(' ', strtolower($post->title));
        $keywords = array_merge($keywords, array_filter($titleWords, function($word) {
            return strlen($word) > 2 && !in_array($word, ['the', 'and', 'for', 'are', 'but', 'not', 'you', 'all', 'can', 'had', 'her', 'was', 'one', 'our', 'out', 'day', 'get', 'has', 'him', 'his', 'how', 'man', 'new', 'now', 'old', 'see', 'two', 'way', 'who', 'boy', 'did', 'its', 'let', 'put', 'say', 'she', 'too', 'use']);
        }));
        
        // Content type keywords
        $keywords[] = $post->type === 'movie' ? 'movie' : 'tv show';
        $keywords[] = $post->type === 'movie' ? 'film' : 'series';
        $keywords[] = $post->type === 'movie' ? 'cinema' : 'television';
        
        // Genre keywords
        if (isset($post->genres) && $post->genres) {
            foreach ($post->genres as $genre) {
                $keywords[] = strtolower($genre->name);
            }
        }
        
        // Year and decade
        if ($post->release_date) {
            $year = date('Y', strtotime($post->release_date));
            $keywords[] = $year;
            $keywords[] = substr($year, 0, 3) . '0s'; // decade
        }
        
        // Quality and streaming keywords
        $streamingKeywords = [
            'watch online', 'streaming', 'free', 'HD', '4K', 'full movie',
            'download', 'online free', 'no registration', 'high quality',
            'english subtitles', 'dubbed', 'stream', 'watch now'
        ];
        $keywords = array_merge($keywords, $streamingKeywords);
        
        // Language keywords
        if (isset($post->country->name)) {
            $keywords[] = strtolower($post->country->name);
        }
        
        // Cast keywords (if available)
        if (isset($post->peoples) && $post->peoples) {
            foreach ($post->peoples->take(3) as $person) {
                $keywords[] = strtolower($person->name);
            }
        }
        
        return implode(', ', array_unique(array_filter($keywords)));
    }

    /**
     * Generate advanced JSON-LD schema with comprehensive SEO optimization
     */
    private static function generateAdvancedMovieSchema($post, $url)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $post->type === 'movie' ? 'Movie' : 'TVSeries',
            'name' => $post->title,
            'description' => $post->overview,
            'url' => $url,
            'image' => [
                '@type' => 'ImageObject',
                'url' => asset($post->image),
                'width' => 1200,
                'height' => 630,
                'caption' => $post->title . ' poster'
            ],
            'datePublished' => $post->release_date,
            'dateCreated' => $post->release_date,
            'dateModified' => $post->updated_at ? $post->updated_at->toISOString() : null,
            'inLanguage' => app()->getLocale(),
            'contentRating' => $post->rated ?? 'NR',
            'keywords' => self::generateKeywords($post),
        ];

        // Add main entity of page
        $schema['mainEntityOfPage'] = [
            '@type' => 'WebPage',
            '@id' => $url
        ];

        // Add publisher
        $schema['publisher'] = [
            '@type' => 'Organization',
            'name' => config('settings.site_name', 'Stream Platform'),
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/logo.png'),
                'width' => 200,
                'height' => 60
            ]
        ];

        // Add rating if available
        if (isset($post->vote_average) && $post->vote_average > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $post->vote_average,
                'ratingCount' => $post->vote_count ?? 1,
                'bestRating' => 10,
                'worstRating' => 0,
                'reviewCount' => $post->vote_count ?? 1
            ];
        }

        // Add genres
        if (isset($post->genres) && $post->genres) {
            $schema['genre'] = $post->genres->pluck('name')->toArray();
        }

        // Add duration for movies
        if ($post->type === 'movie' && isset($post->runtime) && $post->runtime > 0) {
            $schema['duration'] = 'PT' . $post->runtime . 'M';
        }

        // Add country of origin
        if (isset($post->country->name)) {
            $schema['countryOfOrigin'] = [
                '@type' => 'Country',
                'name' => $post->country->name
            ];
        }

        // Add director and actors if available
        if (isset($post->peoples) && $post->peoples) {
            $actors = [];
            $directors = [];
            
            foreach ($post->peoples as $person) {
                if (isset($person->pivot->department)) {
                    if ($person->pivot->department === 'Directing') {
                        $directors[] = [
                            '@type' => 'Person',
                            'name' => $person->name,
                            'url' => route('people', $person->slug)
                        ];
                    } elseif ($person->pivot->department === 'Acting') {
                        $actors[] = [
                            '@type' => 'Person',
                            'name' => $person->name,
                            'url' => route('people', $person->slug)
                        ];
                    }
                } else {
                    // Default to actor if department not specified
                    $actors[] = [
                        '@type' => 'Person',
                        'name' => $person->name,
                        'url' => route('people', $person->slug)
                    ];
                }
            }
            
            if (!empty($directors)) {
                $schema['director'] = $directors;
            }
            if (!empty($actors)) {
                $schema['actor'] = $actors;
            }
        }

        // Add trailer if available
        if (isset($post->trailer)) {
            $schema['trailer'] = [
                '@type' => 'VideoObject',
                'name' => $post->title . ' Trailer',
                'description' => 'Official trailer for ' . $post->title,
                'thumbnailUrl' => asset($post->image),
                'embedUrl' => $post->trailer,
                'uploadDate' => $post->created_at ? $post->created_at->toISOString() : null
            ];
        }

        // Add watch action
        $schema['potentialAction'] = [
            '@type' => 'WatchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => $url,
                'actionPlatform' => [
                    'http://schema.org/DesktopWebPlatform',
                    'http://schema.org/MobileWebPlatform',
                    'http://schema.org/IOSPlatform',
                    'http://schema.org/AndroidPlatform'
                ]
            ],
            'expectsAcceptanceOf' => [
                '@type' => 'Offer',
                'category' => 'free',
                'availability' => 'http://schema.org/InStock'
            ]
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate WebP URL for better performance
     */
    private static function generateWebPUrl($imageUrl)
    {
        // Simple WebP URL generation - in production you'd implement actual WebP conversion
        if (strpos($imageUrl, '.jpg') !== false) {
            return str_replace('.jpg', '.webp', $imageUrl);
        } elseif (strpos($imageUrl, '.png') !== false) {
            return str_replace('.png', '.webp', $imageUrl);
        }
        return $imageUrl;
    }

    /**
     * Generate comprehensive breadcrumb schema with enhanced SEO
     */
    public static function generateBreadcrumbSchema($breadcrumbs)
    {
        if (empty($breadcrumbs)) {
            return '';
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $schema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => [
                    '@type' => 'WebPage',
                    '@id' => $breadcrumb['url'],
                    'name' => $breadcrumb['name'],
                    'url' => $breadcrumb['url']
                ]
            ];
        }

        return json_encode($schema, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate default meta tags for pages with advanced optimization
     */
    public static function generateDefaultMeta($title = null, $description = null, $request = null)
    {
        $siteName = config('settings.site_name', 'Stream Platform');
        $title = $title ? $title . ' - ' . $siteName : $siteName;
        $description = $description ?: 'Watch movies and TV shows online for free in HD quality. Stream the latest releases and classics without registration.';
        $url = $request ? $request->url() : url()->current();
        $image = asset('images/og-default.jpg');

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => 'movies, tv shows, streaming, watch online, free, HD, 4K, cinema, television, series',
            'canonical' => $url,
            'viewport' => 'width=device-width, initial-scale=1, viewport-fit=cover',
            'robots' => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
            'language' => app()->getLocale(),
            'charset' => 'UTF-8',
            
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $image,
            'og_url' => $url,
            'og_type' => 'website',
            'og_site_name' => $siteName,
            'og_locale' => app()->getLocale(),
            
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => $image,
            'twitter_site' => config('settings.twitter_username', '@streamplatform'),
            
            // Performance hints
            'preconnect' => [
                'https://fonts.googleapis.com',
                'https://fonts.gstatic.com'
            ],
            'dns_prefetch' => [
                '//cdnjs.cloudflare.com',
                '//unpkg.com'
            ]
        ];
    }

    /**
     * Generate meta tags for collections/genres with advanced SEO
     */
    public static function generateCollectionMeta($collection, $request = null)
    {
        $title = $collection->name . ' Movies & TV Shows - ' . config('settings.site_name', 'Stream Platform');
        $description = 'Watch ' . $collection->name . ' movies and TV shows online for free in HD quality. Discover the best ' . strtolower($collection->name) . ' content.';
        $url = $request ? $request->url() : url()->current();
        $image = $collection->image ? asset($collection->image) : asset('images/default-collection.jpg');

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $collection->name . ', movies, tv shows, streaming, watch online, free, HD',
            'canonical' => $url,
            'viewport' => 'width=device-width, initial-scale=1, viewport-fit=cover',
            'robots' => 'index, follow',
            
            'og_title' => $collection->name . ' Collection',
            'og_description' => $description,
            'og_image' => $image,
            'og_url' => $url,
            'og_type' => 'website',
            'og_site_name' => config('settings.site_name', 'Stream Platform'),
            
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $collection->name . ' Collection',
            'twitter_description' => $description,
            'twitter_image' => $image,
            
            'schema' => self::generateCollectionSchema($collection, $url)
        ];
    }

    /**
     * Generate schema for collections
     */
    private static function generateCollectionSchema($collection, $url)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $collection->name . ' Collection',
            'description' => 'Collection of ' . $collection->name . ' movies and TV shows',
            'url' => $url,
            'mainEntity' => [
                '@type' => 'ItemList',
                'name' => $collection->name,
                'numberOfItems' => $collection->posts_count ?? 0
            ]
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate critical CSS for above-the-fold content
     */
    public static function generateCriticalCSS()
    {
        return '
        <style>
        body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;margin:0;background:#0f172a;color:#e2e8f0}
        .header{background:#1e293b;padding:1rem;position:fixed;top:0;width:100%;z-index:50}
        .nav{display:flex;align-items:center;justify-content:space-between}
        .logo{font-size:1.5rem;font-weight:bold;color:#3b82f6}
        .main{margin-top:80px;min-height:calc(100vh-80px)}
        .container{max-width:1200px;margin:0 auto;padding:0 1rem}
        .loading{display:none}
        @media(max-width:768px){.container{padding:0 0.5rem}}
        </style>';
    }
}