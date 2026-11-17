<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <meta name="description" content="Mutoko Rural District Council — local government services, public notices, news, documents, wards and development across Mutoko District, Mashonaland East, Zimbabwe.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Mutoko Rural District Council">
    <link rel="canonical" href="{{ url()->current() }}">
    @php($localeSegments = explode('/', trim((string) parse_url(url()->current(), PHP_URL_PATH), '/')))
    @if (in_array($localeSegments[0] ?? '', ['en', 'sn', 'nd'], true))
        @foreach (['en', 'sn', 'nd'] as $alternateLocale)
            <link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ url($alternateLocale.'/'.implode('/', array_slice($localeSegments, 1))) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ url('en/'.implode('/', array_slice($localeSegments, 1))) }}">
    @endif
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
    <style>
        #mrdc-initial-preloader {
            position: fixed;
            inset: 0;
            z-index: 999999;
            background-color: #f7faf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            transition: opacity 0.35s ease-out, visibility 0.35s ease-out;
        }
        #mrdc-initial-preloader.mrdc-preloader--done {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .mrdc-preloader-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 24px;
            max-width: 320px;
        }
        .mrdc-preloader-crest {
            width: 72px;
            height: 72px;
            object-fit: contain;
            margin-bottom: 16px;
            filter: drop-shadow(0 2px 8px rgba(7, 59, 50, 0.1));
            animation: mrdcPreloaderPulse 2s ease-in-out infinite;
        }
        .mrdc-preloader-title {
            display: block;
            font-size: 1.05rem;
            font-weight: 700;
            color: #073b32;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .mrdc-preloader-tagline {
            display: block;
            font-size: 0.78rem;
            color: #4a5c54;
            font-weight: 500;
            letter-spacing: 0.01em;
            margin-bottom: 22px;
            line-height: 1.4;
        }
        .mrdc-preloader-track {
            width: 140px;
            height: 3.5px;
            background: #e0ece3;
            border-radius: 999px;
            overflow: hidden;
            position: relative;
        }
        .mrdc-preloader-bar {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 40%;
            background: linear-gradient(90deg, #074336, #0b8f62, #d97706);
            border-radius: 999px;
            animation: mrdcPreloaderSlide 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        @keyframes mrdcPreloaderSlide {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(350%); }
        }
        @keyframes mrdcPreloaderPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(0.97); opacity: 0.88; }
        }
        @media (prefers-reduced-motion: reduce) {
            .mrdc-preloader-crest { animation: none; }
            .mrdc-preloader-bar { animation: none; width: 100%; }
        }
    </style>
</head>
<body>
    <div id="mrdc-initial-preloader" aria-hidden="true">
        <div class="mrdc-preloader-inner">
            <img src="{{ asset('images/council-crest-light.webp') }}" alt="Mutoko Rural District Council" class="mrdc-preloader-crest" width="72" height="72">
            <div class="mrdc-preloader-text">
                <span class="mrdc-preloader-title">Mutoko Rural District Council</span>
                <span class="mrdc-preloader-tagline">Service Delivery for Sustainable Communities</span>
            </div>
            <div class="mrdc-preloader-track" role="progressbar" aria-label="Loading council portal">
                <div class="mrdc-preloader-bar"></div>
            </div>
        </div>
    </div>
    @inertia
    <script>
        (function() {
            var p = document.getElementById('mrdc-initial-preloader');
            if (!p) return;
            // Hard failsafe timeout: hide after 3.5s in case of slow or dropped rural connection
            setTimeout(function() {
                if (p && !p.classList.contains('mrdc-preloader--done')) {
                    p.classList.add('mrdc-preloader--done');
                    setTimeout(function() { if (p && p.parentNode) p.parentNode.removeChild(p); }, 400);
                }
            }, 3500);
        })();
    </script>
</body>
</html>
