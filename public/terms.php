<?php
// Terms Page — Wireframe N
require __DIR__ . '/../app/bootstrap.php';

$pageTitle = 'Terms of Service';
$currentPage = 'terms';

require __DIR__ . '/partials/header.php';
?>

<section class="section">
    <h1>Terms of Service</h1>
    <p>Last updated: <?= date('F j, Y') ?></p>
    <p>By using OsunBiz, you agree to our terms and conditions.</p>
</section>

<?php require __DIR__ . '/partials/footer.php';