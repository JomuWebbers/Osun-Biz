<?php
// Register — Wireframe E
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;
use App\Validation;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));

$pageTitle = 'Register';
$currentPage = 'register';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validation();
    $v->required('name', $_POST['name'] ?? '');
    $v->minLength('name', $_POST['name'] ?? '', 2);
    $v->required('email', $_POST['email'] ?? '');
    $v->email('email', $_POST['email'] ?? '');
    $v->required('password', $_POST['password'] ?? '');
    $v->minLength('password', $_POST['password'] ?? '', 8);
    $v->passwordMatch('password', 'confirm_password', $_POST['password'] ?? '', $_POST['confirm_password'] ?? '');

    if (!$v->hasErrors()) {
        $result = $auth->register($_POST['name'], $_POST['email'], $_POST['password']);
        if ($result['ok']) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account created! Please check your email to verify.'];
            header('Location: /login');
            exit;
        }
        $errors[] = $result['error'];
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
        <h1>Create Account</h1>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
        <form method="POST" action="/register">
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Full Name</label>
                <input type="text" name="name" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;" value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Email</label>
                <input type="email" name="email" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Password</label>
                <input type="password" name="password" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Confirm Password</label>
                <input type="password" name="confirm_password" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Register</button>
        </form>
        <p style="margin-top:1rem;text-align:center;font-size:0.9rem;">Already have an account? <a href="/login">Sign In</a></p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';