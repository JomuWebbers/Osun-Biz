<?php
// Add/Edit Business — Wireframe G
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;
use App\Repositories\BusinessRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\LocationRepository;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));
$auth->requireLogin();
$auth->requireRole('owner');

$bizRepo = new BusinessRepository($app->db());
$catRepo = new CategoryRepository($app->db());
$locRepo = new \App\Repositories\LocationRepository($app->db());

$categories = $catRepo->findAll();
$locations = $locRepo->findAll();

$pageTitle = 'Add Business';
$currentPage = 'owner';

$businessId = $_GET['id'] ?? null;
$business = null;
if ($businessId) {
    $business = $bizRepo->findById((int) $businessId);
    if ($business === null || $business->ownerId !== (int) $_SESSION['user_id']) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Business not found or you do not own it.'];
        header('Location: /owner/dashboard');
        exit;
    }
    $pageTitle = 'Edit Business';
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $locationId = (int) ($_POST['location_id'] ?? 0);
    $description = $_POST['description'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $website = $_POST['website'] ?? '';
    $status = $business ? ($_POST['status'] ?? $business->status) : 'pending';

    if ($name === '') $errors[] = 'Business name is required.';
    if ($categoryId === 0) $errors[] = 'Please select a category.';
    if ($locationId === 0) $errors[] = 'Please select a location.';

    if (empty($errors)) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($name)));
        $slug = trim($slug, '-');

        if ($business) {
            $bizRepo->update($business->id, [
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'website' => $website,
                'status' => $status,
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Business updated successfully.'];
            header('Location: /owner/dashboard');
            exit;
        } else {
            $newId = $bizRepo->create([
                'owner_id' => (int) $_SESSION['user_id'],
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'website' => $website,
                'status' => 'pending',
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Business submitted for review.'];
            header('Location: /owner/dashboard');
            exit;
        }
    }
}

require __DIR__ . '/../partials/header.php';
?>

<section class="section form-section">
    <div class="form-card" style="max-width:600px;margin:0 auto;">
        <h1><?= $pageTitle ?></h1>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
        <form method="POST" action="/owner/listing-form<?= $businessId ? '?id=' . $businessId : '' ?>">
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Business Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($business->name ?? $_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Category</label>
                <select name="category_id" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat->id ?>" <?= (($business ? $business->categoryId : ($_POST['category_id'] ?? '')) == $cat->id) ? 'selected' : '' ?>><?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Town / Location</label>
                <select name="location_id" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
                    <option value="">Select a town</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc->id ?>" <?= (($business ? $business->locationId : ($_POST['location_id'] ?? '')) == $loc->id) ? 'selected' : '' ?>><?= htmlspecialchars($loc->name, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Description</label>
                <textarea name="description" rows="4" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;"><?= htmlspecialchars($business->description ?? $_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($business->address ?? $_POST['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($business->phone ?? $_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($business->email ?? $_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Website</label>
                <input type="url" name="website" value="<?= htmlspecialchars($business->website ?? $_POST['website'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <?php if ($business): ?>
                <div style="margin-bottom:1rem;">
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Status</label>
                    <select name="status" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
                        <option value="pending" <?= ($business->status === 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= ($business->status === 'approved') ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= ($business->status === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                        <option value="suspended" <?= ($business->status === 'suspended') ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary" style="width:100%;"><?= $business ? 'Update Business' : 'Submit for Review' ?></button>
        </form>
        <?php if ($business): ?>
            <p style="margin-top:1rem;text-align:center;"><a href="/owner/dashboard" class="btn btn-outline">Cancel</a></p>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php';