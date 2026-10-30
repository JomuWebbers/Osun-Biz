<?php
// Owner Profile — Wireframe M
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));
$auth->requireLogin();
$auth->requireRole('owner');

$user = $auth->getCurrentUser();

$pageTitle = 'Owner Profile';
$currentPage = 'owner';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';

    if (empty($errors) && $user) {
        $userRepo = new \App\Repositories\UserRepository($app->db());
        $userRepo->update($user->id, ['name' => $name, 'email' => $email]);
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile updated.'];
        header('Location: /owner/profile');
        exit;
    }
}

require __DIR__ . '/partials/header.php';
?>

<section class="section form-section">
    <div class="form-card" style="max-width:500px;margin:0 auto;">
        <h1>Owner Profile</h1>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
        <form method="POST" action="/owner/profile">
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Full Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($user->name ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <p style="margin-bottom:1rem;"><strong>Role:</strong> <?= htmlspecialchars($user->role ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <p style="margin-bottom:1rem;"><strong>Status:</strong> <?= htmlspecialchars($user->status ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <p style="margin-bottom:1rem;"><strong>Email Verified:</strong> <?= $user->isEmailVerified() ? 'Yes' : 'No' ?></p>
            <button type="submit" class="btn btn-primary" style="width:100%;">Update Profile</button>
        </form>
        <p style="margin-top:1rem;text-align:center;font-size:0.9rem;"><a href="/owner/dashboard">Back to Dashboard</a></p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';