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
        #preloader {
            position: fixed;
            inset: 0;
            z-index: 999999;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
        }
        #preloader.preloader--done {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .preloader-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 24px;
            max-width: 320px;
        }
        .preloader-crest {
            width: 72px;
            height: 72px;
            object-fit: contain;
            margin-bottom: 16px;
        }
        .preloader-title {
            display: block;
            font-size: 1.05rem;
            font-weight: 700;
            color: #073b32;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .preloader-tagline {
            display: block;
            font-size: 0.8rem;
            color: #556960;
            font-weight: 500;
            margin-bottom: 20px;
            line-height: 1.4;
        }
        .preloader-spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #e2e8f0;
            border-top-color: #08543c;
            border-radius: 50%;
            animation: preloaderSpin 0.75s linear infinite;
        }
        @keyframes preloaderSpin {
            to { transform: rotate(360deg); }
        }
        @media (prefers-reduced-motion: reduce) {
            .preloader-spinner { animation: none; border-top-color: #08543c; }
        }
    </style>
</head>
<body>
    <div id="preloader" aria-hidden="true">
        <div class="preloader-inner">
            <img src="{{ asset('images/council-crest-light.webp') }}" alt="Mutoko Rural District Council" class="preloader-crest" width="72" height="72">
            <span class="preloader-title">Mutoko Rural District Council</span>
            <span class="preloader-tagline">Service Delivery for Sustainable Communities</span>
            <div class="preloader-spinner" role="status" aria-label="Loading"></div>
        </div>
    </div>
    @inertia
    <script>
        (function() {
            var p = document.getElementById('preloader');
            if (!p) return;
            setTimeout(function() {
                if (p && !p.classList.contains('preloader--done')) {
                    p.classList.add('preloader--done');
                    setTimeout(function() { if (p && p.parentNode) p.parentNode.removeChild(p); }, 350);
                }
            }, 3000);
        })();
    </script>
</body>
</html>
