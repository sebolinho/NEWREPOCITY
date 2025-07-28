<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Genre;
use App\Models\People;
use App\Models\Post;
use App\Models\PostEpisode;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        try {
            $listings = cache()->remember('sitemap_index', 3600, function () {
                return [
                    'post' => Post::where('status', 'publish')->count(),
                    'episode' => PostEpisode::where('status', 'publish')->count(),
                    'genre' => Genre::count(),
                    'people' => People::count(),
                    'blog' => Article::where('status', 'publish')->get(),
                ];
            });

            return response()->view('sitemap.index', [
                'listings' => $listings,
            ])->header('Content-Type', 'text/xml')
              ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            \Log::error('Error generating sitemap index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            abort(500);
        }
    }

    public function main(Request $request)
    {
        try {
            return response()->view('sitemap.main')
                ->header('Content-Type', 'text/xml')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            \Log::error('Error generating main sitemap: ' . $e->getMessage());
            abort(500);
        }
    }

    public function post(Request $request, $page)
    {
        try {
            $cacheKey = "sitemap_post_page_{$page}";
            $listings = cache()->remember($cacheKey, 3600, function () use ($page) {
                return Post::where('status', 'publish')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(config('attr.sitemap'), ['*'], 'page', $page);
            });

            return response()->view('sitemap.post', [
                'listings' => $listings,
            ])->header('Content-Type', 'text/xml')
              ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            \Log::error('Error generating post sitemap: ' . $e->getMessage(), [
                'page' => $page
            ]);
            abort(500);
        }
    }

    public function genre(Request $request, $page)
    {
        try {
            $cacheKey = "sitemap_genre_page_{$page}";
            $listings = cache()->remember($cacheKey, 7200, function () use ($page) {
                return Genre::orderBy('name', 'asc')
                    ->paginate(config('attr.sitemap'), ['*'], 'page', $page);
            });

            return response()->view('sitemap.genre', [
                'listings' => $listings,
            ])->header('Content-Type', 'text/xml')
              ->header('Cache-Control', 'public, max-age=7200');
        } catch (\Exception $e) {
            \Log::error('Error generating genre sitemap: ' . $e->getMessage(), [
                'page' => $page
            ]);
            abort(500);
        }
    }

    public function episode(Request $request, $page)
    {
        try {
            $cacheKey = "sitemap_episode_page_{$page}";
            $listings = cache()->remember($cacheKey, 3600, function () use ($page) {
                return PostEpisode::where('status', 'publish')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(config('attr.sitemap'), ['*'], 'page', $page);
            });

            return response()->view('sitemap.episode', [
                'listings' => $listings,
            ])->header('Content-Type', 'text/xml')
              ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            \Log::error('Error generating episode sitemap: ' . $e->getMessage(), [
                'page' => $page
            ]);
            abort(500);
        }
    }
        return response()->view('sitemap.episode', [
            'listings' => $listings,
        ])->header('Content-Type', 'text/xml');
    }
    public function people(Request $request,$page)
    {
        $listings       = People::paginate(config('attr.sitemap'), ['*'], 'page', $page);
        return response()->view('sitemap.people', [
            'listings' => $listings,
        ])->header('Content-Type', 'text/xml');
    }
}
