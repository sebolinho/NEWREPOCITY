<?php

use Intervention\Image\Facades\Image;

use App\Models\Settings;

// Settings Controller update
function update_settings($key, $value)
{
    if (!Settings::where('name', $key)->first()) {
        if (isset($value)) {
            Settings::create([
                'name' => $key,
                'val' => $value,
            ]);
        }

        return true;
    } else {
        if (isset($key) and empty($value)) {
            \Illuminate\Support\Facades\Cache::forget($key);

            return (bool)Settings::where('name', $key)->delete();
        } else {
            Settings::where('name', $key)->update([
                'name' => $key,
                'val' => $value,
            ]);
            \Illuminate\Support\Facades\Cache::forget($key);

            return true;
        }
    }
}

// File upload
if (!function_exists('fileUpload')) {
    function fileUpload($img, $path, $width = null, $height = null, $imgName = null, $webp = null)
    {
        if (isset($img)) {
            try {
                if (!file_exists(public_path($path))) {
                    mkdir(public_path($path), 0777, true);
                }

                // making image
                $makeImg = Image::make($img)->orientate();
                $makeImg->encode($webp, 100);
                $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                $mime = $makeImg->mime();

                // saving image in the target path
                $imgName = $imgName . '.' . $allowed[$mime];
                $imgPath = public_path($path . $imgName);

                if (isset($width) && isset($height)) {
                    if ($width == $height) {
                        $makeImg->fit($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } else {
                        $makeImg->fit($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                } elseif (isset($width)) {
                    $makeImg->resize($width, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                if ($makeImg->save($imgPath)) {
                    return $imgName;
                }
            } catch (\Exception $e) {
                // Handle the exception here if needed (optional)
            }
        }
        return false;
    }
}
// Editor
if (!function_exists('editor_preview')) {
    function editor_preview($text = null)
    {
        $searchVal = array(
            '<h1>',
            '<h2>',
            '<h3>',
            '<h4>',
            '<h5>',
            '<p>',
        );
        $replaceVal = array(
            '<h1 class="text-gray-700 dark:text-gray-200 text-4xl font-semibold mb-4">',
            '<h2 class="text-gray-700 dark:text-gray-200 text-3xl font-semibold mb-4">',
            '<h3 class="text-gray-700 dark:text-gray-200 text-xl font-semibold mb-3">',
            '<h4 class="text-gray-700 dark:text-gray-200 text-lg font-semibold mb-3">',
            '<h5 class="text-gray-700 dark:text-gray-200 text-base font-semibold mb-3">',
            '<p class="text-gray-600 dark:text-gray-400 text-base mb-3">',
        );
        return str_replace($searchVal, $replaceVal, $text);
    }
}

// Gravatar
if (!function_exists('gravatar')) {
    function gravatar($name = null, $image = null, $class = null)
    {
        if (isset($image)) {
            return '<div class="bg-cover ' . $class . '" style="background-image:url(' . $image . ');"></div>';
        } else {
            return '<div class="text-white ' . $class . '">' . mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8') . '</div>';
        }
    }
}

// cover
if (!function_exists('cover')) {
    function cover($image = null, $class = null)
    {
        if (isset($image)) {
            return '<div class="bg-cover ' . $class . '" style="background-image:url(' . $image . ');"></div>';
        } else {
            return '<div class="bg-cover ' . $class . '" style="background-image:url(' . asset('uploads/static/img/cover.png') . ');"></div>';
        }
    }
}

// Webp image name
if (!function_exists('webper')) {
    function webper($image = '')
    {
        $searchVal = ['jpg', 'jpeg', 'png'];
        $replaceVal = ['webp', 'webp', 'webp'];

        return str_replace($searchVal, $replaceVal, $image);
    }
}

// Image view
if (!function_exists('picture')) {
    function picture($image = null, $size = null, $class = null, $title = null, $type = null)
    {
        $allowType = ['post', 'people', 'episode'];
        $sizeHtml = null;
        if (isset($size)) {
            $sizeExp = explode(',', $size);
            $sizeHtml = 'width="' . $sizeExp[0] . '" height="' . $sizeExp[1] . '"';
        }

        if (isset($type) and in_array($type, $allowType) and config('settings.tmdb_image') == 'active') {
            return '<picture>
                <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="' . $image . '" alt="' . $title . '" class="lazyload ' . $class . '" ' . $sizeHtml . '>
            </picture>';
        } elseif (isset($image)) {
            return '<picture>
                <source data-srcset="' . webper($image) . '" type="image/webp" class="' . $class . '">
                <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="' . $image . '" alt="' . $title . '" class="lazyload ' . $class . '" ' . $sizeHtml . '>
            </picture>';
        }

    }
}

// Short number
if (!function_exists('short_number')) {
    function short_number(int $n)
    {
        if ($n >= 0 && $n < 1000) {
            // 1 - 999
            $n_format = floor($n);
            $suffix = '';
        } elseif ($n >= 1000 && $n < 1000000) {
            // 1k-999k
            $n_format = floor($n / 1000);
            $suffix = 'K+';
        } elseif ($n >= 1000000 && $n < 1000000000) {
            // 1m-999m
            $n_format = floor($n / 1000000);
            $suffix = 'M+';
        } elseif ($n >= 1000000000 && $n < 1000000000000) {
            // 1b-999b
            $n_format = floor($n / 1000000000);
            $suffix = 'B+';
        } elseif ($n >= 1000000000000) {
            // 1t+
            $n_format = floor($n / 1000000000000);
            $suffix = 'T+';
        }

        return !empty($n_format . $suffix) ? $n_format . $suffix : 0;
    }
}


if (!function_exists('money_format')) {
    function money_format($amount, $currency, $separator = true, $translate = true)
    {
        if (in_array(strtoupper($currency), config('currencies.zero_decimals'))) {
            return number_format($amount, 0, $translate ? __('.') : '.', $separator ? ($translate ? __(',') : ',') : false);
        } else {
            return number_format($amount, 2, $translate ? __('.') : '.', $separator ? ($translate ? __(',') : ',') : false);
        }
    }
}
if (!function_exists('hexToRgb')) {

    function hexToRgb($hex, $alpha = false)
    {
        $hex = str_replace('#', '', $hex);
        $split = str_split($hex, 2);
        $r = hexdec($split[0]);
        $g = hexdec($split[1]);
        $b = hexdec($split[2]);
        return $r . ' ' . $g . ' ' . $b;
    }
}


// Ranked
if (!function_exists('ranked')) {
    function ranked($xp, $rank = null)
    {
        $Array = [];
        $index = 0;
        foreach ($rank as $key) {
            if ($xp >= $key['xp']) {
                $index++;
                $Array['id'] = $index;
                $Array['level'] = $key['level'];
                $Array['xp'] = $key['xp'];
                $Array['name'] = $key['name'];
            }
        }

        return $Array;
    }
}

// Ranked
if (!function_exists('changeRate')) {
    function changeRate($old, $new, int $precision = 2): float
    {
        if ($old == 0) {
            $old++;
            $new++;
        }

        $change = (($new - $old) / $old) * 100;

        return round($change, $precision);
    }
}


if (!function_exists('checkout_total')) {

    function checkout_total($amount, $currency, $separator = true, $translate = true)
    {

    }
}
if (!function_exists('calculateDiscount')) {
    function calculateDiscount($amount, $discount)
    {
        return $amount * ($discount / 100);
    }
}
if (!function_exists('calculatePostDiscount')) {
    function calculatePostDiscount($amount, $discount)
    {
        return $amount - calculateDiscount($amount, $discount);
    }
}
if (!function_exists('calculateInclusiveTaxes')) {
    function calculateInclusiveTaxes($amount, $discount, $inclusiveTaxRate)
    {
        return calculatePostDiscount($amount, $discount) - (calculatePostDiscount($amount, $discount) / (1 + ($inclusiveTaxRate / 100)));
    }
}
if (!function_exists('calculatePostDiscountLessInclTaxes')) {
    function calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates)
    {
        return calculatePostDiscount($amount, $discount) - calculateInclusiveTaxes($amount, $discount, $inclusiveTaxRates);
    }
}

if (!function_exists('calculateInclusiveTax')) {
    function calculateInclusiveTax($amount, $discount, $inclusiveTaxRate, $inclusiveTaxRates)
    {
        return calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates) * ($inclusiveTaxRate / 100);
    }
}

if (!function_exists('checkoutExclusiveTax')) {
    function checkoutExclusiveTax($amount, $discount, $exclusiveTaxRate, $inclusiveTaxRates)
    {
        return calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates) * ($exclusiveTaxRate / 100);
    }
}
if (!function_exists('checkoutTotal')) {
    function checkoutTotal($amount, $discount, $exclusiveTaxRates, $inclusiveTaxRates)
    {
        return calculatePostDiscount($amount, $discount) + checkoutExclusiveTax($amount, $discount, $exclusiveTaxRates, $inclusiveTaxRates);
    }
}
if (!function_exists('generateTagName')) {
    function generateTagName($tagName)
    {
        /*
          Trim whitespace from beginning and end of tag
        */
        $name = trim($tagName);

        /*
          Convert tag name to lower.
        */
        $name = strtolower($name);

        /*
          Convert anything not a letter or number to a dash.
        */
        $name = preg_replace('/[^a-zA-Z0-9]/', '-', $name);

        /*
          Remove multiple instance of '-' and group to one.
        */
        $name = preg_replace('/-{2,}/', '-', $name);
        /*
          Get rid of leading and trailing '-'
        */
        $name = trim($name, '-');

        /*
          Returns the cleaned tag name
        */
        return $name;
    }
}

if (!function_exists('filesizer')) {
    function filesizer($size, $precision = 2) {
        for($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {}
        return round($size, $precision).['B','kB','MB','GB','TB','PB','EB','ZB','YB'][$i];
    }
}

// SEO and Performance Helper Functions

if (!function_exists('generate_schema_org')) {
    /**
     * Generate Schema.org JSON-LD markup
     */
    function generate_schema_org($type, $data) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type
        ];
        
        return '<script type="application/ld+json">' . json_encode(array_merge($schema, $data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}

if (!function_exists('optimize_meta_title')) {
    /**
     * Optimize meta title for SEO (60 characters max)
     */
    function optimize_meta_title($title, $siteName = null) {
        $siteName = $siteName ?: config('settings.site_name', config('app.name'));
        $maxLength = 60;
        
        if (strlen($title) > $maxLength) {
            $title = substr($title, 0, $maxLength - 3) . '...';
        }
        
        if ($siteName && !str_contains($title, $siteName)) {
            $available = $maxLength - strlen($siteName) - 3;
            if (strlen($title) > $available) {
                $title = substr($title, 0, $available) . '...';
            }
            $title .= ' | ' . $siteName;
        }
        
        return $title;
    }
}

if (!function_exists('optimize_meta_description')) {
    /**
     * Optimize meta description for SEO (160 characters max)
     */
    function optimize_meta_description($description) {
        $maxLength = 160;
        
        if (strlen($description) > $maxLength) {
            $description = substr($description, 0, $maxLength - 3) . '...';
        }
        
        return $description;
    }
}

if (!function_exists('generate_breadcrumb_schema')) {
    /**
     * Generate breadcrumb Schema.org markup
     */
    function generate_breadcrumb_schema($breadcrumbs) {
        $items = [];
        $position = 1;
        
        foreach ($breadcrumbs as $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url']
            ];
        }
        
        return generate_schema_org('BreadcrumbList', [
            'itemListElement' => $items
        ]);
    }
}

if (!function_exists('preload_critical_resource')) {
    /**
     * Generate preload link tag for critical resources
     */
    function preload_critical_resource($href, $as, $type = null, $crossorigin = false) {
        $attrs = [
            'rel="preload"',
            'href="' . $href . '"',
            'as="' . $as . '"'
        ];
        
        if ($type) {
            $attrs[] = 'type="' . $type . '"';
        }
        
        if ($crossorigin) {
            $attrs[] = 'crossorigin';
        }
        
        return '<link ' . implode(' ', $attrs) . '>';
    }
}

if (!function_exists('generate_webp_source')) {
    /**
     * Generate WebP source with fallback for picture element
     */
    function generate_webp_source($imagePath, $alt = '', $class = '', $sizes = null) {
        $webpPath = webper($imagePath);
        
        $sourcesTag = '<source srcset="' . $webpPath . '" type="image/webp"';
        if ($sizes) {
            $sourcesTag .= ' sizes="' . $sizes . '"';
        }
        $sourcesTag .= '>';
        
        $imgTag = '<img src="' . $imagePath . '" alt="' . $alt . '" class="' . $class . '"';
        if ($sizes) {
            $imgTag .= ' sizes="' . $sizes . '"';
        }
        $imgTag .= ' loading="lazy" decoding="async">';
        
        return '<picture>' . $sourcesTag . $imgTag . '</picture>';
    }
}

if (!function_exists('cache_buster')) {
    /**
     * Add cache buster to asset URLs for better cache control
     */
    function cache_buster($url) {
        if (app()->environment('production')) {
            $version = config('app.version', '1.0.0');
            return $url . '?v=' . $version;
        }
        
        return $url . '?v=' . time();
    }
}

if (!function_exists('safe_json_encode')) {
    /**
     * Safely encode data for JavaScript usage
     */
    function safe_json_encode($data) {
        return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('truncate_words')) {
    /**
     * Truncate text by word count while preserving HTML
     */
    function truncate_words($text, $limit = 50, $end = '...') {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . $end;
        }
        return $text;
    }
}

// Enhanced Streaming Platform SEO Functions

if (!function_exists('generate_video_schema')) {
    /**
     * Generate video/movie Schema.org markup for streaming content
     */
    function generate_video_schema($video) {
        $schema = [
            'name' => $video['title'] ?? $video['name'],
            'description' => optimize_meta_description($video['overview'] ?? $video['description'] ?? ''),
            'thumbnailUrl' => $video['poster'] ?? $video['thumbnail'] ?? '',
            'contentUrl' => $video['url'] ?? '',
            'embedUrl' => $video['embed_url'] ?? '',
            'uploadDate' => $video['release_date'] ?? $video['created_at'] ?? '',
            'duration' => isset($video['runtime']) ? 'PT' . $video['runtime'] . 'M' : null,
            'genre' => $video['genres'] ?? [],
            'actor' => $video['cast'] ?? [],
            'director' => $video['director'] ?? [],
            'aggregateRating' => isset($video['vote_average']) ? [
                '@type' => 'AggregateRating',
                'ratingValue' => $video['vote_average'],
                'ratingCount' => $video['vote_count'] ?? 1,
                'bestRating' => 10
            ] : null
        ];
        
        $type = isset($video['episode_count']) ? 'TVSeries' : 'Movie';
        
        return generate_schema_org($type, array_filter($schema));
    }
}

if (!function_exists('generate_critical_css')) {
    /**
     * Generate critical CSS inline for above-the-fold content
     */
    function generate_critical_css() {
        return '<style>
            /* Critical above-the-fold styles */
            body{margin:0;padding:0;font-family:Inter,sans-serif;font-display:swap}
            .hero-section{min-height:50vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)}
            .navigation{position:fixed;top:0;width:100%;z-index:1000;backdrop-filter:blur(10px)}
            .container{max-width:1200px;margin:0 auto;padding:0 1rem}
            .loading{opacity:0;animation:fadeIn 0.3s ease-in-out forwards}
            @keyframes fadeIn{to{opacity:1}}
            .lazy-loading{background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:loading 1.5s infinite}
            @keyframes loading{0%{background-position:200% 0}100%{background-position:-200% 0}}
        </style>';
    }
}

if (!function_exists('optimize_image_attrs')) {
    /**
     * Generate optimized image attributes for better performance
     */
    function optimize_image_attrs($src, $alt = '', $width = null, $height = null, $lazy = true) {
        $attrs = [
            'src' => $src,
            'alt' => $alt,
            'decoding' => 'async'
        ];
        
        if ($width) $attrs['width'] = $width;
        if ($height) $attrs['height'] = $height;
        
        if ($lazy) {
            $attrs['loading'] = 'lazy';
            $attrs['data-src'] = $src;
            $attrs['src'] = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . ($width ?? 300) . ' ' . ($height ?? 200) . '"%3E%3C/svg%3E';
        }
        
        return array_map(function($key, $value) {
            return $key . '="' . htmlspecialchars($value) . '"';
        }, array_keys($attrs), $attrs);
    }
}

if (!function_exists('generate_preconnect_hints')) {
    /**
     * Generate DNS preconnect hints for external resources
     */
    function generate_preconnect_hints() {
        $domains = [
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
            'https://api.themoviedb.org',
            'https://image.tmdb.org',
            'https://www.google-analytics.com',
            'https://googletagmanager.com'
        ];
        
        $links = '';
        foreach ($domains as $domain) {
            $links .= '<link rel="preconnect" href="' . $domain . '" crossorigin>' . "\n";
        }
        
        return $links;
    }
}

if (!function_exists('generate_resource_hints')) {
    /**
     * Generate resource hints for better loading performance
     */
    function generate_resource_hints($resources = []) {
        $hints = '';
        
        // Default critical resources
        $defaultResources = [
            ['href' => asset('css/app.css'), 'as' => 'style'],
            ['href' => asset('js/app.js'), 'as' => 'script'],
            ['href' => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', 'as' => 'style', 'crossorigin' => true]
        ];
        
        $allResources = array_merge($defaultResources, $resources);
        
        foreach ($allResources as $resource) {
            $hints .= '<link rel="preload" href="' . $resource['href'] . '" as="' . $resource['as'] . '"';
            if (isset($resource['type'])) {
                $hints .= ' type="' . $resource['type'] . '"';
            }
            if (isset($resource['crossorigin'])) {
                $hints .= ' crossorigin';
            }
            $hints .= '>' . "\n";
        }
        
        return $hints;
    }
}

if (!function_exists('generate_sitemap_entry')) {
    /**
     * Generate sitemap entry for content
     */
    function generate_sitemap_entry($url, $lastmod = null, $changefreq = 'weekly', $priority = '0.8') {
        return [
            'loc' => $url,
            'lastmod' => $lastmod ?: now()->toISOString(),
            'changefreq' => $changefreq,
            'priority' => $priority
        ];
    }
}

if (!function_exists('optimize_heading_structure')) {
    /**
     * Optimize heading structure for SEO
     */
    function optimize_heading_structure($content) {
        // Ensure proper heading hierarchy
        $content = preg_replace_callback('/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', function($matches) {
            $level = intval($matches[1]);
            $text = $matches[2];
            
            // Add proper semantic structure
            return '<h' . $level . ' class="heading-' . $level . '">' . $text . '</h' . $level . '>';
        }, $content);
        
        return $content;
    }
}

if (!function_exists('generate_video_structured_data')) {
    /**
     * Generate comprehensive video structured data for streaming platform
     */
    function generate_video_structured_data($item) {
        if (isset($item->type) && $item->type === 'tv') {
            return generate_tv_series_schema($item);
        } else {
            return generate_movie_schema($item);
        }
    }
}

if (!function_exists('generate_movie_schema')) {
    /**
     * Generate movie-specific schema
     */
    function generate_movie_schema($movie) {
        $schema = [
            'name' => $movie->title,
            'alternateName' => $movie->original_title ?? $movie->title,
            'description' => optimize_meta_description($movie->overview ?? ''),
            'image' => $movie->poster ?? '',
            'datePublished' => $movie->release_date ?? '',
            'genre' => array_map(function($genre) { return $genre['name']; }, $movie->genres ?? []),
            'duration' => isset($movie->runtime) ? 'PT' . $movie->runtime . 'M' : null,
            'contentRating' => $movie->certification ?? '',
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $movie->vote_average ?? 0,
                'ratingCount' => $movie->vote_count ?? 1,
                'bestRating' => 10
            ],
            'url' => url()->current(),
            'sameAs' => isset($movie->imdb_id) ? 'https://www.imdb.com/title/' . $movie->imdb_id : null
        ];
        
        return generate_schema_org('Movie', array_filter($schema));
    }
}

if (!function_exists('generate_tv_series_schema')) {
    /**
     * Generate TV series-specific schema
     */
    function generate_tv_series_schema($series) {
        $schema = [
            'name' => $series->name,
            'alternateName' => $series->original_name ?? $series->name,
            'description' => optimize_meta_description($series->overview ?? ''),
            'image' => $series->poster ?? '',
            'startDate' => $series->first_air_date ?? '',
            'endDate' => $series->last_air_date ?? '',
            'numberOfSeasons' => $series->number_of_seasons ?? 1,
            'numberOfEpisodes' => $series->number_of_episodes ?? 1,
            'genre' => array_map(function($genre) { return $genre['name']; }, $series->genres ?? []),
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $series->vote_average ?? 0,
                'ratingCount' => $series->vote_count ?? 1,
                'bestRating' => 10
            ],
            'url' => url()->current()
        ];
        
        return generate_schema_org('TVSeries', array_filter($schema));
    }
}

if (!function_exists('generate_person_schema')) {
    /**
     * Generate person schema for cast/crew
     */
    function generate_person_schema($person) {
        $schema = [
            'name' => $person->name,
            'image' => $person->profile_path ?? '',
            'description' => $person->biography ?? '',
            'birthDate' => $person->birthday ?? '',
            'deathDate' => $person->deathday ?? null,
            'birthPlace' => $person->place_of_birth ?? '',
            'url' => url()->current(),
            'sameAs' => isset($person->imdb_id) ? 'https://www.imdb.com/name/' . $person->imdb_id : null
        ];
        
        return generate_schema_org('Person', array_filter($schema));
    }
}
