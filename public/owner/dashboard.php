<?php
// Owner Dashboard — Wireframe F
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;
use App\Repositories\BusinessRepository;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));
$auth->requireLogin();
$auth->requireRole('owner');

$bizRepo = new BusinessRepository($app->db());
$myListings = $bizRepo->findByOwner((int) $_SESSION['user_id']);

$pageTitle = 'Owner Dashboard';
$currentPage = 'owner';

require __DIR__ . '/partials/header.php';
?>

<section class="section">
    <h1>Owner Dashboard</h1>
    <p>Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>. Manage your business listings here.</p>

    <div class="card-grid" style="max-width:800px;">
        <div class="card">
            <div class="card-thumb">📊</div>
            <div class="card-body">
                <h3><?= count($myListings) ?></h3>
                <p class="card-category">Total Listings</p>
                <a href="/owner/listing-form" class="btn btn-outline">Add New Business</a>
            </div>
        </div>
        <?php $approved = count(array_filter($myListings, fn($b) => $b->isApproved())); ?>
        <div class="card">
            <div class="card-thumb">✅</div>
            <div class="card-body">
                <h3><?= $approved ?></h3>
                <p class="card-category">Approved</p>
                <a href="/directory" class="btn btn-outline">View Directory</a>
            </div>
        </div>
        <?php $pending = count(array_filter($myListings, fn($b) => $b->isPending())); ?>
        <div class="card">
            <div class="card-thumb">⏳</div>
            <div class="card-body">
                <h3><?= $pending ?></h3>
                <p class="card-category">Pending Review</p>
                <a href="/owner/dashboard" class="btn btn-outline">Refresh</a>
            </div>
        </div>
    </div>

    <h2 style="margin-top:2rem;margin-bottom:1rem;">My Listings</h2>
    <?php if (count($myListings) === 0): ?>
        <div class="empty-state">
            <p>You haven't added any businesses yet.</p>
            <a href="/owner/listing-form" class="btn btn-primary">Add Your First Business</a>
        </div>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($myListings as $biz): ?>
                <article class="card">
                    <div class="card-thumb">🏢</div>
                    <div class="card-body">
                        <h3><?= htmlspecialchars($biz->name, ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="card-category"><?= htmlspecialchars($biz->status, ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="card-location"><?= htmlspecialchars($biz->name, ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="card-rating">★ <?= htmlspecialchars($biz->rating, ENT_QUOTES, 'UTF-8') ?></div>
                        <a href="/details?id=<?= $biz->id ?>" class="btn btn-outline" style="margin-right:0.5rem;">View</a>
                        <a href="/owner/listing-form?id=<?= $biz->id ?>" class="btn btn-outline">Edit</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/partials/footer.php';