<?php
$pageTitle = 'Services';
$pageDescription = 'Expert mobile and laptop repair services in Khuzdar - screen replacement, data recovery, software installation, and more.';
require 'config/database.php';
require 'includes/functions.php';

$services = $pdo->query("SELECT * FROM services ORDER BY service_name")->fetchAll();

$allExtra = $pdo->query("SELECT * FROM service_images ORDER BY sort_order ASC, image_id ASC")->fetchAll();
$extraByService = [];
foreach ($allExtra as $img) {
    $extraByService[$img['service_id']][] = $img;
}

require 'includes/header.php';
?>

<section class="hero" style="padding: 60px 0;">
    <div class="container">
        <h1>Our Services</h1>
        <p>Expert repair and technical services for phones and laptops.</p>
    </div>
</section>

<?php echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
    ['label' => 'Services'],
]); ?>

<section class="section section-mint">
    <div class="container">
        <div class="grid">
            <?php foreach ($services as $service): ?>
            <div class="card">
                <div class="img-slider">
                    <div class="img-slider-track">
                        <img src="uploads/services/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                        <?php if (isset($extraByService[$service['service_id']])): ?>
                            <?php foreach ($extraByService[$service['service_id']] as $img): ?>
                                <img src="uploads/services/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="img-slider-prev">&#8249;</button>
                    <button type="button" class="img-slider-next">&#8250;</button>
                    <div class="img-slider-dots"></div>
                </div>
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                    <p style="color: var(--text-muted); font-size: 14px;">
                        <?php echo htmlspecialchars(substr($service['description'], 0, 70)); ?>...
                    </p>
                    <div class="price">Starting Rs. <?php echo number_format($service['price']); ?></div>
                    <a href="service-details.php?id=<?php echo $service['service_id']; ?>" class="btn btn-outline">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>