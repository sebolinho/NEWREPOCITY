<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;
use App\Models\Comment;
use App\Models\Genre;
use App\Models\Log;
use App\Models\Post;
use App\Models\People;
use App\Models\PostEpisode;
use App\Models\Report;
use App\Models\User;
use App\Traits\PostTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Spatie\Analytics\OrderBy;
use DateTime;
class IndexController extends Controller
{
    use PostTrait;

    public function index()
    {
        $config = [
            'title' => __('Dashboard'),
            'heading' => __('Dashboard'),
            'nav' => 'dashboard',
        ];

        if (env('GOOGLE_P12') AND env('ANALYTICS_PROPERTY_ID')) {
            try {
                $data['sessions'] = Cache::remember('analytic-sessions', now()->addHours(12), function () {
                    try {
                        $sessions = Analytics::get(
                            period: Period::months(1),
                            metrics: ['activeUsers'],
                        );
                        $total = 0;
                        foreach ($sessions as $session) {
                            $total = $total + (int)$session['activeUsers'];
                        }
                        return $total;
                    } catch (\Exception $e) {
                        \Log::error('Analytics sessions error: ' . $e->getMessage());
                        return 0;
                    }
                });
                
                $data['pageViews'] = Cache::remember('analytic-pageViews', now()->addHours(12), function () {
                    try {
                        $sessions = Analytics::get(
                            period: Period::months(1),
                            metrics: ['screenPageViews'],
                        );
                        $total = 0;
                        foreach ($sessions as $session) {
                            $total = $total + (int)$session['screenPageViews'];
                        }
                        return $total;
                    } catch (\Exception $e) {
                        \Log::error('Analytics pageViews error: ' . $e->getMessage());
                        return 0;
                    }
                });
                
                $data['bounceRate'] = Cache::remember('analytic-bounceRate', now()->addHours(12), function () {
                    try {
                        $sessions = Analytics::get(
                            period: Period::months(1),
                            metrics: ['bounceRate'],
                        );
                        $total = 0;
                        foreach ($sessions as $session) {
                            $total = $total + (float)$session['bounceRate'];
                        }
                        return $total;
                    } catch (\Exception $e) {
                        \Log::error('Analytics bounceRate error: ' . $e->getMessage());
                        return 0;
                    }
                });
                
                $data['newSessions'] = Cache::remember('analytic-newSessions', now()->addHours(12), function () {
                    try {
                        $newsessions = Analytics::get(
                            period: Period::months(1),
                            metrics: ['newUsers'],
                        );
                        $total = 0;
                        foreach ($newsessions as $session) {
                            $total = $total + (int)$session['newUsers'];
                        }
                        return $total;
                    } catch (\Exception $e) {
                        \Log::error('Analytics newSessions error: ' . $e->getMessage());
                        return 0;
                    }
                });
                
                $data['popular'] = Cache::remember('analytic-popular', now()->addHours(12), function () {
                    try {
                        return Analytics::fetchMostVisitedPages(Period::months(1), 10);
                    } catch (\Exception $e) {
                        \Log::error('Analytics popular pages error: ' . $e->getMessage());
                        return [];
                    }
                });
                
                $data['visitor'] = Cache::remember('analytic-visitors', 60 * 24, function () {
                    try {
                        $orderBy = [
                            OrderBy::dimension('date', false),
                            OrderBy::metric('activeUsers', false),
                        ];
                        return Analytics::get(
                            Period::days(15),
                            ['activeUsers'],
                            ['date'],
                            30,
                            $orderBy
                        );
                    } catch (\Exception $e) {
                        \Log::error('Analytics visitors error: ' . $e->getMessage());
                        return [];
                    }
                });
            } catch (\Exception $e) {
                \Log::error('General Analytics error: ' . $e->getMessage());
                $data['sessions'] = 0;
                $data['pageViews'] = 0;
                $data['bounceRate'] = 0;
                $data['newSessions'] = 0;
                $data['popular'] = [];
                $data['visitor'] = [];
            }
        } else {
            $data['sessions'] = 0;
            $data['pageViews'] = 0;
            $data['bounceRate'] = 0;
            $data['newSessions'] = 0;
            $data['popular'] = [];
            $data['visitor'] = [];
        }

        // Initialize arrays for visitor data processing
        $data['visitors'] = [];
        $data['date'] = [];

        foreach ($data['visitor'] as $visitor) {
            $data['date'][] = date('d M', strtotime($visitor['date']));
        }
        foreach ($data['visitor'] as $visitor) {
            $data['visitors'][] = $visitor['activeUsers'];
        }

        $data['user'] = User::count();
        $data['comment'] = Comment::count();
        $data['genre'] = Genre::count();
        $data['report'] = Report::count();
        $data['movie'] = Post::where('type','movie')->count();
        $data['tv'] = Post::where('type','tv')->count();
        $data['episode'] = PostEpisode::count();
        $data['people'] = People::count();
        $reports = Report::where('status','pending')->limit(8)->orderBy('id','desc')->get();
        $comments = Comment::where('status','draft')->limit(8)->orderBy('id','desc')->get();

        return view('admin.home.index', compact('config', 'data','reports','comments'));
    }

    public function search(Request $request)
    {
        // Filter
        $search = $request->q;

        return PostCollection::collection(Post::when($search, function ($query) use ($search) {
            return $query->searchUrl($search);
        })->limit(10)->get());
    }

    public function first(Request $request)
    {
        return $this->postjson(Post::where('id', $request->id)->first());
    }
}
