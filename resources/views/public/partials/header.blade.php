<header class="sticky top-0 z-30 border-b border-stone-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center gap-6 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" aria-label="OsunBiz home">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl rounded-bl-sm bg-emerald-800 text-sm font-black text-white">OB</span>
            <span>
                <span class="block text-lg font-extrabold leading-tight tracking-tight">OsunBiz</span>
                <span class="block text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Business directory</span>
            </span>
        </a>

        <nav class="hidden items-center gap-6 text-sm font-medium text-stone-600 md:flex" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-emerald-800' : 'hover:text-emerald-800' }}">Home</a>
            <a href="{{ route('directory.index') }}" class="{{ request()->routeIs('directory.*') ? 'text-emerald-800' : 'hover:text-emerald-800' }}">Directory</a>
            <a href="{{ route('home') }}#categories" class="hover:text-emerald-800">Categories</a>
        </nav>

        <div class="ml-auto hidden items-center gap-3 md:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-stone-700 hover:text-emerald-800">My account</a>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100">Log in</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-900">List your business</a>
            @endauth
        </div>

        <details class="group relative ml-auto md:hidden">
            <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg border border-stone-200 text-stone-700 hover:bg-stone-50" aria-label="Open navigation menu">
                <svg class="h-5 w-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg class="hidden h-5 w-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </summary>
            <nav class="absolute right-0 top-12 z-40 w-56 rounded-xl border border-stone-200 bg-white p-2 shadow-xl" aria-label="Mobile navigation">
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-stone-50">Home</a>
                <a href="{{ route('directory.index') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-stone-50">Browse businesses</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-stone-50">My account</a>
                @else
                    <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-stone-50">Log in</a>
                    <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-emerald-800 hover:bg-emerald-50">List your business</a>
                @endauth
            </nav>
        </details>
    </div>
</header>
