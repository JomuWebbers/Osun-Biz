<?php
// Admin Dashboard — Wireframe D
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));
$auth->requireLogin();
$auth->requireRole('admin');

$pageTitle = 'Admin Dashboard';
$currentPage = 'admin';

require __DIR__ . '/../partials/header.php';
?>

<section class="section">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>. Manage the OsunBiz directory.</p>

    <div class="card-grid" style="max-width:800px;">
        <div class="card">
            <div class="card-thumb">📊</div>
            <div class="card-body">
                <h3>—</h3>
                <p class="card-category">Total Businesses</p>
            </div>
        </div>
        <div class="card">
            <div class="card-thumb">⏳</div>
            <div class="card-body">
                <h3>—</h3>
                <p class="card-category">Pending Submissions</p>
            </div>
        </div>
        <div class="card">
            <div class="card-thumb">📝</div>
            <div class="card-body">
                <h3>—</h3>
                <p class="card-category">Pending Reviews</p>
            </div>
        </div>
        <div class="card">
            <div class="card-thumb">👥</div>
            <div class="card-body">
                <h3>—</h3>
                <p class="card-category">Total Users</p>
            </div>
        </div>
    </div>

    <h2 style="margin-top:2rem;">Quick Actions</h2>
    <div class="card-grid">
        <div class="card">
            <div class="card-body">
                <h3>Review Submissions</h3>
                <p>Approve or reject pending business listings.</p>
                <a href="/admin/review" class="btn btn-primary">Review Submissions</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Manage Data</h3>
                <p>Categories, towns, and users.</p>
                <a href="/admin/manage" class="btn btn-primary">Manage</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Reports & Logs</h3>
                <p>Moderation activity and reported reviews.</p>
                <a href="/admin/reports" class="btn btn-primary">View Reports</a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php';