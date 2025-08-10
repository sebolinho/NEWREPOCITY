@extends('layouts.app')
@section('content')
    <div class="custom-container">
        @foreach($modules as $module)
            @include('home.partials.'.$module->slug)
            @if($loop->index % 3 == 0)
                @include('partials.ads',['id'=> 4])
            @endif
        @endforeach
        @if(config('settings.footer_description'))
            <div
                class="pb-6 lg:pb-10">{!! editor_preview(config('settings.footer_description')) !!}</div>
        @endif
    </div>
    @push('javascript')
        <!-- Load Swiper.js asynchronously to prevent blocking -->
        <script>
            // Load Swiper.js only when needed
            const loadSwiper = () => {
                if (!window.Swiper) {
                    const script = document.createElement('script');
                    script.src = '{{asset('static/js/swiper.js')}}';
                    script.defer = true;
                    document.head.appendChild(script);
                }
            };
            
            // Load on user interaction or after content is visible
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', loadSwiper);
            } else {
                loadSwiper();
            }
        </script>
    @endpush
@endsection
