<?php
// Admin Reports — Wireframe J
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));
$auth->requireLogin();
$auth->requireRole('admin');

$pageTitle = 'Reports & Activity Log';
$currentPage = 'admin';

require __DIR__ . '/../partials/header.php';
?>

<section class="section">
    <h1>Reports & Activity Log</h1>
    <p>Moderation activity, reported reviews, and system logs.</p>

    <div class="empty-state">
        <p>No reports or activity logs at this time.</p>
        <a href="/admin/dashboard" class="btn btn-outline">Back to Dashboard</a>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php';