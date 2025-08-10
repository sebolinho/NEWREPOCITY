<!-- DNS prefetch for external domains -->
<link rel="dns-prefetch" href="//image.tmdb.org">
<link rel="dns-prefetch" href="//www.googletagmanager.com">
<link rel="dns-prefetch" href="//cdn.onesignal.com">

<!-- Preconnect to critical external domains -->
<link rel="preconnect" href="https://image.tmdb.org" crossorigin>
<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>

<!-- Preload critical fonts to prevent layout shift -->
<link rel="preload" href="{{asset('build/assets/Inter-Regular-d612f121.woff2')}}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{asset('build/assets/Inter-Medium-1b498b95.woff2')}}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{asset('build/assets/Inter-SemiBold-15226129.woff2')}}" as="font" type="font/woff2" crossorigin>

<link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png')}}">
<link rel="icon" type="image/png" sizes="32x32" href="{{asset('favicon/favicon-32x32.png')}}">
<link rel="icon" type="image/png" sizes="16x16" href="{{asset('favicon/favicon-16x16.png')}}">
<link rel="manifest" href="{{asset('site.webmanifest')}}">
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
</style>
{!! config('settings.custom_code') !!}
@if(config('settings.onesignal_id'))
    <!-- Defer OneSignal to prevent blocking -->
    <script defer src="https://cdn.onesignal.com/sdks/OneSignalSDK.js"></script>
    <script>
        // Initialize OneSignal after page load
        window.addEventListener('load', function() {
            window.OneSignal = window.OneSignal || [];
            OneSignal.push(function () {
                OneSignal.init({
                    appId: "{{env('ONESIGNAL_APP_ID')}}"
                });
            });

            OneSignal.push(function () {
                OneSignal.showNativePrompt();
            });
        });
    </script>
@endif

<!-- Google Tag Manager - Optimized loading -->
@if(config('app.env') === 'production')
<script>
    // Load Google Tag Manager asynchronously to prevent blocking
    (function() {
        function loadGTM() {
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','G-WSRVTDL74C');
        }
        
        // Load GTM after user interaction or 3 seconds delay
        var loaded = false;
        var loadTimeout = setTimeout(function() {
            if (!loaded) {
                loadGTM();
                loaded = true;
            }
        }, 3000);
        
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(function(event) {
            document.addEventListener(event, function() {
                if (!loaded) {
                    clearTimeout(loadTimeout);
                    loadGTM();
                    loaded = true;
                }
            }, { once: true, passive: true });
        });
    })();
</script>
@endif
