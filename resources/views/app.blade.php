<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Official website of Mutoko Rural District Council.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Mutoko Rural District Council">
    <link rel="canonical" href="{{ url()->current() }}">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
