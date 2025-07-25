<?php

namespace App\Helpers;

class SEOHelper
{
    /**
     * Generate meta tags for a movie/TV show
     */
    public static function generateMovieMeta($post, $request = null)
    {
        $title = $post->title . ' - ' . config('settings.site_name', 'Stream Platform');
        $description = $post->overview ? strip_tags($post->overview) : 'Watch ' . $post->title . ' online for free';
        $description = substr($description, 0, 160) . (strlen($description) > 160 ? '...' : '');
        
        $image = $post->image ? asset($post->image) : asset('images/default-poster.jpg');
        $url = $request ? $request->url() : url()->current();
        
        return [
            'title' => $title,
            'description' => $description,
            'keywords' => self::generateKeywords($post),
            'canonical' => $url,
            'og_title' => $post->title,
            'og_description' => $description,
            'og_image' => $image,
            'og_url' => $url,
            'og_type' => $post->type === 'movie' ? 'video.movie' : 'video.tv_show',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $post->title,
            'twitter_description' => $description,
            'twitter_image' => $image,
            'schema' => self::generateMovieSchema($post, $url)
        ];
    }

    /**
     * Generate keywords from post data
     */
    private static function generateKeywords($post)
    {
        $keywords = [];
        
        // Add title words
        $keywords[] = $post->title;
        
        // Add type
        $keywords[] = $post->type === 'movie' ? 'movie' : 'tv show';
        $keywords[] = $post->type === 'movie' ? 'film' : 'series';
        
        // Add genres
        if (isset($post->genres) && $post->genres) {
            foreach ($post->genres as $genre) {
                $keywords[] = $genre->name;
            }
        }
        
        // Add year
        if ($post->release_date) {
            $keywords[] = date('Y', strtotime($post->release_date));
        }
        
        // Add streaming keywords
        $keywords[] = 'watch online';
        $keywords[] = 'streaming';
        $keywords[] = 'free';
        $keywords[] = 'HD';
        
        return implode(', ', array_unique($keywords));
    }

    /**
     * Generate JSON-LD schema for movies/TV shows
     */
    private static function generateMovieSchema($post, $url)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $post->type === 'movie' ? 'Movie' : 'TVSeries',
            'name' => $post->title,
            'description' => $post->overview,
            'url' => $url,
            'image' => asset($post->image),
            'datePublished' => $post->release_date,
        ];

        // Add rating if available
        if (isset($post->vote_average) && $post->vote_average > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $post->vote_average,
                'ratingCount' => $post->vote_count ?? 1,
                'bestRating' => 10,
                'worstRating' => 0
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

        return json_encode($schema, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate breadcrumb schema
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
                'item' => $breadcrumb['url']
            ];
        }

        return json_encode($schema, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate default meta tags for pages
     */
    public static function generateDefaultMeta($title = null, $description = null, $request = null)
    {
        $siteName = config('settings.site_name', 'Stream Platform');
        $title = $title ? $title . ' - ' . $siteName : $siteName;
        $description = $description ?: 'Watch movies and TV shows online for free in HD quality';
        $url = $request ? $request->url() : url()->current();
        $image = asset('images/og-default.jpg');

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => 'movies, tv shows, streaming, watch online, free, HD',
            'canonical' => $url,
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $image,
            'og_url' => $url,
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => $image,
        ];
    }

    /**
     * Generate meta tags for collections/genres
     */
    public static function generateCollectionMeta($collection, $request = null)
    {
        $title = $collection->name . ' Movies & TV Shows - ' . config('settings.site_name', 'Stream Platform');
        $description = 'Watch ' . $collection->name . ' movies and TV shows online for free in HD quality';
        $url = $request ? $request->url() : url()->current();
        $image = $collection->image ? asset($collection->image) : asset('images/default-collection.jpg');

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $collection->name . ', movies, tv shows, streaming, watch online',
            'canonical' => $url,
            'og_title' => $collection->name . ' Collection',
            'og_description' => $description,
            'og_image' => $image,
            'og_url' => $url,
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $collection->name . ' Collection',
            'twitter_description' => $description,
            'twitter_image' => $image,
        ];
    }
}