@extends('layouts.public')

@section('title', 'Reviews for '.$business->name.' | OsunBiz')
@section('meta_description', 'Read approved customer reviews for '.$business->name.' in '.$business->location->name.'.')

@section('content')
    <section class="border-b border-stone-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-9 sm:px-6 sm:py-12 lg:px-8">
            <nav class="text-xs font-medium text-stone-500" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-emerald-800">Home</a><span class="px-2">/</span>
                <a href="{{ route('directory.show', $business->slug) }}" class="hover:text-emerald-800">{{ $business->name }}</a><span class="px-2">/</span>
                <span class="text-stone-800">Reviews</span>
            </nav>
            <div class="mt-5 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Customer feedback</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Reviews for {{ $business->name }}</h1>
                    <p class="mt-2 text-sm text-stone-600">{{ $business->location->name }} · {{ $business->category->name }}</p>
                </div>
                <div class="rounded-xl bg-amber-50 px-4 py-3 text-right">
                    <p class="text-2xl font-black text-stone-900"><span class="text-amber-500">★</span> {{ number_format((float) $business->rating, 1) }}</p>
                    <p class="mt-0.5 text-xs text-stone-600">{{ $business->reviews_count }} approved {{ \Illuminate\Support\Str::plural('review', $business->reviews_count) }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($reviews->isNotEmpty())
            <div class="divide-y divide-stone-200 rounded-2xl border border-stone-200 bg-white px-5 sm:px-7">
                @foreach ($reviews as $review)
                    <article class="py-6 first:pt-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-bold text-stone-900">{{ $review->user->name }}</h2>
                                <time class="mt-1 block text-xs text-stone-500" datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('j F Y') }}</time>
                            </div>
                            <span class="shrink-0 text-sm font-bold text-amber-600" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating) }}<span class="text-stone-300">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                        </div>
                        @if ($review->comment)
                            <p class="mt-4 text-sm leading-7 text-stone-600">{{ $review->comment }}</p>
                        @else
                            <p class="mt-4 text-sm italic text-stone-500">No written comment.</p>
                        @endif
                        @if ($review->owner_reply)
                            <div class="mt-4 rounded-xl bg-stone-50 p-4">
                                <p class="text-xs font-bold text-stone-800">Owner response</p>
                                <p class="mt-1 text-sm leading-6 text-stone-600">{{ $review->owner_reply }}</p>
                                @if ($review->owner_replied_at)
                                    <time class="mt-2 block text-xs text-stone-500" datetime="{{ $review->owner_replied_at->toDateString() }}">{{ $review->owner_replied_at->format('j M Y') }}</time>
                                @endif
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
            <div class="mt-6">{{ $reviews->links() }}</div>
        @else
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                <h2 class="font-bold">No approved reviews yet</h2>
                <p class="mt-2 text-sm text-stone-600">There are no public reviews for this business yet.</p>
            </div>
        @endif
        <a href="{{ route('directory.show', $business->slug) }}" class="mt-6 inline-flex text-sm font-bold text-emerald-800 hover:text-emerald-950">← Back to business</a>
    </section>
@endsection
