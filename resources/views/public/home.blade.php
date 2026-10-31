@extends('layouts.public')

@section('title', 'OsunBiz — Discover local businesses across Osun State')
@section('meta_description', 'Find businesses, shops, and services in Osogbo, Ile-Ife, Ilesa, Ede, and other towns across Osun State.')

@section('content')
    <section class="relative isolate overflow-hidden bg-emerald-950">
        <div class="absolute inset-0 -z-10 opacity-30" aria-hidden="true">
            <div class="absolute -right-20 -top-32 h-96 w-96 rounded-full bg-emerald-500 blur-3xl"></div>
            <div class="absolute -bottom-48 left-1/4 h-96 w-96 rounded-full bg-amber-400 blur-3xl"></div>
        </div>
        <div class="mx-auto max-w-7xl px-4 pb-14 pt-16 sm:px-6 sm:pb-20 sm:pt-20 lg:px-8 lg:pb-24 lg:pt-24">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold tracking-wide text-emerald-50">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-300"></span>
                    Your guide to local business in Osun
                </span>
                <h1 class="mt-6 text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">Find the right local business, <span class="text-emerald-300">closer to you.</span></h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-emerald-100/80 sm:text-lg">Explore shops, services, and places to eat across Osun State — from Osogbo to Ile-Ife and beyond.</p>
            </div>

            <div class="mx-auto mt-9 max-w-5xl">
                @include('public.partials.search-form', [
                    'filters' => [],
                    'categories' => $categories,
                    'locations' => $locations,
                ])
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-x-8 gap-y-3 text-sm text-emerald-100/80">
                <span><strong class="text-white">{{ number_format($businessCount) }}</strong> approved businesses</span>
                <span><strong class="text-white">{{ number_format($categoryCount) }}</strong> categories</span>
                <span><strong class="text-white">{{ number_format($locationCount) }}</strong> towns</span>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <section id="categories" class="scroll-mt-24 py-14 sm:py-16">
            <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Find what you need</p>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Browse by category</h2>
                    <p class="mt-2 text-sm text-stone-600">Explore businesses by the products and services they offer.</p>
                </div>
                <a href="{{ route('directory.index') }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950">View all businesses <span aria-hidden="true">→</span></a>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($categories as $category)
                    <a href="{{ route('directory.index', ['category' => $category->slug]) }}" class="group flex min-h-28 items-center gap-3 rounded-2xl border border-stone-200 bg-white p-4 transition hover:border-emerald-300 hover:bg-emerald-50/50 sm:p-5">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-extrabold text-emerald-800 transition group-hover:bg-emerald-100" aria-hidden="true">{{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}</span>
                        <span class="min-w-0">
                            <span class="block line-clamp-2 text-sm font-bold text-stone-900 group-hover:text-emerald-900">{{ $category->name }}</span>
                            <span class="mt-1 block text-xs text-stone-500">{{ $category->businesses_count }} {{ \Illuminate\Support\Str::plural('business', $category->businesses_count) }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="border-t border-stone-200 py-14 sm:py-16">
            <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Around the state</p>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Explore popular towns</h2>
                </div>
                <a href="{{ route('directory.index') }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950">Browse the directory <span aria-hidden="true">→</span></a>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($popularLocations as $location)
                    <a href="{{ route('directory.index', ['town' => $location->slug]) }}" class="group rounded-2xl border border-stone-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow-sm">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-sm font-bold text-amber-800" aria-hidden="true">{{ mb_strtoupper(mb_substr($location->name, 0, 1)) }}</span>
                        <span class="mt-4 block font-bold group-hover:text-emerald-800">{{ $location->name }}</span>
                        <span class="mt-1 block text-xs text-stone-500">{{ $location->businesses_count }} {{ \Illuminate\Support\Str::plural('business', $location->businesses_count) }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="border-t border-stone-200 py-14 sm:py-16">
            <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Discover local favourites</p>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Featured businesses</h2>
                    <p class="mt-2 text-sm text-stone-600">Approved listings from businesses across Osun State.</p>
                </div>
                <a href="{{ route('directory.index', ['sort' => 'rating']) }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950">See all listings <span aria-hidden="true">→</span></a>
            </div>
            @if ($featuredBusinesses->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featuredBusinesses as $business)
                        @include('public.partials.business-card', ['business' => $business])
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-10 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-xl text-emerald-800" aria-hidden="true">+</span>
                    <h3 class="mt-4 font-bold">Local listings are on their way</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-600">There are no approved business listings to feature yet. Browse categories or add a business to help people discover Osun.</p>
                    <a href="{{ route('register') }}" class="mt-5 inline-flex rounded-lg bg-emerald-800 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-900">List your business</a>
                </div>
            @endif
        </section>
    </div>
@endsection
