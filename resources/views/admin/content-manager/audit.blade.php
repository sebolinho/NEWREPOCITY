@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{__('Content Audit')}}</h3>
        <p class="text-sm text-gray-500 mt-1">{{__('Review and fix content quality issues')}}</p>
    </div>

    <!-- Summary Cards -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <x-ui.icon name="image" class="w-6 h-6 text-red-600 dark:text-red-400"/>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{count($issues['missing_images'])}}</h4>
                    <p class="text-sm text-gray-500">{{__('Missing Images')}}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <x-ui.icon name="file-text" class="w-6 h-6 text-orange-600 dark:text-orange-400"/>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{count($issues['missing_descriptions'])}}</h4>
                    <p class="text-sm text-gray-500">{{__('Missing Descriptions')}}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <x-ui.icon name="tag" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"/>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{count($issues['missing_genres'])}}</h4>
                    <p class="text-sm text-gray-500">{{__('Missing Genres')}}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Issues Tabs -->
    <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button class="audit-tab border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400" 
                        data-tab="missing-images">
                    {{__('Missing Images')}} ({{count($issues['missing_images'])}})
                </button>
                <button class="audit-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" 
                        data-tab="missing-descriptions">
                    {{__('Missing Descriptions')}} ({{count($issues['missing_descriptions'])}})
                </button>
                <button class="audit-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" 
                        data-tab="missing-genres">
                    {{__('Missing Genres')}} ({{count($issues['missing_genres'])}})
                </button>
                <button class="audit-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" 
                        data-tab="duplicates">
                    {{__('Duplicates')}} ({{count($issues['duplicate_titles'])}})
                </button>
            </nav>
        </div>

        <!-- Missing Images -->
        <div id="missing-images" class="audit-content p-6">
            <div class="mb-4 flex justify-between items-center">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{__('Content Missing Images')}}</h4>
                <button class="fix-all-btn px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" 
                        data-issue-type="missing_image">
                    {{__('Auto-fix All (TMDB)')}}
                </button>
            </div>
            
            <div class="space-y-4">
                @forelse($issues['missing_images'] as $item)
                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="w-16 h-20 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                        <x-ui.icon name="image" class="w-8 h-8 text-gray-400"/>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-medium text-gray-900 dark:text-gray-100">{{$item->title}}</h5>
                        <p class="text-sm text-gray-500">{{ucfirst($item->type)}} • {{$item->created_at->format('M d, Y')}}</p>
                        @if($item->tmdb_id)
                        <p class="text-xs text-blue-600 dark:text-blue-400">TMDB ID: {{$item->tmdb_id}}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @if($item->tmdb_id)
                        <button class="fix-issue-btn px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700" 
                                data-post-id="{{$item->id}}" 
                                data-issue-type="missing_image">
                            {{__('Auto-fix')}}
                        </button>
                        @endif
                        <a href="{{route('admin.'.($item->type === 'movie' ? 'movie' : 'tv').'.edit', $item->id)}}" 
                           class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700">
                            {{__('Edit')}}
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <x-ui.icon name="check-circle" class="w-16 h-16 mx-auto mb-4 text-green-500"/>
                    <p>{{__('All content has images! Great job.')}}</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Missing Descriptions -->
        <div id="missing-descriptions" class="audit-content p-6 hidden">
            <div class="mb-4 flex justify-between items-center">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{__('Content Missing Descriptions')}}</h4>
                <button class="fix-all-btn px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" 
                        data-issue-type="missing_description">
                    {{__('Auto-fix All (TMDB)')}}
                </button>
            </div>
            
            <div class="space-y-4">
                @forelse($issues['missing_descriptions'] as $item)
                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <img src="{{$item->imageurl}}" alt="{{$item->title}}" class="w-16 h-20 object-cover rounded">
                    <div class="flex-1">
                        <h5 class="font-medium text-gray-900 dark:text-gray-100">{{$item->title}}</h5>
                        <p class="text-sm text-gray-500">{{ucfirst($item->type)}} • {{$item->created_at->format('M d, Y')}}</p>
                        @if($item->tmdb_id)
                        <p class="text-xs text-blue-600 dark:text-blue-400">TMDB ID: {{$item->tmdb_id}}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @if($item->tmdb_id)
                        <button class="fix-issue-btn px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700" 
                                data-post-id="{{$item->id}}" 
                                data-issue-type="missing_description">
                            {{__('Auto-fix')}}
                        </button>
                        @endif
                        <a href="{{route('admin.'.($item->type === 'movie' ? 'movie' : 'tv').'.edit', $item->id)}}" 
                           class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700">
                            {{__('Edit')}}
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <x-ui.icon name="check-circle" class="w-16 h-16 mx-auto mb-4 text-green-500"/>
                    <p>{{__('All content has descriptions! Great job.')}}</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Missing Genres -->
        <div id="missing-genres" class="audit-content p-6 hidden">
            <div class="mb-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{__('Content Missing Genres')}}</h4>
            </div>
            
            <div class="space-y-4">
                @forelse($issues['missing_genres'] as $item)
                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <img src="{{$item->imageurl}}" alt="{{$item->title}}" class="w-16 h-20 object-cover rounded">
                    <div class="flex-1">
                        <h5 class="font-medium text-gray-900 dark:text-gray-100">{{$item->title}}</h5>
                        <p class="text-sm text-gray-500">{{ucfirst($item->type)}} • {{$item->created_at->format('M d, Y')}}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{route('admin.'.($item->type === 'movie' ? 'movie' : 'tv').'.edit', $item->id)}}" 
                           class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{__('Add Genres')}}
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <x-ui.icon name="check-circle" class="w-16 h-16 mx-auto mb-4 text-green-500"/>
                    <p>{{__('All content has genres! Great job.')}}</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Duplicates -->
        <div id="duplicates" class="audit-content p-6 hidden">
            <div class="mb-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{__('Duplicate Titles')}}</h4>
            </div>
            
            <div class="space-y-4">
                @forelse($issues['duplicate_titles'] as $duplicate)
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
                        "{{$duplicate->title}}" ({{$duplicate->total}} {{__('duplicates')}})
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        {{__('Review these items and consider merging or renaming them.')}}
                    </p>
                    <a href="{{route('admin.movie.index')}}?q={{urlencode($duplicate->title)}}" 
                       class="inline-flex items-center px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{__('Review Duplicates')}}
                        <x-ui.icon name="arrow-right" class="w-4 h-4 ml-1"/>
                    </a>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <x-ui.icon name="check-circle" class="w-16 h-16 mx-auto mb-4 text-green-500"/>
                    <p>{{__('No duplicate titles found! Great job.')}}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{route('admin.content-manager.index')}}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <x-ui.icon name="arrow-left" class="w-4 h-4 mr-2"/>
            {{__('Back to Content Manager')}}
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    document.querySelectorAll('.audit-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Update tab styles
            document.querySelectorAll('.audit-tab').forEach(t => {
                t.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            
            // Show/hide content
            document.querySelectorAll('.audit-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            const targetTab = this.dataset.tab;
            document.getElementById(targetTab).classList.remove('hidden');
        });
    });

    // Fix individual issues
    document.querySelectorAll('.fix-issue-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const postId = this.dataset.postId;
            const issueType = this.dataset.issueType;
            
            try {
                this.disabled = true;
                this.textContent = '{{__('Fixing...')}}';
                
                const response = await fetch('{{route('admin.content-manager.fix-issue')}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        post_id: postId,
                        issue_type: issueType
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    this.textContent = '{{__('Fixed!')}}';
                    this.classList.remove('bg-green-600', 'hover:bg-green-700');
                    this.classList.add('bg-gray-400');
                    
                    // Remove the item after a delay
                    setTimeout(() => {
                        this.closest('.bg-gray-50, .bg-gray-800').remove();
                    }, 1000);
                } else {
                    alert(result.message || '{{__('Error fixing issue.')}}');
                    this.disabled = false;
                    this.textContent = '{{__('Auto-fix')}}';
                }
            } catch (error) {
                console.error('Error fixing issue:', error);
                alert('{{__('Error fixing issue. Please try again.')}}');
                this.disabled = false;
                this.textContent = '{{__('Auto-fix')}}';
            }
        });
    });
});
</script>
@endsection