<?php
// 404 System State — Wireframe L
require __DIR__ . '/../app/bootstrap.php';

$pageTitle = 'Page Not Found';
$currentPage = '404';
http_response_code(404);

require __DIR__ . '/partials/header.php';
?>

<section class="section system-state">
    <div class="state-card">
        <div class="state-code">404</div>
        <h1>Page Not Found</h1>
        <p>Sorry, we couldn't find the page you're looking for.</p>
        <a href="/" class="btn btn-primary">Go Home</a>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';