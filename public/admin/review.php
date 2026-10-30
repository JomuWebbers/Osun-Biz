<?php
// Admin Review Submission — Wireframe H
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));
$auth->requireLogin();
$auth->requireRole('admin');

$pageTitle = 'Review Submissions';
$currentPage = 'admin';

require __DIR__ . '/../partials/header.php';
?>

<section class="section">
    <h1>Review Submissions</h1>
    <p>Review pending business listings and approve or reject them.</p>

    <div class="empty-state">
        <p>No pending submissions at this time.</p>
        <a href="/admin/dashboard" class="btn btn-outline">Back to Dashboard</a>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php';