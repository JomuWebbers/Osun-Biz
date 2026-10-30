<?php
// Login — Wireframe E
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;
use App\Validation;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));

$pageTitle = 'Sign In';
$currentPage = 'login';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validation();
    $v->required('email', $_POST['email'] ?? '');
    $v->email('email', $_POST['email'] ?? '');
    $v->required('password', $_POST['password'] ?? '');

    if (!$v->hasErrors()) {
        $result = $auth->login($_POST['email'], $_POST['password']);
        if ($result['ok']) {
            $role = $_SESSION['user_role'] ?? 'customer';
            header('Location: ' . ($role === 'admin' ? '/admin' : ($role === 'owner' ? '/owner' : '/')));
            exit;
        }
        $errors[] = $result['error'];
    } else {
        $errors = array_merge($errors, array_values(array_filter(array_map(fn($e) => $e[0] ?? null, $v->getErrors()))));
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="section form-section">
    <div class="form-card" style="max-width:400px;margin:0 auto;">
        <h1>Sign In</h1>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
        <form method="POST" action="/login">
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Email</label>
                <input type="email" name="email" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Password</label>
                <input type="password" name="password" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <p style="text-align:right;margin-bottom:1rem;"><a href="/forgot-password" style="font-size:0.9rem;">Forgot password?</a></p>
            <button type="submit" class="btn btn-primary" style="width:100%;">Sign In</button>
        </form>
        <p style="margin-top:1rem;text-align:center;font-size:0.9rem;">Don't have an account? <a href="/register">Register</a></p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';