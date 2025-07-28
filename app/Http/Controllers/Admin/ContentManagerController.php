<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostEpisode;
use App\Models\Genre;
use App\Models\Country;
use App\Models\People;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Cviebrock\EloquentSluggable\Services\SlugService;

class ContentManagerController extends Controller
{
    public function index()
    {
        $config = [
            'title' => __('Content Manager'),
            'nav' => 'content-manager',
        ];

        // Statistics for dashboard
        $stats = [
            'total_movies' => Post::where('type', 'movie')->count(),
            'total_tv_shows' => Post::where('type', 'tv')->count(),
            'total_episodes' => PostEpisode::count(),
            'draft_content' => Post::where('status', 'draft')->count(),
            'published_content' => Post::where('status', 'publish')->count(),
            'featured_content' => Post::where('featured', 'active')->count(),
            'total_genres' => Genre::count(),
            'total_countries' => Country::count(),
            'total_people' => People::count(),
            'total_tags' => Tag::count(),
        ];

        // Recent activity
        $recent_movies = Post::where('type', 'movie')->orderBy('created_at', 'desc')->limit(5)->get();
        $recent_tv_shows = Post::where('type', 'tv')->orderBy('created_at', 'desc')->limit(5)->get();
        $recent_episodes = PostEpisode::with('post')->orderBy('created_at', 'desc')->limit(5)->get();

        // Content that needs attention
        $pending_review = Post::where('status', 'draft')->orderBy('updated_at', 'desc')->limit(5)->get();
        $missing_images = Post::whereNull('image')->orWhere('image', '')->limit(5)->get();
        $missing_descriptions = Post::where(function($query) {
            $query->whereNull('overview')->orWhere('overview', '');
        })->limit(5)->get();

        return view('admin.content-manager.index', compact('config', 'stats', 'recent_movies', 'recent_tv_shows', 'recent_episodes', 'pending_review', 'missing_images', 'missing_descriptions'));
    }

    public function bulkActions()
    {
        $config = [
            'title' => __('Bulk Actions'),
            'nav' => 'content-manager',
        ];

        return view('admin.content-manager.bulk-actions', compact('config'));
    }

    public function processBulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,draft,delete,feature,unfeature,update_genre,update_country',
            'content_ids' => 'required|array',
            'content_ids.*' => 'integer',
        ]);

        $action = $request->action;
        $contentIds = $request->content_ids;
        $updatedCount = 0;

        try {
            DB::beginTransaction();

            switch ($action) {
                case 'publish':
                    $updatedCount = Post::whereIn('id', $contentIds)->update(['status' => 'publish']);
                    break;

                case 'draft':
                    $updatedCount = Post::whereIn('id', $contentIds)->update(['status' => 'draft']);
                    break;

                case 'delete':
                    $updatedCount = Post::whereIn('id', $contentIds)->count();
                    Post::whereIn('id', $contentIds)->delete();
                    break;

                case 'feature':
                    $updatedCount = Post::whereIn('id', $contentIds)->update(['featured' => 'active']);
                    break;

                case 'unfeature':
                    $updatedCount = Post::whereIn('id', $contentIds)->update(['featured' => 'disable']);
                    break;

                case 'update_genre':
                    if ($request->new_genre_id) {
                        foreach ($contentIds as $postId) {
                            $post = Post::find($postId);
                            if ($post) {
                                $genres = $post->postGenres->pluck('genre_id')->toArray();
                                if (!in_array($request->new_genre_id, $genres)) {
                                    $post->postGenres()->create(['genre_id' => $request->new_genre_id]);
                                    $updatedCount++;
                                }
                            }
                        }
                    }
                    break;

                case 'update_country':
                    if ($request->new_country_id) {
                        $updatedCount = Post::whereIn('id', $contentIds)->update(['country_id' => $request->new_country_id]);
                    }
                    break;
            }

            DB::commit();
            Cache::flush(); // Clear cache after bulk operations

            return response()->json([
                'success' => true,
                'message' => __('Bulk action completed successfully. :count items updated.', ['count' => $updatedCount])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => __('Error processing bulk action: :error', ['error' => $e->getMessage()])
            ], 500);
        }
    }

    public function quickAdd()
    {
        $config = [
            'title' => __('Quick Add Content'),
            'nav' => 'content-manager',
        ];

        $genres = Genre::all();
        $countries = Country::all();

        return view('admin.content-manager.quick-add', compact('config', 'genres', 'countries'));
    }

    public function storeQuickAdd(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:movie,tv',
            'overview' => 'nullable|string',
            'release_date' => 'nullable|date',
            'genre_ids' => 'nullable|array',
            'country_id' => 'nullable|integer',
            'imdb_id' => 'nullable|string',
            'tmdb_id' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // Create slug
            $slug = SlugService::createSlug(Post::class, 'slug', $request->title);

            // Create post
            $post = Post::create([
                'title' => $request->title,
                'slug' => $slug,
                'type' => $request->type,
                'overview' => $request->overview,
                'release_date' => $request->release_date,
                'country_id' => $request->country_id,
                'imdb_id' => $request->imdb_id,
                'tmdb_id' => $request->tmdb_id,
                'status' => 'draft',
                'featured' => 'disable',
                'member' => 'disable',
                'comment' => 'disable',
                'slider' => 'disable',
            ]);

            // Add genres
            if ($request->genre_ids) {
                foreach ($request->genre_ids as $genreId) {
                    $post->postGenres()->create(['genre_id' => $genreId]);
                }
            }

            // Try to fetch additional data from TMDB if ID provided
            if ($request->tmdb_id) {
                $this->fetchTmdbData($post, $request->tmdb_id);
            }

            DB::commit();

            return redirect()->route('admin.content-manager.index')
                ->with('success', __('Content created successfully! You can now edit it to add more details.'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => __('Error creating content: :error', ['error' => $e->getMessage()])])
                ->withInput();
        }
    }

    public function contentAudit()
    {
        $config = [
            'title' => __('Content Audit'),
            'nav' => 'content-manager',
        ];

        // Content quality checks
        $issues = [
            'missing_images' => Post::whereNull('image')->orWhere('image', '')->get(),
            'missing_descriptions' => Post::where(function($query) {
                $query->whereNull('overview')->orWhere('overview', '');
            })->get(),
            'missing_genres' => Post::whereDoesntHave('postGenres')->get(),
            'missing_countries' => Post::whereNull('country_id')->get(),
            'short_titles' => Post::whereRaw('LENGTH(title) < 3')->get(),
            'duplicate_titles' => Post::select('title', DB::raw('count(*) as total'))
                ->groupBy('title')
                ->having('total', '>', 1)
                ->get(),
        ];

        return view('admin.content-manager.audit', compact('config', 'issues'));
    }

    public function fixContentIssue(Request $request)
    {
        $request->validate([
            'issue_type' => 'required|string',
            'post_id' => 'required|integer',
            'fix_data' => 'nullable|array',
        ]);

        try {
            $post = Post::findOrFail($request->post_id);

            switch ($request->issue_type) {
                case 'missing_image':
                    if ($post->tmdb_id) {
                        $this->fetchTmdbImages($post);
                    }
                    break;

                case 'missing_description':
                    if ($post->tmdb_id) {
                        $this->fetchTmdbData($post, $post->tmdb_id);
                    }
                    break;

                case 'missing_genre':
                    if ($request->fix_data['genre_id']) {
                        $post->postGenres()->create(['genre_id' => $request->fix_data['genre_id']]);
                    }
                    break;

                case 'missing_country':
                    if ($request->fix_data['country_id']) {
                        $post->update(['country_id' => $request->fix_data['country_id']]);
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => __('Issue fixed successfully.')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error fixing issue: :error', ['error' => $e->getMessage()])
            ], 500);
        }
    }

    public function searchContent(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');

        $posts = Post::when($query, function ($q) use ($query) {
                return $q->where('title', 'like', "%{$query}%")
                         ->orWhere('slug', 'like', "%{$query}%");
            })
            ->when($type !== 'all', function ($q) use ($type) {
                return $q->where('type', $type);
            })
            ->limit(20)
            ->get(['id', 'title', 'type', 'status', 'image']);

        return response()->json($posts);
    }

    private function fetchTmdbData($post, $tmdbId)
    {
        try {
            $apiKey = env('TMDB_API_KEY');
            if (!$apiKey) return;

            $endpoint = $post->type === 'movie' ? 'movie' : 'tv';
            $response = Http::get("https://api.themoviedb.org/3/{$endpoint}/{$tmdbId}", [
                'api_key' => $apiKey,
                'language' => 'pt-BR'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $updateData = [];
                
                if (empty($post->overview) && !empty($data['overview'])) {
                    $updateData['overview'] = $data['overview'];
                }
                
                if (empty($post->image) && !empty($data['poster_path'])) {
                    $updateData['image'] = 'https://image.tmdb.org/t/p/w500' . $data['poster_path'];
                }
                
                if (empty($post->release_date)) {
                    $releaseKey = $post->type === 'movie' ? 'release_date' : 'first_air_date';
                    if (!empty($data[$releaseKey])) {
                        $updateData['release_date'] = $data[$releaseKey];
                    }
                }
                
                if (!empty($updateData)) {
                    $post->update($updateData);
                }
            }
        } catch (\Exception $e) {
            \Log::error('TMDB fetch error: ' . $e->getMessage());
        }
    }

    private function fetchTmdbImages($post)
    {
        try {
            $apiKey = env('TMDB_API_KEY');
            if (!$apiKey || !$post->tmdb_id) return;

            $endpoint = $post->type === 'movie' ? 'movie' : 'tv';
            $response = Http::get("https://api.themoviedb.org/3/{$endpoint}/{$post->tmdb_id}/images", [
                'api_key' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['posters'][0]['file_path'])) {
                    $post->update([
                        'image' => 'https://image.tmdb.org/t/p/w500' . $data['posters'][0]['file_path']
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('TMDB images fetch error: ' . $e->getMessage());
        }
    }
}