<form action="{{ route('directory.index') }}" method="GET" class="grid gap-3 rounded-2xl border border-stone-200 bg-white p-3 shadow-xl shadow-stone-950/5 sm:grid-cols-2 lg:grid-cols-[minmax(0,1.5fr)_minmax(10rem,1fr)_minmax(10rem,1fr)_auto] lg:items-end">
    <div>
        <label for="search-keyword" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-stone-500">What are you looking for?</label>
        <input id="search-keyword" name="q" value="{{ $filters['q'] ?? '' }}" type="search" maxlength="120" placeholder="Business, product, or service" class="w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
    </div>
    <div>
        <label for="search-town" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-stone-500">Town</label>
        <select id="search-town" name="town" class="w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
            <option value="">All towns</option>
            @foreach ($locations as $location)
                <option value="{{ $location->slug }}" @selected(($filters['town'] ?? '') === $location->slug)>{{ $location->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="search-category" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-stone-500">Category</label>
        <select id="search-category" name="category" class="w-full rounded-lg border-stone-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-emerald-800 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
        Search
    </button>
</form>
