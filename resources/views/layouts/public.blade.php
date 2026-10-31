<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OsunBiz — Discover Osun State')</title>
    <meta name="description" content="@yield('meta_description', 'Find trusted businesses, shops, and services across Osun State.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-900 antialiased">
    @include('public.partials.header')

    <main>
        @yield('content')
    </main>

    @include('public.partials.footer')
</body>
</html>
