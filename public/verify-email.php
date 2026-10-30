<?php
// Verify Email — Wireframe E
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));

$pageTitle = 'Verify Email';
$currentPage = 'verify-email';

$token = $_GET['token'] ?? '';
$result = $auth->verifyEmail($token);

require __DIR__ . '/partials/header.php';
?>

<section class="section form-section">
    <div class="form-card" style="max-width:400px;margin:0 auto;">
        <h1><?= $result['ok'] ? 'Email Verified' : 'Verification Failed' ?></h1>
        <div class="alert alert-<?= $result['ok'] ? 'success' : 'danger' ?>">
            <?= htmlspecialchars($result['message'] ?? $result['error'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php if ($result['ok']): ?>
            <a href="/login" class="btn btn-primary">Sign In</a>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';