<?php
$pageTitle = 'Home';
$pageDescription = 'Genuine mobile phones, laptops, tablets, and expert repair services in Khuzdar, Balochistan. Visit Bismillah Mobile & Laptop Shop today.';
require 'config/database.php';
require 'includes/functions.php';

// Featured products
$stmt = $pdo->query("SELECT p.*, c.category_name FROM products p 
                      JOIN categories c ON p.category_id = c.category_id 
                      ORDER BY p.created_at DESC LIMIT 4");
$featuredProducts = $stmt->fetchAll();

$featuredIds = array_column($featuredProducts, 'product_id');
$extraByProduct = [];
if (count($featuredIds) > 0) {
    $placeholders = implode(',', array_fill(0, count($featuredIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id IN ($placeholders) ORDER BY sort_order ASC, image_id ASC");
    $stmt->execute($featuredIds);
    foreach ($stmt->fetchAll() as $img) {
        $extraByProduct[$img['product_id']][] = $img;
    }
}

// Sale products
$saleProducts = $pdo->query("SELECT p.*, c.category_name FROM products p 
                              JOIN categories c ON p.category_id = c.category_id 
                              WHERE p.sale_price IS NOT NULL AND p.sale_price > 0 AND p.sale_price < p.price 
                              ORDER BY p.updated_at DESC LIMIT 4")->fetchAll();

$saleIds = array_column($saleProducts, 'product_id');
$extraBySale = [];
if (count($saleIds) > 0) {
    $placeholders = implode(',', array_fill(0, count($saleIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id IN ($placeholders) ORDER BY sort_order ASC, image_id ASC");
    $stmt->execute($saleIds);
    foreach ($stmt->fetchAll() as $img) {
        $extraBySale[$img['product_id']][] = $img;
    }
}

// Services
$stmt = $pdo->query("SELECT * FROM services ORDER BY created_at DESC LIMIT 3");
$services = $stmt->fetchAll();

$serviceIds = array_column($services, 'service_id');
$extraByService = [];
if (count($serviceIds) > 0) {
    $placeholders = implode(',', array_fill(0, count($serviceIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM service_images WHERE service_id IN ($placeholders) ORDER BY sort_order ASC, image_id ASC");
    $stmt->execute($serviceIds);
    foreach ($stmt->fetchAll() as $img) {
        $extraByService[$img['service_id']][] = $img;
    }
}

// Gallery preview (Our Shop section)
$stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 3");
$galleryImages = $stmt->fetchAll();

$galleryPreviewIds = array_column($galleryImages, 'gallery_id');
$extraByGalleryPreview = [];
if (count($galleryPreviewIds) > 0) {
    $placeholders = implode(',', array_fill(0, count($galleryPreviewIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE gallery_id IN ($placeholders) ORDER BY sort_order ASC, image_id ASC");
    $stmt->execute($galleryPreviewIds);
    foreach ($stmt->fetchAll() as $img) {
        $extraByGalleryPreview[$img['gallery_id']][] = $img;
    }
}

// Intro section auto-sliding photo (last 5 gallery uploads)
$introImages = $pdo->query("SELECT image FROM gallery ORDER BY created_at DESC LIMIT 5")->fetchAll();

require 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <span class="hero-eyebrow">Welcome to Bismillah Mobile & Laptop Shop</span>
        <h1>Your Trusted Technology Partner</h1>
        <p>Quality mobile phones, laptops, and expert repair services — right here in Khuzdar.</p>
        <a href="products.php" class="btn btn-primary">Explore Products</a>
        <a href="contact.php" class="btn btn-outline">Contact Us</a>

        <div class="hero-trust-strip">
            <div class="hero-trust-item"><span class="check">&#10003;</span> Genuine products</div>
            <div class="hero-trust-item"><span class="check">&#10003;</span> Expert repairs</div>
            <div class="hero-trust-item"><span class="check">&#10003;</span> Trusted in Khuzdar</div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="split-intro">
            <div>
                <span class="category-badge" style="background:var(--primary-light); color:var(--primary-dark);">Who we are</span>
                <h2 style="margin:14px 0 16px; font-size:30px;">Genuine tech, honest repairs, real people</h2>
                <p style="color:var(--text-muted); font-size:16px; line-height:1.75;">
                    Bismillah Mobile & Laptop Shop is Khuzdar's go-to spot for genuine phones, laptops, and
                    accessories — backed by a repair team that actually knows what they're doing. Whether you're
                    buying your first smartphone or need a cracked screen fixed the same day, we treat every
                    customer like a neighbor, not a number.
                </p>
                <a href="about.php" class="btn btn-outline" style="margin-top:20px;">Read Our Story</a>
            </div>

            <?php if (count($introImages) > 0): ?>
            <div class="img-slider" data-autoplay="4000" style="box-shadow: var(--shadow-hover); aspect-ratio: 4/3;">
                <div class="img-slider-track">
                    <?php foreach ($introImages as $img): ?>
                        <img src="uploads/gallery/<?php echo htmlspecialchars($img['image']); ?>" style="aspect-ratio: 4/3; object-fit:cover;">
                    <?php endforeach; ?>
                </div>
                <button type="button" class="img-slider-prev">&#8249;</button>
                <button type="button" class="img-slider-next">&#8250;</button>
                <div class="img-slider-dots"></div>
            </div>
            <?php else: ?>
            <div style="width:100%; aspect-ratio: 4/3; background:var(--bg-light); display:flex; align-items:center; justify-content:center; color:var(--text-muted); border-radius:var(--radius);">Shop photo coming soon</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section section-cream">
    <div class="container">
        <div class="section-title">
            <h2>Featured Products</h2>
            <p>A few of our popular items</p>
        </div>
        <div class="grid">
            <?php foreach ($featuredProducts as $product): $catColor = categoryBadgeColors($product['category_name']); ?>
            <div class="card">
                <div class="img-slider">
                    <div class="img-slider-track">
                        <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <?php if (isset($extraByProduct[$product['product_id']])): ?>
                            <?php foreach ($extraByProduct[$product['product_id']] as $img): ?>
                                <img src="uploads/products/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="img-slider-prev">&#8249;</button>
                    <button type="button" class="img-slider-next">&#8250;</button>
                    <div class="img-slider-dots"></div>
                </div>
                <div class="card-body">
                    <span class="category-badge" style="background:<?php echo $catColor['bg']; ?>; color:<?php echo $catColor['text']; ?>;"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p style="color: var(--text-muted); font-size: 14px;"><?php echo htmlspecialchars($product['brand']); ?></p>
                    <?php echo renderProductPrice($product['price'], $product['sale_price']); ?>
                    <a href="product-details.php?id=<?php echo $product['product_id']; ?>" class="btn btn-outline">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top:30px;">
            <a href="products.php" class="btn btn-primary">View All Products</a>
        </div>
    </div>
</section>

<?php if (count($saleProducts) > 0): ?>
<section class="section" style="background: #fef2f2;">
    <div class="container">
        <div class="section-title">
            <h2 style="color:var(--danger);">On Sale Now</h2>
            <p>Limited-time discounted prices</p>
        </div>
        <div class="grid">
            <?php foreach ($saleProducts as $sp): $spCatColor = categoryBadgeColors($sp['category_name']); ?>
            <div class="card">
                <div class="img-slider">
                    <div class="img-slider-track">
                        <img src="uploads/products/<?php echo htmlspecialchars($sp['image']); ?>" alt="<?php echo htmlspecialchars($sp['product_name']); ?>">
                        <?php if (isset($extraBySale[$sp['product_id']])): ?>
                            <?php foreach ($extraBySale[$sp['product_id']] as $img): ?>
                                <img src="uploads/products/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($sp['product_name']); ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="img-slider-prev">&#8249;</button>
                    <button type="button" class="img-slider-next">&#8250;</button>
                    <div class="img-slider-dots"></div>
                </div>
                <div class="card-body">
                    <span class="category-badge" style="background:<?php echo $spCatColor['bg']; ?>; color:<?php echo $spCatColor['text']; ?>;"><?php echo htmlspecialchars($sp['category_name']); ?></span>
                    <h3><?php echo htmlspecialchars($sp['product_name']); ?></h3>
                    <p style="color: var(--text-muted); font-size: 14px;"><?php echo htmlspecialchars($sp['brand']); ?></p>
                    <?php echo renderProductPrice($sp['price'], $sp['sale_price']); ?>
                    <a href="product-details.php?id=<?php echo $sp['product_id']; ?>" class="btn btn-outline">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section-mint">
    <div class="container">
        <div class="section-title">
            <h2>Popular Services</h2>
            <p>Expert repair and technical services</p>
        </div>
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
        <div style="text-align:center; margin-top:30px;">
            <a href="services.php" class="btn btn-primary">View All Services</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title"><h2>Why Choose Us</h2></div>
        <div class="grid">
            <div class="card"><div class="card-body">
                <h3>Genuine Products</h3>
                <p style="color:var(--text-muted);">All products sourced from trusted suppliers.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <h3>Expert Technicians</h3>
                <p style="color:var(--text-muted);">Years of hands-on repair experience.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <h3>Fair Pricing</h3>
                <p style="color:var(--text-muted);">Honest prices, no hidden charges.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <h3>Local & Trusted</h3>
                <p style="color:var(--text-muted);">Proudly serving the Khuzdar community.</p>
            </div></div>
        </div>
    </div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="grid" style="text-align:center;">
            <div><h2 style="color:var(--accent); font-size:36px;">500+</h2><p style="color:#94a3b8;">Happy Customers</p></div>
            <div><h2 style="color:var(--accent); font-size:36px;">1000+</h2><p style="color:#94a3b8;">Products Sold</p></div>
            <div><h2 style="color:var(--accent); font-size:36px;">5+</h2><p style="color:#94a3b8;">Years Experience</p></div>
            <div><h2 style="color:var(--accent); font-size:36px;">24/7</h2><p style="color:#94a3b8;">Customer Support</p></div>
        </div>
    </div>
</section>

<section class="section section-neutral">
    <div class="container">
        <div class="section-title"><h2>Our Shop</h2></div>
        <div class="grid">
            <?php foreach ($galleryImages as $item): ?>
            <div class="card">
                <div class="img-slider">
                    <div class="img-slider-track">
                        <img src="uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php if (isset($extraByGalleryPreview[$item['gallery_id']])): ?>
                            <?php foreach ($extraByGalleryPreview[$item['gallery_id']] as $img): ?>
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
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-dark" style="text-align: center;">
    <div class="container">
        <h2 style="color:#fff;">Need Help With Your Device?</h2>
        <p style="color:#94a3b8; margin: 12px 0 24px;">Get in touch with us today for products or repair services.</p>
        <a href="contact.php" class="btn btn-primary">Contact Us</a>
    </div>
</section>

<?php require 'includes/footer.php'; ?>