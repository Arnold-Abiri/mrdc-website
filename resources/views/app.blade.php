<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <meta name="description" content="Development preview of the Mutoko Rural District Council website."
    <meta name="robots" content="noindex, nofollow">
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
</head>
<body>
    @inertia
</body>
</html>
