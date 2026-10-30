<?php
// Admin Manage — Wireframe I
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));
$auth->requireLogin();
$auth->requireRole('admin');

$pageTitle = 'Manage Data';
$currentPage = 'admin';

require __DIR__ . '/../partials/header.php';
?>

<section class="section">
    <h1>Manage Data</h1>
    <p>CRUD for categories, towns, and users.</p>

    <div class="card-grid">
        <div class="card">
            <div class="card-body">
                <h3>Categories</h3>
                <p>Manage business categories.</p>
                <a href="/admin/manage?tab=categories" class="btn btn-primary">Manage Categories</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Towns</h3>
                <p>Manage Osun State locations.</p>
                <a href="/admin/manage?tab=locations" class="btn btn-primary">Manage Towns</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Users</h3>
                <p>Manage accounts and block users.</p>
                <a href="/admin/manage?tab=users" class="btn btn-primary">Manage Users</a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php';