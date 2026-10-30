<?php
// Forgot Password — Wireframe E
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));

$pageTitle = 'Forgot Password';
$currentPage = 'forgot-password';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->requestPasswordReset($_POST['email'] ?? '');
    $message = $result['message'];
    if (!$result['ok']) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $result['error']];
        header('Location: /forgot-password');
        exit;
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="section form-section">
    <div class="form-card" style="max-width:400px;margin:0 auto;">
        <h1>Forgot Password</h1>
        <p>Enter your email and we'll send you a reset link.</p>
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="POST" action="/forgot-password">
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Email</label>
                <input type="email" name="email" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Send Reset Link</button>
        </form>
        <p style="margin-top:1rem;text-align:center;font-size:0.9rem;">Remember your password? <a href="/login">Sign In</a></p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';