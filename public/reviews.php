<?php
// Reviews & Report — Wireframe K
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));

$pageTitle = 'Reviews';
$currentPage = 'reviews';

require __DIR__ . '/../partials/header.php';
?>

<section class="section form-section">
    <div class="form-card" style="max-width:500px;margin:0 auto;">
        <h1>Leave a Review</h1>
        <?php if (!$auth->isLoggedIn()): ?>
            <div class="alert alert-warning">Please <a href="/login">sign in</a> to leave a review.</div>
        <?php else: ?>
            <form method="POST" action="/reviews">
                <div style="margin-bottom:1rem;">
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Business ID</label>
                    <input type="text" name="business_id" required placeholder="e.g. 1" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Rating</label>
                    <select name="rating" required style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
                        <option value="5">5 stars</option>
                        <option value="4">4 stars</option>
                        <option value="3">3 stars</option>
                        <option value="2">2 stars</option>
                        <option value="1">1 star</option>
                    </select>
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Comment</label>
                    <textarea name="comment" rows="4" placeholder="Share your experience..." style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Submit Review</button>
            </form>
        <?php endif; ?>

        <h2 style="margin-top:2rem;">Report a Review</h2>
        <form method="POST" action="/reviews/report">
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Review ID</label>
                <input type="text" name="review_id" required placeholder="e.g. 42" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Reason</label>
                <textarea name="reason" rows="3" required placeholder="Why should this review be removed?" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;"></textarea>
            </div>
            <button type="submit" class="btn btn-outline" style="width:100%;">Submit Report</button>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php';