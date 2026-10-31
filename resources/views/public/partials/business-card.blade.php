<article class="group overflow-hidden rounded-2xl border border-stone-200 bg-white transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-lg hover:shadow-stone-900/5">
    <a href="{{ route('directory.show', $business->slug) }}" class="block">
        <div class="flex h-32 items-end bg-gradient-to-br from-emerald-100 via-amber-50 to-stone-100 p-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-lg font-extrabold text-emerald-900 shadow-sm" aria-hidden="true">{{ mb_strtoupper(mb_substr($business->name, 0, 1)) }}</span>
        </div>
        <div class="p-5">
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">{{ $business->category->name }}</span>
                <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600">{{ $business->location->name }}</span>
            </div>
            <h3 class="mt-3 line-clamp-1 text-base font-bold tracking-tight text-stone-900 group-hover:text-emerald-800">{{ $business->name }}</h3>
            <p class="mt-2 line-clamp-2 min-h-10 text-sm leading-5 text-stone-600">{{ $business->description ?: 'Contact this business to learn more about its products and services.' }}</p>
            <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-3">
                <span class="flex items-center gap-1.5 text-sm font-bold text-stone-800">
                    <span class="text-amber-500" aria-hidden="true">★</span>
                    {{ number_format((float) $business->rating, 1) }}
                    <span class="font-normal text-stone-500">({{ $business->reviews_count }} {{ \Illuminate\Support\Str::plural('review', $business->reviews_count) }})</span>
                </span>
                <span class="text-xs font-bold text-emerald-800">View details <span aria-hidden="true">→</span></span>
            </div>
        </div>
    </a>
</article>
