<?php
$pageTitle = 'Service Details';
require 'config/database.php';
require 'includes/functions.php';

$serviceId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$service = null;
$extraImages = [];
$relatedServices = [];
$extraByRelated = [];

if ($serviceId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch();

    if ($service) {
        $stmt = $pdo->prepare("SELECT * FROM service_images WHERE service_id = ? ORDER BY sort_order ASC, image_id ASC");
        $stmt->execute([$serviceId]);
        $extraImages = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id != ? ORDER BY RAND() LIMIT 4");
        $stmt->execute([$serviceId]);
        $relatedServices = $stmt->fetchAll();

        $relatedIds = array_column($relatedServices, 'service_id');
        if (count($relatedIds) > 0) {
            $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
            $stmt = $pdo->prepare("SELECT * FROM service_images WHERE service_id IN ($placeholders) ORDER BY sort_order ASC, image_id ASC");
            $stmt->execute($relatedIds);
            foreach ($stmt->fetchAll() as $img) {
                $extraByRelated[$img['service_id']][] = $img;
            }
        }
    }
}

if ($service) {
    $pageTitle = $service['service_name'];
    $pageDescription = $service['service_name'] . ' service starting from Rs. ' . number_format($service['price']) . ' at Bismillah Mobile & Laptop Shop, Khuzdar.';
}
require 'includes/header.php';
?>

<section class="section">
    <div class="container">

        <?php if (!$service): ?>
            <div style="text-align:center; padding: 60px 0;">
                <h2>Service Not Found</h2>
                <p style="color:var(--text-muted); margin: 12px 0 24px;">
                    The service you're looking for doesn't exist or may have been removed.
                </p>
                <a href="services.php" class="btn btn-primary">Back to Services</a>
            </div>
        <?php else: ?>

        <?php echo renderBreadcrumbs([
            ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
            ['label' => 'Services', 'url' => '/bismillah-shop/services.php'],
            ['label' => $service['service_name']],
        ]); ?>

               <div class="split-equal">

            <div class="img-slider">
                <div class="img-slider-track">
                    <img src="uploads/services/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                    <?php foreach ($extraImages as $img): ?>
                        <img src="uploads/services/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                    <?php endforeach; ?>
                </div>
                <button type="button" class="img-slider-prev">&#8249;</button>
                <button type="button" class="img-slider-next">&#8250;</button>
                <div class="img-slider-dots"></div>
            </div>

            <div>
                <span class="category-badge" style="background:var(--primary-light); color:var(--primary-dark);">Service</span>
                <h1 style="margin-top:10px;"><?php echo htmlspecialchars($service['service_name']); ?></h1>
                <div class="price" style="font-size: 28px; margin: 16px 0;">
                    Starting Rs. <?php echo number_format($service['price']); ?>
                </div>
                <p style="color:var(--text); margin-bottom: 24px;">
                    <?php echo nl2br(htmlspecialchars($service['description'])); ?>
                </p>

                               <div class="cta-button-group">
                <a href="contact.php?subject=<?php echo urlencode('Service Booking: ' . $service['service_name']); ?>&message=<?php echo urlencode('Hi, I would like to book the ' . $service['service_name'] . ' service (Starting Rs. ' . number_format($service['price']) . '). Please let me know the next steps.'); ?>" class="btn btn-primary">Book This Service</a>
                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode('Hi, I would like to book the ' . $service['service_name'] . ' service (Starting Rs. ' . number_format($service['price']) . '). Please let me know the next steps.'); ?>" target="_blank" rel="noopener" class="btn" style="background:#25D366; color:#fff;">Chat on WhatsApp</a>
                <a href="services.php" class="btn btn-outline">Back to Services</a>
                </div>
            </div>
        </div>

        <?php if (count($relatedServices) > 0): ?>
        <div style="margin-top: 70px;">
            <div class="section-title">
                <h2>Other Services You May Need</h2>
            </div>
            <div class="grid">
                <?php foreach ($relatedServices as $rs): ?>
                <div class="card">
                    <div class="img-slider">
                        <div class="img-slider-track">
                            <img src="uploads/services/<?php echo htmlspecialchars($rs['image']); ?>" alt="<?php echo htmlspecialchars($rs['service_name']); ?>">
                            <?php if (isset($extraByRelated[$rs['service_id']])): ?>
                                <?php foreach ($extraByRelated[$rs['service_id']] as $img): ?>
                                    <img src="uploads/services/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($rs['service_name']); ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="img-slider-prev">&#8249;</button>
                        <button type="button" class="img-slider-next">&#8250;</button>
                        <div class="img-slider-dots"></div>
                    </div>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($rs['service_name']); ?></h3>
                        <p style="color: var(--text-muted); font-size: 14px;">
                            <?php echo htmlspecialchars(substr($rs['description'], 0, 60)); ?>...
                        </p>
                        <div class="price">Starting Rs. <?php echo number_format($rs['price']); ?></div>
                        <a href="service-details.php?id=<?php echo $rs['service_id']; ?>" class="btn btn-outline">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<?php require 'includes/footer.php'; ?>