<?php
// Privacy Page — Wireframe N
require __DIR__ . '/../app/bootstrap.php';

$pageTitle = 'Privacy Policy';
$currentPage = 'privacy';

require __DIR__ . '/partials/header.php';
?>

<section class="section">
    <h1>Privacy Policy</h1>
    <p>Last updated: <?= date('F j, Y') ?></p>
    <p>We respect your privacy and protect your personal data.</p>
</section>

<?php require __DIR__ . '/partials/footer.php';