<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Owner dashboard | OsunBiz')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-900 antialiased">
    @include('public.partials.header')

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid items-start gap-8 lg:grid-cols-[15rem_minmax(0,1fr)]">
            <aside class="rounded-2xl border border-stone-200 bg-white p-3">
                <div class="px-3 py-3">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Owner portal</p>
                    <p class="mt-1 truncate font-bold">{{ auth()->user()->name }}</p>
                </div>
                <nav class="mt-2 grid gap-1" aria-label="Owner navigation">
                    <a href="{{ route('owner.dashboard') }}" @class([
                        'rounded-lg px-3 py-2.5 text-sm font-semibold transition',
                        'bg-emerald-50 text-emerald-900' => request()->routeIs('owner.dashboard'),
                        'text-stone-600 hover:bg-stone-50' => ! request()->routeIs('owner.dashboard'),
                    ])>My dashboard</a>
                    <a href="{{ route('owner.businesses.create') }}" @class([
                        'rounded-lg px-3 py-2.5 text-sm font-semibold transition',
                        'bg-emerald-50 text-emerald-900' => request()->routeIs('owner.businesses.create'),
                        'text-stone-600 hover:bg-stone-50' => ! request()->routeIs('owner.businesses.create'),
                    ])>Add a business</a>
                    <a href="{{ route('owner.profile') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-stone-600 transition hover:bg-stone-50">Profile and password</a>
                    <a href="{{ route('home') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-stone-600 transition hover:bg-stone-50">Back to OsunBiz</a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="mt-3 border-t border-stone-100 pt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-stone-600 transition hover:bg-stone-50">Log out</button>
                </form>
            </aside>

            <main class="min-w-0">
                @if (session('status'))
                    <div role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    @include('public.partials.footer')
</body>
</html>
