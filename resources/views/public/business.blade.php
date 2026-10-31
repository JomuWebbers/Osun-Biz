@extends('layouts.public')

@section('title', $business->name.' in '.$business->location->name.' | OsunBiz')
@section('meta_description', \Illuminate\Support\Str::limit($business->description ?: $business->name.' in '.$business->location->name.', Osun State.', 155))

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <nav class="text-xs font-medium text-stone-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a><span class="px-2">/</span>
            <a href="{{ route('directory.index') }}" class="hover:text-emerald-800">Directory</a><span class="px-2">/</span>
            <a href="{{ route('directory.landing', [$business->location->slug, $business->category->slug]) }}" class="hover:text-emerald-800">{{ $business->category->name }} in {{ $business->location->name }}</a><span class="px-2">/</span>
            <span class="text-stone-800">{{ $business->name }}</span>
        </nav>

        <section class="mt-6 overflow-hidden rounded-3xl border border-stone-200 bg-white">
            <div class="h-36 bg-gradient-to-r from-emerald-950 via-emerald-800 to-emerald-700 sm:h-52"></div>
            <div class="px-5 pb-6 sm:px-8 sm:pb-8">
                <div class="-mt-10 flex flex-col gap-5 sm:-mt-12 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex items-end gap-4">
                        <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border-4 border-white bg-amber-50 text-3xl font-black text-emerald-900 shadow-sm sm:h-24 sm:w-24" aria-hidden="true">{{ mb_strtoupper(mb_substr($business->name, 0, 1)) }}</span>
                        <div class="pb-1">
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-800">Approved listing</span>
                            <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $business->name }}</h1>
                        </div>
                    </div>
                    @if ($business->phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $business->phone) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-emerald-800 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-900">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.8 2.1Z"/></svg>
                            Call business
                        </a>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-3 border-t border-stone-100 pt-5 text-sm text-stone-600">
                    <a href="{{ route('directory.landing', [$business->location->slug, $business->category->slug]) }}" class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $business->category->name }}</a>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        {{ $business->address ? $business->address.', ' : '' }}{{ $business->location->name }}, Osun State
                    </span>
                    <a href="{{ route('directory.reviews', $business->slug) }}" class="inline-flex items-center gap-1.5 hover:text-emerald-800">
                        <span class="text-amber-500" aria-hidden="true">★</span>
                        <strong class="text-stone-900">{{ number_format((float) $business->rating, 1) }}</strong>
                        <span>({{ $business->reviews_count }} {{ \Illuminate\Support\Str::plural('review', $business->reviews_count) }})</span>
                    </a>
                </div>
            </div>
        </section>

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <div class="space-y-6">
                <section class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                    <h2 class="text-lg font-extrabold">About this business</h2>
                    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-stone-600">{{ $business->description ?: 'More information about this business will be added soon.' }}</p>
                </section>

                <section class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-extrabold">Reviews and ratings</h2>
                            <p class="mt-1 text-sm text-stone-500">{{ number_format((float) $business->rating, 1) }} average · {{ $business->reviews_count }} approved {{ \Illuminate\Support\Str::plural('review', $business->reviews_count) }}</p>
                        </div>
                        @if ($business->reviews_count > 0)
                            <a href="{{ route('directory.reviews', $business->slug) }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950">See all reviews <span aria-hidden="true">→</span></a>
                        @endif
                    </div>
                    @if ($reviews->isNotEmpty())
                        <div class="mt-5 divide-y divide-stone-100">
                            @foreach ($reviews as $review)
                                <article class="py-4 first:pt-0 last:pb-0">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="font-bold text-stone-900">{{ $review->user->name }}</span>
                                        <time class="text-xs text-stone-500" datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('j M Y') }}</time>
                                    </div>
                                    <p class="mt-1 text-sm font-semibold text-amber-600" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating) }}<span class="text-stone-300">{{ str_repeat('★', 5 - $review->rating) }}</span></p>
                                    @if ($review->comment)
                                        <p class="mt-2 text-sm leading-6 text-stone-600">{{ $review->comment }}</p>
                                    @endif
                                    @if ($review->owner_reply)
                                        <div class="mt-3 rounded-xl bg-stone-50 p-4">
                                            <p class="text-xs font-bold text-stone-800">Owner response</p>
                                            <p class="mt-1 text-sm leading-6 text-stone-600">{{ $review->owner_reply }}</p>
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 rounded-xl bg-stone-50 px-4 py-5 text-sm text-stone-600">No approved reviews yet. Check back later for customer feedback.</p>
                    @endif
                </section>

                @if ($similarBusinesses->isNotEmpty())
                    <section>
                        <div class="mb-4 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">More nearby</p>
                                <h2 class="mt-1 text-xl font-extrabold">Similar businesses</h2>
                            </div>
                            <a href="{{ route('directory.landing', [$business->location->slug, $business->category->slug]) }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950">View all</a>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($similarBusinesses as $similarBusiness)
                                @include('public.partials.business-card', ['business' => $similarBusiness])
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="space-y-5 lg:sticky lg:top-24">
                <section class="rounded-2xl border border-stone-200 bg-white p-5">
                    <h2 class="font-extrabold">Contact details</h2>
                    <dl class="mt-4 space-y-4 text-sm">
                        @if ($business->phone)
                            <div>
                                <dt class="text-xs font-semibold text-stone-500">Phone</dt>
                                <dd class="mt-1"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $business->phone) }}" class="font-semibold text-emerald-800 hover:underline">{{ $business->phone }}</a></dd>
                            </div>
                        @endif
                        @if ($business->email)
                            <div>
                                <dt class="text-xs font-semibold text-stone-500">Email</dt>
                                <dd class="mt-1 break-all"><a href="mailto:{{ $business->email }}" class="font-semibold text-emerald-800 hover:underline">{{ $business->email }}</a></dd>
                            </div>
                        @endif
                        @if ($business->website)
                            <div>
                                <dt class="text-xs font-semibold text-stone-500">Website</dt>
                                <dd class="mt-1 break-all text-stone-700">{{ $business->website }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-semibold text-stone-500">Location</dt>
                            <dd class="mt-1 font-medium text-stone-700">{{ $business->address ? $business->address.', ' : '' }}{{ $business->location->name }}, Osun</dd>
                        </div>
                    </dl>
                </section>

                @if ($business->hours->isNotEmpty())
                    <section class="rounded-2xl border border-stone-200 bg-white p-5">
                        <h2 class="font-extrabold">Opening hours</h2>
                        <dl class="mt-4 space-y-3 text-sm">
                            @foreach ($business->hours->sortBy('day_of_week') as $hour)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="{{ (int) $hour->day_of_week === now()->dayOfWeek ? 'font-bold text-emerald-800' : 'text-stone-600' }}">{{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][(int) $hour->day_of_week] ?? 'Day' }}</dt>
                                    <dd class="text-right {{ $hour->is_closed ? 'text-stone-400' : 'font-medium text-stone-800' }}">
                                        @if ($hour->is_closed)
                                            Closed
                                        @elseif ($hour->open_time && $hour->close_time)
                                            {{ \Illuminate\Support\Carbon::parse($hour->open_time)->format('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($hour->close_time)->format('g:i A') }}
                                        @else
                                            Hours not listed
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>
                @endif
            </aside>
        </div>
    </div>
@endsection
