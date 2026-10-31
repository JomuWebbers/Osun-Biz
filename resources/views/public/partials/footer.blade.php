<footer class="mt-20 bg-stone-950 text-stone-300">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
        <div class="md:col-span-2 lg:col-span-1">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl rounded-bl-sm bg-emerald-700 text-sm font-black">OB</span>
                <span class="text-lg font-extrabold">OsunBiz</span>
            </a>
            <p class="mt-4 max-w-xs text-sm leading-6 text-stone-400">Discover local businesses, shops, and services in towns across Osun State.</p>
        </div>
        <div>
            <h2 class="text-sm font-bold text-white">Explore</h2>
            <a href="{{ route('directory.index') }}" class="mt-4 block text-sm text-stone-400 transition hover:text-white">Browse the directory</a>
            <a href="{{ route('home') }}#categories" class="mt-3 block text-sm text-stone-400 transition hover:text-white">Browse categories</a>
        </div>
        <div>
            <h2 class="text-sm font-bold text-white">For businesses</h2>
            <a href="{{ route('register') }}" class="mt-4 block text-sm text-stone-400 transition hover:text-white">List your business</a>
            <a href="{{ route('login') }}" class="mt-3 block text-sm text-stone-400 transition hover:text-white">Owner sign in</a>
        </div>
        <div>
            <h2 class="text-sm font-bold text-white">Local discovery</h2>
            <p class="mt-4 text-sm leading-6 text-stone-400">Connecting customers with businesses throughout Osun State, Nigeria.</p>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-stone-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <span>&copy; {{ now()->year }} OsunBiz. All rights reserved.</span>
            <span>Osun State, Nigeria</span>
        </div>
    </div>
</footer>
