<?php
$pageTitle = 'Product Details';
require 'config/database.php';
require 'includes/functions.php';

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$product = null;
$extraImages = [];
$reviews = [];
$avgRating = 0;
$reviewCount = 0;
$reviewError = '';
$relatedProducts = [];
$extraByRelated = [];

if ($productId > 0) {
    $stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p 
                            JOIN categories c ON p.category_id = c.category_id 
                            WHERE p.product_id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, image_id ASC");
        $stmt->execute([$productId]);
        $extraImages = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? AND status = 'approved' ORDER BY created_at DESC");
        $stmt->execute([$productId]);
        $reviews = $stmt->fetchAll();

        $reviewCount = count($reviews);
        if ($reviewCount > 0) {
            $total = 0;
            foreach ($reviews as $r) { $total += $r['rating']; }
            $avgRating = $total / $reviewCount;
        }

        // Related products: same category, excluding this one
        $stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p 
                                JOIN categories c ON p.category_id = c.category_id 
                                WHERE p.category_id = ? AND p.product_id != ? 
                                ORDER BY p.created_at DESC LIMIT 4");
        $stmt->execute([$product['category_id'], $productId]);
        $relatedProducts = $stmt->fetchAll();

        $relatedIds = array_column($relatedProducts, 'product_id');
        if (count($relatedIds) > 0) {
            $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
            $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id IN ($placeholders) ORDER BY sort_order ASC, image_id ASC");
            $stmt->execute($relatedIds);
            foreach ($stmt->fetchAll() as $img) {
                $extraByRelated[$img['product_id']][] = $img;
            }
        }
    }
}

if ($product && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $customerName = trim($_POST['customer_name'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);
    $reviewText = trim($_POST['review_text'] ?? '');

    if ($customerName === '' || $rating < 1 || $rating > 5) {
        $reviewError = 'Please enter your name and select a rating.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, customer_name, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$productId, $customerName, $rating, $reviewText]);
        header('Location: product-details.php?id=' . $productId . '&review_submitted=1');
        exit;
    }
}

if ($product) {
    $pageTitle = $product['product_name'];
    $pageDescription = $product['product_name'] . ' by ' . $product['brand'] . ' - Rs. ' . number_format($product['price']) . '. Available at Bismillah Mobile & Laptop Shop, Khuzdar.';
}
require 'includes/header.php';
?>

<section class="section">
    <div class="container">

        <?php if (!$product): ?>
            <div style="text-align:center; padding: 60px 0;">
                <h2>Product Not Found</h2>
                <p style="color:var(--text-muted); margin: 12px 0 24px;">
                    The product you're looking for doesn't exist or may have been removed.
                </p>
                <a href="products.php" class="btn btn-primary">Back to Products</a>
            </div>
       <?php else: $catColor = categoryBadgeColors($product['category_name']); ?>

        <?php echo renderBreadcrumbs([
            ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
            ['label' => 'Products', 'url' => '/bismillah-shop/products.php'],
            ['label' => $product['category_name'], 'url' => '/bismillah-shop/products.php?category=' . $product['category_id']],
            ['label' => $product['product_name']],
        ]); ?>

        <div class="grid" style="grid-template-columns: 1fr 1fr; align-items: start;">

            <div class="img-slider">
                <div class="img-slider-track">
                    <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <?php foreach ($extraImages as $img): ?>
                        <img src="uploads/products/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <?php endforeach; ?>
                </div>
                <button type="button" class="img-slider-prev">&#8249;</button>
                <button type="button" class="img-slider-next">&#8250;</button>
                <div class="img-slider-dots"></div>
            </div>

            <div>
                <span class="category-badge" style="background:<?php echo $catColor['bg']; ?>; color:<?php echo $catColor['text']; ?>;"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h1 style="margin-top:10px;"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <p style="color:var(--text-muted); margin: 8px 0;">
                    <?php echo htmlspecialchars($product['brand']); ?>
                </p>

                <?php if ($reviewCount > 0): ?>
                <p style="margin-bottom:8px;">
                    <span style="color:var(--accent-dark); font-size:18px;"><?php echo renderStars($avgRating); ?></span>
                    <span style="color:var(--text-muted); font-size:14px;"> <?php echo number_format($avgRating, 1); ?> (<?php echo $reviewCount; ?> review<?php echo $reviewCount > 1 ? 's' : ''; ?>)</span>
                </p>
                <?php else: ?>
                <p style="color:var(--text-muted); font-size:14px; margin-bottom:8px;">No reviews yet — be the first to review this product.</p>
                <?php endif; ?>
                <?php echo renderProductPrice($product['price'], $product['sale_price'], 'large'); ?>

                <table style="width:100%; margin-bottom: 20px;">
                    <tr>
                        <td style="padding:8px 0; color:var(--text-muted);">Model Number</td>
                        <td style="padding:8px 0; font-weight:600;"><?php echo htmlspecialchars($product['model_number']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:var(--text-muted);">Condition</td>
                        <td style="padding:8px 0; font-weight:600;"><?php echo htmlspecialchars($product['condition']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:var(--text-muted);">Availability</td>
                        <td style="padding:8px 0; font-weight:600;">
                            <?php echo $product['stock_quantity'] > 0 
                                ? '<span style="color:var(--success);">In Stock (' . $product['stock_quantity'] . ' available)</span>' 
                                : '<span style="color:var(--danger);">Out of Stock</span>'; ?>
                        </td>
                    </tr>
                </table>

                <p style="color:var(--text); margin-bottom: 24px;">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>

                <a href="contact.php?subject=<?php echo urlencode('Product Enquiry: ' . $product['product_name']); ?>&message=<?php echo urlencode('Hi, I am interested in the ' . $product['product_name'] . ' (Rs. ' . number_format($product['price']) . '). Please provide more information.'); ?>" class="btn btn-primary">Enquire About This Product</a>
                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode('Hi, I am interested in the ' . $product['product_name'] . ' (Rs. ' . number_format($product['price']) . '). Please provide more information.'); ?>" target="_blank" rel="noopener" class="btn" style="background:#25D366; color:#fff;">Chat on WhatsApp</a>
                <a href="products.php" class="btn btn-outline">Back to Products</a>
            </div>
        </div>

        <div style="max-width:800px; margin: 60px auto 0;">
            <h2 style="margin-bottom:20px;">Customer Reviews</h2>

            <?php if (isset($_GET['review_submitted'])): ?>
                <div class="alert alert-success">Thank you! Your review has been submitted and will appear once approved.</div>
            <?php endif; ?>

            <?php if (count($reviews) === 0): ?>
                <p style="color:var(--text-muted); margin-bottom:24px;">No reviews yet.</p>
            <?php else: ?>
                <?php foreach ($reviews as $r): ?>
                <div class="card" style="margin-bottom:14px;">
                    <div class="card-body">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <strong><?php echo htmlspecialchars($r['customer_name']); ?></strong>
                            <span style="color:var(--text-muted); font-size:13px;"><?php echo date('d M Y', strtotime($r['created_at'])); ?></span>
                        </div>
                        <div style="color:var(--accent-dark); margin:4px 0;"><?php echo renderStars($r['rating']); ?></div>
                        <?php if ($r['review_text']): ?>
                            <p style="color:var(--text); font-size:14px;"><?php echo nl2br(htmlspecialchars($r['review_text'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="card section-mint" style="margin-top:30px; border:none;">
                <div class="card-body">
                    <h3 style="margin-bottom:16px;">Leave a Review</h3>

                    <?php if ($reviewError): ?><div class="alert alert-danger"><?php echo htmlspecialchars($reviewError); ?></div><?php endif; ?>

                    <form method="POST" action="product-details.php?id=<?php echo $productId; ?>">
                        <input type="hidden" name="submit_review" value="1">
                        <div class="form-group">
                            <label>Your Name</label>
                            <input type="text" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label>Rating</label>
                            <select name="rating" required>
                                <option value="">-- Select Rating --</option>
                                <option value="5">★★★★★ Excellent (5)</option>
                                <option value="4">★★★★☆ Good (4)</option>
                                <option value="3">★★★☆☆ Average (3)</option>
                                <option value="2">★★☆☆☆ Poor (2)</option>
                                <option value="1">★☆☆☆☆ Very Poor (1)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Your Review (optional)</label>
                            <textarea name="review_text" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if (count($relatedProducts) > 0): ?>
        <div style="margin-top: 70px;">
            <div class="section-title">
                <h2>You Might Also Like</h2>
                <p>More from <?php echo htmlspecialchars($product['category_name']); ?></p>
            </div>
            <div class="grid">
                <?php foreach ($relatedProducts as $rp): $rpCatColor = categoryBadgeColors($rp['category_name']); ?>
                <div class="card">
                    <div class="img-slider">
                        <div class="img-slider-track">
                            <img src="uploads/products/<?php echo htmlspecialchars($rp['image']); ?>" alt="<?php echo htmlspecialchars($rp['product_name']); ?>">
                            <?php if (isset($extraByRelated[$rp['product_id']])): ?>
                                <?php foreach ($extraByRelated[$rp['product_id']] as $img): ?>
                                    <img src="uploads/products/<?php echo htmlspecialchars($img['image']); ?>" alt="<?php echo htmlspecialchars($rp['product_name']); ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="img-slider-prev">&#8249;</button>
                        <button type="button" class="img-slider-next">&#8250;</button>
                        <div class="img-slider-dots"></div>
                    </div>
                    <div class="card-body">
                        <span class="category-badge" style="background:<?php echo $rpCatColor['bg']; ?>; color:<?php echo $rpCatColor['text']; ?>;"><?php echo htmlspecialchars($rp['category_name']); ?></span>
                        <h3><?php echo htmlspecialchars($rp['product_name']); ?></h3>
                        <p style="color: var(--text-muted); font-size: 14px;"><?php echo htmlspecialchars($rp['brand']); ?></p>
                        <?php echo renderProductPrice($rp['price'], $rp['sale_price']); ?>
                        <a href="product-details.php?id=<?php echo $rp['product_id']; ?>" class="btn btn-outline">View Details</a>
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