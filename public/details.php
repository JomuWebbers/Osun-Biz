<?php
// Business Details — Wireframe C
require __DIR__ . '/../app/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);

// Sample business (placeholder until Phase 4 database)
$business = [
    'id' => 1,
    'name' => 'Osogbo Grill & Chill',
    'category' => 'Restaurant',
    'town' => 'Osogbo',
    'address' => '123 Alaafin Road, Osogbo, Osun State',
    'phone' => '+234 803 123 4567',
    'email' => 'info@osobogrill.example',
    'website' => 'https://osobogrill.example',
    'description' => 'Serving the best local and continental cuisine in Osogbo since 2015. Family-friendly restaurant with a cozy atmosphere.',
    'services' => ['Dine-in', 'Takeaway', 'Delivery', 'Catering', 'Outdoor Seating'],
    'hours' => [
        'Monday' => '10:00 AM – 10:00 PM',
        'Tuesday' => '10:00 AM – 10:00 PM',
        'Wednesday' => '10:00 AM – 10:00 PM',
        'Thursday' => '10:00 AM – 10:00 PM',
        'Friday' => '10:00 AM – 11:00 PM',
        'Saturday' => '9:00 AM – 11:00 PM',
        'Sunday' => '10:00 AM – 8:00 PM',
    ],
    'rating' => 4.5,
    'reviewCount' => 128,
    'image' => '🏢',
];

if ($id !== 1) {
    header('Location: /404');
    exit;
}

$pageTitle = htmlspecialchars($business['name'], ENT_QUOTES, 'UTF-8');
$currentPage = 'details';

require __DIR__ . '/partials/header.php';
?>

<article class="business-details">
    <div class="business-header">
        <div class="business-badge"><?= htmlspecialchars($business['image'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="business-info">
            <h1><?= htmlspecialchars($business['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="business-meta">
                <span class="business-category"><?= htmlspecialchars($business['category'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="business-location"><?= htmlspecialchars($business['town'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="business-rating">★ <?= htmlspecialchars($business['rating'], ENT_QUOTES, 'UTF-8') ?> (<?= $business['reviewCount'] ?> reviews)</span>
            </div>
        </div>
    </div>

    <div class="business-body">
        <section class="business-description">
            <h2>About</h2>
            <p><?= htmlspecialchars($business['description'], ENT_QUOTES, 'UTF-8') ?></p>
        </section>

        <section class="business-services">
            <h2>Services</h2>
            <ul class="service-list">
                <?php foreach ($business['services'] as $service): ?>
                    <li><?= htmlspecialchars($service, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="business-contact">
            <h2>Contact</h2>
            <p><strong>Address:</strong> <?= htmlspecialchars($business['address'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Phone:</strong> <a href="tel:<?= htmlspecialchars($business['phone'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($business['phone'], ENT_QUOTES, 'UTF-8') ?></a></p>
            <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($business['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($business['email'], ENT_QUOTES, 'UTF-8') ?></a></p>
            <p><strong>Website:</strong> <a href="<?= htmlspecialchars($business['website'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars($business['website'], ENT_QUOTES, 'UTF-8') ?></a></p>
            <div class="contact-actions">
                <a href="tel:<?= htmlspecialchars($business['phone'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline">Call</a>
                <a href="https://wa.me/<?= preg_replace('/\D/', '', $business['phone']) ?>" class="btn btn-outline" target="_blank" rel="noopener">WhatsApp</a>
            </div>
        </section>

        <section class="business-hours">
            <h2>Opening Hours</h2>
            <table class="hours-table">
                <?php foreach ($business['hours'] as $day => $time): ?>
                    <tr>
                        <th><?= htmlspecialchars($day, ENT_QUOTES, 'UTF-8') ?></th>
                        <td><?= htmlspecialchars($time, ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <aside class="business-sidebar">
            <div class="card">
                <h3>Location</h3>
                <p>📍 Map placeholder — interactive map will be added in Phase 4.</p>
            </div>
            <div class="card">
                <h3>Similar Businesses</h3>
                <ul class="similar-list">
                    <li><a href="/details?id=2">Ile-Ife Craft Hub</a> — Arts & Crafts</li>
                    <li><a href="/details?id=4">Ede Fashion House</a> — Fashion</li>
                    <li><a href="/details?id=6">Osogbo Pharmacy</a> — Health & Wellness</li>
                </ul>
            </div>
        </aside>
    </div>
</article>

<?php require __DIR__ . '/partials/footer.php';