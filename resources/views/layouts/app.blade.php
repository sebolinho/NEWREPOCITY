<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark"
      dir="{{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- Enhanced SEO Meta Tags --}}
    <title>@if(isset($config['title'])){{$config['title']}}@else{{config('settings.title')}}@endif</title>
    
    <meta name="description" content="@if(isset($config['description'])){{$config['description']}}@else{{config('settings.description')}}@endif">
    <meta name="keywords" content="@if(isset($config['keywords'])){{$config['keywords']}}@else{{config('settings.keywords', 'streaming, movies, TV shows, watch online, entertainment')}}@endif">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="{{config('settings.site_name', config('app.name'))}}">
    <meta name="publisher" content="{{config('settings.site_name', config('app.name'))}}">
    <meta name="theme-color" content="@if(config('settings.color')){{config('settings.color')}}@else#8b5cf6@endif">
    
    {{-- Enhanced Open Graph Tags --}}
    <meta property="og:title" content="@if(isset($config['title'])){{$config['title']}}@else{{config('settings.title')}}@endif">
    <meta property="og:description" content="@if(isset($config['description'])){{$config['description']}}@else{{config('settings.description')}}@endif">
    <meta property="og:type" content="@if(isset($config['og_type'])){{$config['og_type']}}@else website @endif">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{config('settings.site_name', config('app.name'))}}">
    <meta property="og:locale" content="{{app()->getLocale().'_'.strtoupper(app()->getLocale())}}"/>
    @if(!empty($config['image']))
        <meta property="og:image" content="{{ $config['image'] }}"/>
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="@if(isset($config['title'])){{$config['title']}}@else{{config('settings.title')}}@endif">
    @endif

    {{-- Enhanced Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@if(config('settings.twitter_handle')){{ '@'.config('settings.twitter_handle') }}@endif">
    <meta name="twitter:creator" content="@if(config('settings.twitter_handle')){{ '@'.config('settings.twitter_handle') }}@endif">
    <meta name="twitter:title" content="@if(isset($config['title'])){{$config['title']}}@else{{config('settings.title')}}@endif">
    <meta name="twitter:description" content="@if(isset($config['description'])){{$config['description']}}@else{{config('settings.description')}}@endif">
    @if(!empty($config['image']))
        <meta name="twitter:image" content="{{ $config['image'] }}">
        <meta name="twitter:image:alt" content="@if(isset($config['title'])){{$config['title']}}@else{{config('settings.title')}}@endif">
    @endif

    {{-- Schema.org Markup --}}
    <meta itemprop="name" content="@if(isset($config['title'])){{$config['title']}}@else{{config('settings.title')}}@endif">
    <meta itemprop="description" content="@if(isset($config['description'])){{$config['description']}}@else{{config('settings.description')}}@endif">
    @if(!empty($config['image']))
        <meta itemprop="image" content="{{ $config['image'] }}">
    @endif

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}"/>
    
    {{-- DNS Prefetch for Performance --}}
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    {{-- Alternative Language Links --}}
    @if(count(config('languages', [])) > 1)
        @foreach(config('languages', []) as $langCode => $langName)
            <link rel="alternate" hreflang="{{$langCode}}" href="{{url()->current()}}?lang={{$langCode}}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{url()->current()}}">
    @endif

    @include('partials.head')
    @livewireStyles
</head>
<body class="min-h-screen dark:bg-gray-950 flex flex-col relative" x-cloak="" x-data="{ searchOpen: false,loading:false,'sidebarToggle': false,compactToggle: localStorage.getItem('compactToggle') === 'true', cookiePolicy: localStorage.getItem('cookiePolicy'), promote: localStorage.getItem('promote')}"
    x-init="$watch('cookiePolicy', val => {
  localStorage.setItem('cookiePolicy', val);
}) ; $watch('promote', val => {
  localStorage.setItem('promote', val);
}); $watch('compactToggle', val => {
  localStorage.setItem('compactToggle', val);
})">
        @include('partials.navbar',['search' => true])

            @if(!config('settings.layout') || config('settings.layout') == 'horizontal')
                @include('partials.sidenav')
            @endif
            <div class="">
                <div
                    class="flex-1 @if(!config('settings.layout') || config('settings.layout') == 'horizontal'){{'lg:ml-64 rtl:lg:ml-o rtl:lg:mr-64'}}@endif"
                    :class="compactToggle ? 'lg:!ml-20 rtl:lg:!ml-0 rtl:lg:!mr-0' : ''">
                    @yield('content')

                </div>
                <div
                    class="@if(!config('settings.layout') || config('settings.layout') == 'horizontal'){{'lg:ml-64 rtl:lg:ml-o rtl:lg:mr-64'}}@endif mt-auto"
                    :class="compactToggle ? 'lg:!ml-20 rtl:lg:!ml-0 rtl:lg:!mr-0' : ''">
                    @include('partials.footer')
                </div>
            </div>


<livewire:search-component/>
<script src="{{asset('static/js/lazysizes.js')}}"></script>
<livewire:notify-component/>
@stack('javascript')
<x-ui.toast/>
@livewireScripts
</body>
</html>
