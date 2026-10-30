<?php
// Category + Town Landing Page — Wireframe O
require __DIR__ . '/../app/bootstrap.php';

$category = $_GET['category'] ?? '';
$town = $_GET['town'] ?? '';

if ($category === '' && $town === '') {
    header('Location: /');
    exit;
}

$pageTitle = ucfirst($category) . ($town ? ' in ' . ucfirst($town) : '');
$currentPage = 'landing';

require __DIR__ . '/partials/header.php';
?>

<section class="section page-header">
    <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Discover top-rated <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?><?= $town ? ' in ' . htmlspecialchars($town, ENT_QUOTES, 'UTF-8') : '' ?> businesses.</p>
</section>

<section class="section">
    <div class="card-grid">
        <article class="card">
            <div class="card-thumb">🏢</div>
            <div class="card-body">
                <h3><a href="/details?id=1">Sample Business</a></h3>
                <p class="card-category"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></p>
                <p class="card-location"><?= htmlspecialchars($town ?: 'Osun State', ENT_QUOTES, 'UTF-8') ?></p>
                <div class="card-rating">★ 4.5</div>
            </div>
        </article>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';