<?php
// Homepage — Wireframe A
require __DIR__ . '/../app/bootstrap.php';

$pageTitle = 'Home';
$currentPage = 'home';

// Sample featured businesses (placeholder until Phase 4 database)
$featuredBusinesses = [
    ['name' => 'Osogbo Grill & Chill', 'category' => 'Restaurant', 'town' => 'Osogbo', 'rating' => 4.5],
    ['name' => 'Ile-Ife Craft Hub', 'category' => 'Arts & Crafts', 'town' => 'Ile-Ife', 'rating' => 4.8],
    ['name' => 'Ilesa Bookstore', 'category' => 'Books', 'town' => 'Ilesa', 'rating' => 4.2],
    ['name' => 'Ede Fashion House', 'category' => 'Fashion', 'town' => 'Ede', 'rating' => 4.6],
    ['name' => 'Iwo Tech Solutions', 'category' => 'Technology', 'town' => 'Iwo', 'rating' => 4.3],
];

$popularLocations = ['Osogbo', 'Ile-Ife', 'Ilesa', 'Ede', 'Iwo'];

$categories = [
    'Restaurants', 'Fashion & Clothing', 'Technology', 'Arts & Crafts',
    'Books & Stationery', 'Food & Beverages', 'Health & Wellness', 'Professional Services'
];

require __DIR__ . '/partials/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h1>Discover Local Businesses in Osun State</h1>
        <p>Find the best restaurants, shops, services, and artisans across Osogbo, Ile-Ife, Ilesa, Ede, Iwo, and beyond.</p>
        <form class="search-form" action="/directory" method="GET">
            <div class="search-row">
                <input type="text" name="q" placeholder="What are you looking for?" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" name="location" placeholder="Town or city" value="<?= htmlspecialchars($_GET['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>" <?= (($_GET['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
</section>

<section class="section">
    <h2>Popular Categories</h2>
    <div class="category-grid">
        <?php foreach ($categories as $cat): ?>
            <a href="/landing?category=<?= urlencode($cat) ?>" class="category-card">
                <span class="category-icon">📁</span>
                <span class="category-name"><?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h2>Featured Businesses</h2>
    <div class="card-grid">
        <?php foreach ($featuredBusinesses as $biz): ?>
            <article class="card">
                <div class="card-thumb">🏢</div>
                <div class="card-body">
                    <h3><?= htmlspecialchars($biz['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="card-category"><?= htmlspecialchars($biz['category'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-location"><?= htmlspecialchars($biz['town'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="card-rating">★ <?= htmlspecialchars($biz['rating'], ENT_QUOTES, 'UTF-8') ?></div>
                    <a href="/details?id=1" class="btn btn-outline">View Details</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h2>Popular Locations</h2>
    <div class="location-list">
        <?php foreach ($popularLocations as $town): ?>
            <a href="/landing?town=<?= urlencode($town) ?>" class="location-chip"><?= htmlspecialchars($town, ENT_QUOTES, 'UTF-8') ?></a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section cta">
    <div class="cta-box">
        <h2>Own a Business?</h2>
        <p>List your business on OsunBiz and reach customers across Osun State.</p>
        <a href="/owner/listing-form" class="btn btn-primary">Add Your Business</a>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';