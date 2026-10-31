@extends('layouts.public')

@section('title', 'Business Directory in Osun State | OsunBiz')
@section('meta_description', 'Search approved businesses, shops, and services across towns in Osun State.')

@section('content')
    <section class="border-b border-stone-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-9 sm:px-6 sm:py-12 lg:px-8">
            <nav class="text-xs font-medium text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a><span class="px-2">/</span><span class="text-stone-800">Directory</span></nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Business directory</h1>
            <p class="mt-2 text-sm text-stone-600">Find businesses and services across Osun State.</p>
            <div class="mt-6">
                @include('public.partials.search-form', [
                    'filters' => $filters,
                    'categories' => $categories,
                    'locations' => $locations,
                ])
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-stone-600" aria-live="polite">
                <strong class="text-stone-900">{{ $businesses->total() }}</strong>
                {{ \Illuminate\Support\Str::plural('approved business', $businesses->total()) }} found
                @if (filled($filters['q'] ?? null)) for <strong class="text-stone-900">“{{ $filters['q'] }}”</strong>@endif
            </p>
            <form action="{{ route('directory.index') }}" method="GET" class="flex items-center gap-2">
                @foreach ($filters as $key => $value)
                    @if ($key !== 'sort' && filled($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="sort" class="text-xs font-semibold text-stone-500">Sort by</label>
                <select id="sort" name="sort" onchange="this.form.submit()" class="rounded-lg border-stone-300 py-2 pl-3 pr-8 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                    <option value="rating" @selected(($filters['sort'] ?? 'rating') === 'rating')>Top rated</option>
                    <option value="recent" @selected(($filters['sort'] ?? '') === 'recent')>Newest</option>
                    <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name A–Z</option>
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
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-14 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-stone-100 text-stone-500" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                </span>
                <h2 class="mt-4 text-lg font-bold">No matching businesses yet</h2>
                <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-stone-600">Try a different search or clear some filters. Only approved listings appear in the public directory.</p>
                <a href="{{ route('directory.index') }}" class="mt-5 inline-flex rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50">Clear search</a>
            </div>
        @endif
    </section>
@endsection
