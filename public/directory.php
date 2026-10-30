<?php
// Business Directory — Wireframe B
require __DIR__ . '/../app/bootstrap.php';

$pageTitle = 'Business Directory';
$currentPage = 'directory';

// Sample businesses (placeholder until Phase 4 database)
$businesses = [
    ['id' => 1, 'name' => 'Osogbo Grill & Chill', 'category' => 'Restaurant', 'town' => 'Osogbo', 'rating' => 4.5, 'image' => '🏢'],
    ['id' => 2, 'name' => 'Ile-Ife Craft Hub', 'category' => 'Arts & Crafts', 'town' => 'Ile-Ife', 'rating' => 4.8, 'image' => '🏪'],
    ['id' => 3, 'name' => 'Ilesa Bookstore', 'category' => 'Books', 'town' => 'Ilesa', 'rating' => 4.2, 'image' => '📚'],
    ['id' => 4, 'name' => 'Ede Fashion House', 'category' => 'Fashion', 'town' => 'Ede', 'rating' => 4.6, 'image' => '👗'],
    ['id' => 5, 'name' => 'Iwo Tech Solutions', 'category' => 'Technology', 'town' => 'Iwo', 'rating' => 4.3, 'image' => '💻'],
    ['id' => 6, 'name' => 'Osogbo Pharmacy', 'category' => 'Health & Wellness', 'town' => 'Osogbo', 'rating' => 4.1, 'image' => '💊'],
    ['id' => 7, 'name' => 'Ile-Ife Cafe', 'category' => 'Food & Beverages', 'town' => 'Ile-Ife', 'rating' => 4.4, 'image' => '☕'],
    ['id' => 8, 'name' => 'Ilesa Legal Services', 'category' => 'Professional Services', 'town' => 'Ilesa', 'rating' => 4.7, 'image' => '🏛️'],
];

// Apply filters
$q = $_GET['q'] ?? '';
$location = $_GET['location'] ?? '';
$category = $_GET['category'] ?? '';

$filtered = array_filter($businesses, function ($b) use ($q, $location, $category) {
    $match = true;
    if ($q !== '' && stripos($b['name'], $q) === false) $match = false;
    if ($location !== '' && $b['town'] !== $location) $match = false;
    if ($category !== '' && $b['category'] !== $category) $match = false;
    return $match;
});

$filtered = array_values($filtered);

// Pagination (simple)
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 6;
$totalPages = max(1, ceil(count($filtered) / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;
$paged = array_slice($filtered, $offset, $perPage);

$categories = ['Restaurants', 'Fashion & Clothing', 'Technology', 'Arts & Crafts', 'Books & Stationery', 'Food & Beverages', 'Health & Wellness', 'Professional Services'];
$towns = ['Osogbo', 'Ile-Ife', 'Ilesa', 'Ede', 'Iwo'];

require __DIR__ . '/partials/header.php';
?>

<section class="section page-header">
    <h1>Business Directory</h1>
    <p><?= count($filtered) ?> result<?= count($filtered) !== 1 ? 's' : '' ?> found</p>
</section>

<form class="filter-form" action="/directory" method="GET">
    <div class="filter-row">
        <input type="text" name="q" placeholder="Search businesses…" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
        <select name="location">
            <option value="">All Towns</option>
            <?php foreach ($towns as $t): ?>
                <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>" <?= ($location === $t) ? 'selected' : '' ?>><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>" <?= ($category === $c) ? 'selected' : '' ?>><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort">
            <option value="rating" <?= (($_GET['sort'] ?? '') === 'rating') ? 'selected' : '' ?>>Top Rated</option>
            <option value="name" <?= (($_GET['sort'] ?? '') === 'name') ? 'selected' : '' ?>>Name (A-Z)</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<?php if (count($paged) === 0): ?>
    <div class="empty-state">
        <p>No businesses found matching your criteria.</p>
        <a href="/directory" class="btn btn-outline">Clear Filters</a>
    </div>
<?php else: ?>
    <div class="card-grid">
        <?php foreach ($paged as $biz): ?>
            <article class="card">
                <div class="card-thumb"><?= htmlspecialchars($biz['image'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="card-body">
                    <h3><a href="/details?id=<?= $biz['id'] ?>"><?= htmlspecialchars($biz['name'], ENT_QUOTES, 'UTF-8') ?></a></h3>
                    <p class="card-category"><?= htmlspecialchars($biz['category'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-location"><?= htmlspecialchars($biz['town'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="card-rating">★ <?= htmlspecialchars($biz['rating'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a href="/directory?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">← Previous</a>
            <?php endif; ?>
            <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="/directory?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next →</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php';