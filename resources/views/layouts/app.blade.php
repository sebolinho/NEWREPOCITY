<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark"
      dir="{{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Security Headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    
    <!-- Performance Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//image.tmdb.org">
    
    <!-- Title -->
    <title>@if(isset($config['title']))
            {{$config['title']}}
        @else
            {{config('settings.title')}}
        @endif</title>

    <!-- Meta Description -->
    <meta name="description" content="@if(isset($config['description'])){{$config['description']}}@else{{config('settings.description')}}@endif">
    
    <!-- Keywords -->
    @if(isset($config['keywords']))
        <meta name="keywords" content="{{ $config['keywords'] }}">
    @endif
    
    <!-- Canonical URL -->
    <link rel="canonical" href="@if(isset($config['canonical'])){{ $config['canonical'] }}@else{{ url()->current() }}@endif"/>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@if(isset($config['og_type'])){{ $config['og_type'] }}@else website @endif">
    <meta property="og:url" content="@if(isset($config['og_url'])){{ $config['og_url'] }}@else{{ url()->current() }}@endif">
    <meta property="og:title" content="@if(isset($config['og_title'])){{ $config['og_title'] }}@elseif(isset($config['title'])){{$config['title']}}@else{{config('settings.title')}}@endif">
    <meta property="og:description" content="@if(isset($config['og_description'])){{ $config['og_description'] }}@elseif(isset($config['description'])){{$config['description']}}@else{{config('settings.description')}}@endif">
    <meta property="og:image" content="@if(isset($config['og_image'])){{ $config['og_image'] }}@elseif(!empty($config['image'])){{ $config['image'] }}@else{{ asset('images/og-default.jpg') }}@endif">
    <meta property="og:site_name" content="{{ config('settings.site_name', 'Stream Platform') }}">
    <meta property="og:locale" content="{{app()->getLocale().'_'.strtoupper(app()->getLocale())}}"/>

    <!-- Twitter -->
    <meta property="twitter:card" content="@if(isset($config['twitter_card'])){{ $config['twitter_card'] }}@else summary_large_image @endif">
    <meta property="twitter:url" content="@if(isset($config['twitter_url'])){{ $config['twitter_url'] }}@else{{ url()->current() }}@endif">
    <meta property="twitter:title" content="@if(isset($config['twitter_title'])){{ $config['twitter_title'] }}@elseif(isset($config['title'])){{$config['title']}}@else{{config('settings.title')}}@endif">
    <meta property="twitter:description" content="@if(isset($config['twitter_description'])){{ $config['twitter_description'] }}@elseif(isset($config['description'])){{$config['description']}}@else{{config('settings.description')}}@endif">
    <meta property="twitter:image" content="@if(isset($config['twitter_image'])){{ $config['twitter_image'] }}@elseif(!empty($config['image'])){{ $config['image'] }}@else{{ asset('images/og-default.jpg') }}@endif">

    <!-- Schema.org markup -->
    @if(isset($config['schema']))
        <script type="application/ld+json">
            {!! $config['schema'] !!}
        </script>
    @endif
    
    <!-- Breadcrumb Schema -->
    @if(isset($config['breadcrumb_schema']))
        <script type="application/ld+json">
            {!! $config['breadcrumb_schema'] !!}
        </script>
    @endif

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    @include('partials.head')
    @livewireStyles
    @livewireScriptConfig
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
</body>
</html>
