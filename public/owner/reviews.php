<?php
// Owner Reviews — Wireframe P
require __DIR__ . '/../app/bootstrap.php';

use App\Auth;
use App\Repositories\ReviewRepository;

$auth = new Auth(new \App\Repositories\UserRepository($app->db()));
$auth->requireLogin();
$auth->requireRole('owner');

$reviewRepo = new ReviewRepository($app->db());

$pageTitle = 'Owner Reviews';
$currentPage = 'owner';

require __DIR__ . '/partials/header.php';
?>

<section class="section">
    <h1>Owner Reviews</h1>
    <p>View and reply to reviews for your businesses.</p>

    <h2>Received Reviews</h2>
    <div class="card-grid">
        <?php
        // Placeholder: In a full implementation, filter reviews by businesses owned by this user
        echo '<div class="empty-state"><p>No reviews yet for your businesses.</p></div>';
        ?>
    </div>

    <h2 style="margin-top:2rem;">Reply to a Review</h2>
    <div class="form-card" style="max-width:500px;">
        <form method="POST" action="/owner/reviews">
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Review ID</label>
                <input type="text" name="review_id" placeholder="e.g. 123" style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Your Reply</label>
                <textarea name="reply" rows="4" placeholder="Thank you for your feedback..." style="width:100%;padding:0.7rem;border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:1rem;"></textarea>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block;margin-bottom:0.4rem;font-weight:600;">Report Abusive?</label>
                <input type="checkbox" name="report" value="1"> <span style="font-size:0.9rem;color:var(--color-text-muted);">Mark this review as abusive for moderation</span>
            </div>
            <button type="submit" class="btn btn-primary">Submit Reply</button>
        </form>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';