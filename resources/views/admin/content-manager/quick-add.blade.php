@extends('layouts.admin')
@section('content')
<div class="container">
    <form method="POST" action="{{route('admin.content-manager.store-quick-add')}}" class="max-w-2xl mx-auto">
        @csrf
        
        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{__('Quick Add Content')}}</h3>
                <p class="text-sm text-gray-500 mt-1">{{__('Quickly add new movies or TV shows. You can edit details later.')}}</p>
            </div>

            <!-- Content Type -->
            <div class="mb-6">
                <x-form.label for="type" :value="__('Content Type')" />
                <div class="mt-2 flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="type" value="movie" class="mr-2" checked>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Movie')}}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="type" value="tv" class="mr-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{__('TV Show')}}</span>
                    </label>
                </div>
                <x-form.error class="mt-2" :messages="$errors->get('type')" />
            </div>

            <!-- Title -->
            <div class="mb-6">
                <x-form.label for="title" :value="__('Title')" />
                <x-form.input id="title" name="title" type="text" class="mt-1 block w-full" 
                              value="{{ old('title') }}" placeholder="{{__('Enter title...')}}" required />
                <x-form.error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <!-- Overview/Description -->
            <div class="mb-6">
                <x-form.label for="overview" :value="__('Description')" />
                <textarea id="overview" name="overview" rows="4" 
                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                          placeholder="{{__('Enter description...')}}">{{ old('overview') }}</textarea>
                <x-form.error class="mt-2" :messages="$errors->get('overview')" />
            </div>

            <!-- Release Date -->
            <div class="mb-6">
                <x-form.label for="release_date" :value="__('Release Date')" />
                <x-form.input id="release_date" name="release_date" type="date" class="mt-1 block w-full" 
                              value="{{ old('release_date') }}" />
                <x-form.error class="mt-2" :messages="$errors->get('release_date')" />
            </div>

            <!-- Genres -->
            <div class="mb-6">
                <x-form.label for="genre_ids" :value="__('Genres')" />
                <div class="mt-2 max-h-40 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md p-3">
                    @foreach($genres as $genre)
                    <label class="flex items-center py-1">
                        <input type="checkbox" name="genre_ids[]" value="{{$genre->id}}" class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{$genre->title}}</span>
                    </label>
                    @endforeach
                </div>
                <x-form.error class="mt-2" :messages="$errors->get('genre_ids')" />
            </div>

            <!-- Country -->
            <div class="mb-6">
                <x-form.label for="country_id" :value="__('Country')" />
                <select id="country_id" name="country_id" 
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">{{__('Select Country')}}</option>
                    @foreach($countries as $country)
                    <option value="{{$country->id}}" {{old('country_id') == $country->id ? 'selected' : ''}}>
                        {{$country->title}}
                    </option>
                    @endforeach
                </select>
                <x-form.error class="mt-2" :messages="$errors->get('country_id')" />
            </div>

            <!-- TMDB ID -->
            <div class="mb-6">
                <x-form.label for="tmdb_id" :value="__('TMDB ID (Optional)')" />
                <x-form.input id="tmdb_id" name="tmdb_id" type="number" class="mt-1 block w-full" 
                              value="{{ old('tmdb_id') }}" placeholder="{{__('Enter TMDB ID to auto-fetch data...')}}" />
                <p class="text-xs text-gray-500 mt-1">{{__('If provided, additional data will be fetched from TMDB automatically')}}</p>
                <x-form.error class="mt-2" :messages="$errors->get('tmdb_id')" />
            </div>

            <!-- IMDB ID -->
            <div class="mb-6">
                <x-form.label for="imdb_id" :value="__('IMDB ID (Optional)')" />
                <x-form.input id="imdb_id" name="imdb_id" type="text" class="mt-1 block w-full" 
                              value="{{ old('imdb_id') }}" placeholder="{{__('e.g. tt1234567')}}" />
                <x-form.error class="mt-2" :messages="$errors->get('imdb_id')" />
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{route('admin.content-manager.index')}}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <x-ui.icon name="arrow-left" class="w-4 h-4 mr-2"/>
                    {{__('Back')}}
                </a>
                
                <div class="flex gap-3">
                    <button type="submit" name="status" value="draft"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        <x-ui.icon name="file" class="w-4 h-4 mr-2"/>
                        {{__('Save as Draft')}}
                    </button>
                    
                    <button type="submit" name="status" value="publish"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <x-ui.icon name="check" class="w-4 h-4 mr-2"/>
                        {{__('Add & Publish')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-suggest based on TMDB ID
    const tmdbInput = document.getElementById('tmdb_id');
    const titleInput = document.getElementById('title');
    const overviewInput = document.getElementById('overview');
    const releaseDateInput = document.getElementById('release_date');
    
    tmdbInput.addEventListener('blur', function() {
        const tmdbId = this.value;
        if (tmdbId && tmdbId.length > 0) {
            // Here you could add AJAX call to fetch TMDB data
            // and pre-fill the form fields
        }
    });
});
</script>
@endsection