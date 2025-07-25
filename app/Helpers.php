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
