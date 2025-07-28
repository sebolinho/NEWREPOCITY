@extends('layouts.admin')
@section('content')
<div class="container">
    <!-- Stats Overview -->
    <div class="mb-6 grid grid-cols-1 lg:grid-cols-4 gap-5">
        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <x-ui.icon name="movie" class="w-6 h-6 text-blue-600 dark:text-blue-400"/>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{number_format($stats['total_movies'])}}</h3>
                    <p class="text-sm text-gray-500">{{__('Movies')}}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <x-ui.icon name="tv-2" class="w-6 h-6 text-green-600 dark:text-green-400"/>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{number_format($stats['total_tv_shows'])}}</h3>
                    <p class="text-sm text-gray-500">{{__('TV Shows')}}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <x-ui.icon name="calendar" class="w-6 h-6 text-purple-600 dark:text-purple-400"/>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{number_format($stats['total_episodes'])}}</h3>
                    <p class="text-sm text-gray-500">{{__('Episodes')}}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <x-ui.icon name="star" class="w-6 h-6 text-orange-600 dark:text-orange-400"/>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{number_format($stats['featured_content'])}}</h3>
                    <p class="text-sm text-gray-500">{{__('Featured')}}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-6 bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{__('Quick Actions')}}</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{route('admin.content-manager.quick-add')}}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <x-ui.icon name="plus" class="w-4 h-4 mr-2"/>
                {{__('Quick Add Content')}}
            </a>
            <a href="{{route('admin.content-manager.bulk-actions')}}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <x-ui.icon name="list" class="w-4 h-4 mr-2"/>
                {{__('Bulk Actions')}}
            </a>
            <a href="{{route('admin.content-manager.audit')}}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <x-ui.icon name="search" class="w-4 h-4 mr-2"/>
                {{__('Content Audit')}}
            </a>
            <a href="{{route('admin.movie.index')}}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <x-ui.icon name="movie" class="w-4 h-4 mr-2"/>
                {{__('Manage Movies')}}
            </a>
            <a href="{{route('admin.tv.index')}}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <x-ui.icon name="tv-2" class="w-4 h-4 mr-2"/>
                {{__('Manage TV Shows')}}
            </a>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{__('Recent Movies')}}</h3>
            <div class="space-y-3">
                @forelse($recent_movies as $movie)
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <img src="{{$movie->imageurl}}" alt="{{$movie->title}}" class="w-12 h-16 object-cover rounded">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">{{$movie->title}}</h4>
                        <p class="text-sm text-gray-500">{{$movie->created_at->diffForHumans()}}</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($movie->status === 'publish') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                            {{ucfirst($movie->status)}}
                        </span>
                    </div>
                    <a href="{{route('admin.movie.edit', $movie->id)}}" class="text-blue-600 hover:text-blue-700">
                        <x-ui.icon name="edit" class="w-4 h-4"/>
                    </a>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">{{__('No recent movies')}}</p>
                @endforelse
            </div>
        </div>

        <!-- Recent TV Shows -->
        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{__('Recent TV Shows')}}</h3>
            <div class="space-y-3">
                @forelse($recent_tv_shows as $show)
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <img src="{{$show->imageurl}}" alt="{{$show->title}}" class="w-12 h-16 object-cover rounded">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">{{$show->title}}</h4>
                        <p class="text-sm text-gray-500">{{$show->created_at->diffForHumans()}}</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($show->status === 'publish') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                            {{ucfirst($show->status)}}
                        </span>
                    </div>
                    <a href="{{route('admin.tv.edit', $show->id)}}" class="text-blue-600 hover:text-blue-700">
                        <x-ui.icon name="edit" class="w-4 h-4"/>
                    </a>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">{{__('No recent TV shows')}}</p>
                @endforelse
            </div>
        </div>

        <!-- Content Needing Attention -->
        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{__('Needs Review')}}</h3>
            <div class="space-y-3">
                @forelse($pending_review as $content)
                <div class="flex items-center gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <img src="{{$content->imageurl}}" alt="{{$content->title}}" class="w-12 h-16 object-cover rounded">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">{{$content->title}}</h4>
                        <p class="text-sm text-gray-500">{{ucfirst($content->type)}} • {{__('Draft')}}</p>
                        <p class="text-xs text-gray-400">{{$content->updated_at->diffForHumans()}}</p>
                    </div>
                    <a href="{{route('admin.'.($content->type === 'movie' ? 'movie' : 'tv').'.edit', $content->id)}}" 
                       class="text-blue-600 hover:text-blue-700">
                        <x-ui.icon name="edit" class="w-4 h-4"/>
                    </a>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">{{__('No content needs review')}}</p>
                @endforelse
            </div>
        </div>

        <!-- Quality Issues -->
        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{__('Quality Issues')}}</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div>
                        <h4 class="font-medium text-red-900 dark:text-red-400">{{__('Missing Images')}}</h4>
                        <p class="text-sm text-red-700 dark:text-red-300">{{count($missing_images)}} {{__('items')}}</p>
                    </div>
                    <a href="{{route('admin.content-manager.audit')}}#missing-images" class="text-red-600 hover:text-red-700">
                        <x-ui.icon name="arrow-right" class="w-4 h-4"/>
                    </a>
                </div>

                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                    <div>
                        <h4 class="font-medium text-orange-900 dark:text-orange-400">{{__('Missing Descriptions')}}</h4>
                        <p class="text-sm text-orange-700 dark:text-orange-300">{{count($missing_descriptions)}} {{__('items')}}</p>
                    </div>
                    <a href="{{route('admin.content-manager.audit')}}#missing-descriptions" class="text-orange-600 hover:text-orange-700">
                        <x-ui.icon name="arrow-right" class="w-4 h-4"/>
                    </a>
                </div>

                <div class="text-center mt-4">
                    <a href="{{route('admin.content-manager.audit')}}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700">
                        {{__('View All Issues')}}
                        <x-ui.icon name="arrow-right" class="w-4 h-4 ml-1"/>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection