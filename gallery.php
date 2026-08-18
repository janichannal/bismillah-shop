<?php
$pageTitle = 'Gallery';
$pageDescription = 'Take a look inside Bismillah Mobile & Laptop Shop - our storefront, repair counter, and product displays in Khuzdar.';
require 'config/database.php';
require 'includes/functions.php';

$galleryItems = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll();

$allExtra = $pdo->query("SELECT * FROM gallery_images ORDER BY sort_order ASC, image_id ASC")->fetchAll();
$extraByGallery = [];
foreach ($allExtra as $img) {
    $extraByGallery[$img['gallery_id']][] = $img;
}

require 'includes/header.php';
?>

<section class="hero" style="padding: 60px 0;">
    <div class="container">
        <h1>Our Gallery</h1>
        <p>Take a look around our shop.</p>
    </div>
</section>

<?php echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
    ['label' => 'Gallery'],
]); ?>

<section class="section section-neutral">
    <div class="container">
        <?php if (count($galleryItems) === 0): ?>
            <p style="text-align:center; color:var(--text-muted);">No gallery photos yet.</p>
        <?php else: ?>
        <div class="grid">
            <?php foreach ($galleryItems as $item): ?>
            <div class="card">
                <div class="img-slider">
                    <div class="img-slider-track">
                        <img src="uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php if (isset($extraByGallery[$item['gallery_id']])): ?>
                            <?php foreach ($extraByGallery[$item['gallery_id']] as $img): ?>
                                <img src="uploads/gallery/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="img-slider-prev">&#8249;</button>
                    <button type="button" class="img-slider-next">&#8250;</button>
                    <div class="img-slider-dots"></div>
                </div>
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <?php if ($item['description']): ?>
                        <p style="color:var(--text-muted); font-size:14px;"><?php echo htmlspecialchars($item['description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>