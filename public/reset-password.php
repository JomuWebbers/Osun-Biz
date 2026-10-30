<?php
// Reset Password — Wireframe Q
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;
use App\Validation;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));

$pageTitle = 'Reset Password';
$currentPage = 'reset-password';

$token = $_GET['token'] ?? '';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validation();
    $v->required('password', $_POST['password'] ?? '');
    $v->minLength('password', $_POST['password'] ?? '', 8);
    $v->passwordMatch('password', 'confirm_password', $_POST['password'] ?? '', $_POST['confirm_password'] ?? '');

    if (!$v->hasErrors()) {
        $result = $auth->resetPassword($_POST['token'] ?? $token, $_POST['password']);
        if ($result['ok']) {
            $success = true;
        } else {
            $errors[] = $result['error'];
        }
    } else {
        foreach ($v->getErrors() as $field => $msgs) {
            $errors[] = $msgs[0] ?? '';
        }
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="section form-section">
    <div class="form-card" style="max-width:400px;margin:0 auto;">
        <h1><?= $success ? 'Password Reset' : 'Reset Password' ?></h1>
        <?php if ($success): ?>
            <div class="alert alert-success">Your password has been reset successfully. <a href="/login">Sign In</a></div>
        <?php else: ?>
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endforeach; ?>
            <form method="POST" action="/reset-password">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                <div style="margin-bottom:1rem;">
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;">New Password</label>
                    <input type="password" name="password" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Confirm Password</label>
                    <input type="password" name="confirm_password" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';