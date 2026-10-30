<?php
// Contact Page — Wireframe N
require __DIR__ . '/../app/bootstrap.php';

$pageTitle = 'Contact';
$currentPage = 'contact';

require __DIR__ . '/partials/header.php';
?>

<section class="section">
    <h1>Contact Us</h1>
    <p>Have questions or need help listing your business? Reach out to us.</p>
    <div class="contact-card">
        <p><strong>Email:</strong> hello@osunbiz.example</p>
        <p><strong>Phone:</strong> +234 800 000 0000</p>
        <p><strong>Address:</strong> Osun State, Nigeria</p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php';