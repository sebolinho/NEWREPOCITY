<link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png')}}">
<link rel="icon" type="image/png" sizes="32x32" href="{{asset('favicon/favicon-32x32.png')}}">
<link rel="icon" type="image/png" sizes="16x16" href="{{asset('favicon/favicon-16x16.png')}}">
<link rel="manifest" href="{{asset('site.webmanifest')}}">

{{-- Preconnect to external domains for performance --}}
<link rel="preconnect" href="https://image.tmdb.org">
<link rel="preconnect" href="https://zf.cantorparcels.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">

{{-- Preload critical fonts --}}
<link rel="preload" href="{{asset('build/assets/Inter-Regular.woff2')}}" as="font" type="font/woff2" crossorigin="anonymous">
<link rel="preload" href="{{asset('build/assets/Inter-Medium.woff2')}}" as="font" type="font/woff2" crossorigin="anonymous">

@vite(['resources/scss/app.scss', 'resources/js/app.js'])

<style>
    :root {
    @if(config('settings.palette'))
        @foreach(config('attr.colors.'.config('settings.palette')) as $color => $value)
            {{'--color-gray-'.$color.':'.hexToRgb('#'.$value)}};
        @endforeach
    @else
        @foreach(config('attr.colors.zinc') as $color => $value)
            {{'--color-gray-'.$color.':'.hexToRgb('#'.$value)}};
        @endforeach
    @endif
           --color-primary-500: @if(config('settings.color')){{hexToRgb(config('settings.color'))}}@else{{hexToRgb('#8b5cf6')}}@endif;
    }
    
    /* Critical CSS for above-the-fold content */
    body{min-height:100vh;display:flex;flex-direction:column;position:relative}
    .dark{--tw-bg-opacity:1;background-color:rgb(3 7 18/var(--tw-bg-opacity))}
    .container{margin-left:auto;margin-right:auto;width:100%;padding-left:1.5rem;padding-right:1.5rem}
    .swiper{margin-left:auto;margin-right:auto;position:relative;overflow:hidden;list-style:none;padding:0;z-index:1}
    .swiper-wrapper{position:relative;width:100%;height:100%;z-index:1;display:flex;transition-property:transform;box-sizing:content-box}
    .swiper-slide{flex-shrink:0;width:100%;height:100%;position:relative;transition-property:transform}
    .aspect-square{aspect-ratio:1/1}
    @media (min-width:1024px){.lg\:aspect-slide{aspect-ratio:3/1}}
    .absolute{position:absolute}
    .relative{position:relative}
    .inset-0{inset:0}
    .z-10{z-index:10}
    .z-20{z-index:20}
    .h-full{height:100%}
    .w-full{width:100%}
    .object-cover{object-fit:cover}
    .bg-gray-950{--tw-bg-opacity:1;background-color:rgb(3 7 18/var(--tw-bg-opacity))}
    .text-white{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}
    .rounded-lg{border-radius:0.5rem}
    [x-cloak]{display:none!important}
    body{overflow-x:hidden}
</style>
{!! config('settings.custom_code') !!}
@if(config('settings.onesignal_id'))
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
    <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function () {
            OneSignal.init({
                appId: "{{env('ONESIGNAL_APP_ID')}}"
            });
        });

        OneSignal.push(function () {
            OneSignal.showNativePrompt();
        });
    </script>
@endif
