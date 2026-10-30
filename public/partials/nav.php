<nav class="nav">
    <a href="/" <?= ($currentPage ?? '') === 'home' ? 'aria-current="page"' : '' ?>>Home</a>
    <a href="/directory" <?= ($currentPage ?? '') === 'directory' ? 'aria-current="page"' : '' ?>>Directory</a>
    <a href="/categories" <?= ($currentPage ?? '') === 'categories' ? 'aria-current="page"' : '' ?>>Categories</a>
    <a href="/locations" <?= ($currentPage ?? '') === 'locations' ? 'aria-current="page"' : '' ?>>Locations</a>
    <a href="/about" <?= ($currentPage ?? '') === 'about' ? 'aria-current="page"' : '' ?>>About</a>
    <a href="/contact" <?= ($currentPage ?? '') === 'contact' ? 'aria-current="page"' : '' ?>>Contact</a>
</nav>