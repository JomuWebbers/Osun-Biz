@extends('layouts.public')

@section('title', 'Businesses in '.$category->name.' in '.$location->name.' | OsunBiz')
@section('meta_description', 'Explore approved '.$category->name.' businesses in '.$location->name.', Osun State.')

@section('content')
    <section class="border-b border-stone-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-9 sm:px-6 sm:py-12 lg:px-8">
            <nav class="text-xs font-medium text-stone-500" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a><span class="px-2">/</span>
                <a href="{{ route('directory.index', ['town' => $location->slug]) }}" class="hover:text-emerald-800">{{ $location->name }}</a><span class="px-2">/</span>
                <span class="text-stone-800">{{ $category->name }}</span>
            </nav>
            <p class="mt-5 text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Explore Osun State</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $category->name }} in {{ $location->name }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">
                Browse {{ $businesses->total() }} approved {{ \Illuminate\Support\Str::plural('business', $businesses->total()) }} in {{ $location->name }}.
                Discover local options and contact businesses directly.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <section class="border-b border-stone-200 py-8">
            <h2 class="text-sm font-bold text-stone-900">Browse other categories in {{ $location->name }}</h2>
            <div class="mt-4 flex gap-2 overflow-x-auto pb-2">
                <a href="{{ route('directory.landing', [$location->slug, $category->slug]) }}" class="shrink-0 rounded-full bg-emerald-800 px-4 py-2 text-xs font-bold text-white">{{ $category->name }} <span class="ml-1 text-emerald-100">{{ $businesses->total() }}</span></a>
                @foreach ($otherCategories as $otherCategory)
                    <a href="{{ route('directory.landing', [$location->slug, $otherCategory->slug]) }}" class="shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 transition hover:border-emerald-300 hover:text-emerald-800">{{ $otherCategory->name }} <span class="ml-1 text-stone-400">{{ $otherCategory->matching_businesses_count }}</span></a>
                @endforeach
            </div>
        </section>

        <section class="py-9">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight">Businesses in {{ $location->name }}</h2>
                    <p class="mt-1 text-sm text-stone-600">{{ $businesses->total() }} approved {{ \Illuminate\Support\Str::plural('listing', $businesses->total()) }}</p>
                </div>
                <form action="{{ route('directory.landing', [$location->slug, $category->slug]) }}" method="GET" class="flex items-center gap-2">
                    <label for="landing-sort" class="text-xs font-semibold text-stone-500">Sort by</label>
                    <select id="landing-sort" name="sort" onchange="this.form.submit()" class="rounded-lg border-stone-300 py-2 pl-3 pr-8 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                        <option value="rating" @selected($sort === 'rating')>Top rated</option>
                        <option value="recent" @selected($sort === 'recent')>Newest</option>
                        <option value="name" @selected($sort === 'name')>Name A–Z</option>
                    </select>
                </form>
            </div>

            @if ($businesses->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($businesses as $business)
                        @include('public.partials.business-card', ['business' => $business])
                    @endforeach
                </div>
                <div class="mt-8">{{ $businesses->links() }}</div>
            @else
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                    <h3 class="font-bold">No approved {{ \Illuminate\Support\Str::lower($category->name) }} listings in {{ $location->name }} yet</h3>
                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-stone-600">Try another category or explore the full directory. New listings appear here after they are approved.</p>
                    <a href="{{ route('directory.index', ['town' => $location->slug]) }}" class="mt-5 inline-flex rounded-lg bg-emerald-800 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-900">Browse all businesses in {{ $location->name }}</a>
                </div>
            @endif
        </section>

        <section class="border-t border-stone-200 py-9">
            <h2 class="text-xl font-extrabold tracking-tight">{{ $category->name }} in other towns</h2>
            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($otherLocations as $otherLocation)
                    <a href="{{ route('directory.landing', [$otherLocation->slug, $category->slug]) }}" class="rounded-xl border border-stone-200 bg-white p-4 transition hover:border-emerald-300 hover:bg-emerald-50/50">
                        <span class="block text-sm font-bold">{{ $otherLocation->name }}</span>
                        <span class="mt-1 block text-xs text-stone-500">{{ $otherLocation->matching_businesses_count }} {{ \Illuminate\Support\Str::plural('listing', $otherLocation->matching_businesses_count) }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection
