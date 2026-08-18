<?php
$pageTitle = 'Products';
require 'config/database.php';
require 'includes/functions.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();

$selectedCategory = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$searchTerm = trim($_GET['search'] ?? '');
$sortOption = $_GET['sort'] ?? 'newest';
$minPriceRaw = trim($_GET['min_price'] ?? '');
$maxPriceRaw = trim($_GET['max_price'] ?? '');

$whereClauses = [];
$params = [];

if ($selectedCategory > 0) {
    $whereClauses[] = "p.category_id = ?";
    $params[] = $selectedCategory;
}
if ($searchTerm !== '') {
    $whereClauses[] = "(p.product_name LIKE ? OR p.brand LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}
if ($minPriceRaw !== '' && is_numeric($minPriceRaw)) {
    $whereClauses[] = "p.price >= ?";
    $params[] = (float) $minPriceRaw;
}
if ($maxPriceRaw !== '' && is_numeric($maxPriceRaw)) {
    $whereClauses[] = "p.price <= ?";
    $params[] = (float) $maxPriceRaw;
}

switch ($sortOption) {
    case 'price_low':  $orderBy = 'COALESCE(p.sale_price, p.price) ASC'; break;
    case 'price_high': $orderBy = 'COALESCE(p.sale_price, p.price) DESC'; break;
    case 'name_az':    $orderBy = 'p.product_name ASC'; break;
    default:           $orderBy = 'p.created_at DESC'; $sortOption = 'newest'; break;
}

$sql = "SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.category_id";
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}
$sql .= " ORDER BY " . $orderBy;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$ratingStats = $pdo->query("SELECT product_id, AVG(rating) as avg_rating, COUNT(*) as review_count 
                             FROM reviews WHERE status = 'approved' GROUP BY product_id")->fetchAll();
$ratingsByProduct = [];
foreach ($ratingStats as $r) {
    $ratingsByProduct[$r['product_id']] = $r;
}

$allExtra = $pdo->query("SELECT * FROM product_images ORDER BY sort_order ASC, image_id ASC")->fetchAll();
$extraByProduct = [];
foreach ($allExtra as $img) {
    $extraByProduct[$img['product_id']][] = $img;
}

// Builds a category button link that preserves the current search/sort/price filters
function buildCategoryLink($categoryId, $search, $sort, $minPrice, $maxPrice) {
    $qparams = [];
    if ($categoryId > 0) $qparams['category'] = $categoryId;
    if ($search !== '') $qparams['search'] = $search;
    if ($sort !== '' && $sort !== 'newest') $qparams['sort'] = $sort;
    if ($minPrice !== '') $qparams['min_price'] = $minPrice;
    if ($maxPrice !== '') $qparams['max_price'] = $maxPrice;
    $qs = http_build_query($qparams);
    return 'products.php' . ($qs !== '' ? '?' . $qs : '');
}

require 'includes/header.php';
?>

<section class="hero" style="padding: 60px 0;">
    <div class="container">
        <h1>Our Products</h1>
        <p>Browse our full range of mobile phones, laptops, and accessories.</p>
    </div>
</section>

<?php
$breadcrumbItems = [['label' => 'Home', 'url' => '/bismillah-shop/index.php']];
if ($selectedCategory > 0) {
    $currentCatName = 'Products';
    foreach ($categories as $c) {
        if ($c['category_id'] == $selectedCategory) { $currentCatName = $c['category_name']; break; }
    }
    $breadcrumbItems[] = ['label' => 'Products', 'url' => '/bismillah-shop/products.php'];
    $breadcrumbItems[] = ['label' => $currentCatName];
} else {
    $breadcrumbItems[] = ['label' => 'Products'];
}
echo renderBreadcrumbs($breadcrumbItems);
?>

<section class="section">
    <div class="container">

        <form method="GET" action="products.php" class="filter-bar">
            <?php if ($selectedCategory > 0): ?>
                <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
            <?php endif; ?>

            <div class="filter-row">
                <input type="text" name="search" placeholder="Search by product name or brand..." value="<?php echo htmlspecialchars($searchTerm); ?>" class="filter-search">

                <select name="sort" class="filter-select">
                    <option value="newest" <?php echo $sortOption === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="price_low" <?php echo $sortOption === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo $sortOption === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="name_az" <?php echo $sortOption === 'name_az' ? 'selected' : ''; ?>>Name: A-Z</option>
                </select>

                <input type="number" name="min_price" placeholder="Min Rs." value="<?php echo htmlspecialchars($minPriceRaw); ?>" class="filter-price-input">
                <span class="filter-price-sep">–</span>
                <input type="number" name="max_price" placeholder="Max Rs." value="<?php echo htmlspecialchars($maxPriceRaw); ?>" class="filter-price-input">

                <button type="submit" class="btn btn-primary">Apply</button>
                <?php if ($searchTerm !== '' || $sortOption !== 'newest' || $minPriceRaw !== '' || $maxPriceRaw !== ''): ?>
                    <a href="products.php<?php echo $selectedCategory > 0 ? '?category=' . $selectedCategory : ''; ?>" class="btn btn-outline">Clear Filters</a>
                <?php endif; ?>
            </div>
        </form>

        <div style="display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin: 24px 0 40px;">
            <a href="<?php echo buildCategoryLink(0, $searchTerm, $sortOption, $minPriceRaw, $maxPriceRaw); ?>" 
               class="btn <?php echo $selectedCategory === 0 ? 'btn-primary' : 'btn-outline'; ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo buildCategoryLink($cat['category_id'], $searchTerm, $sortOption, $minPriceRaw, $maxPriceRaw); ?>" 
                   class="btn <?php echo $selectedCategory === (int)$cat['category_id'] ? 'btn-primary' : 'btn-outline'; ?>">
                   <?php echo htmlspecialchars($cat['category_name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($searchTerm !== '' || $minPriceRaw !== '' || $maxPriceRaw !== ''): ?>
            <p style="text-align:center; color:var(--text-muted); margin-bottom:20px;">
                <?php echo count($products); ?> result<?php echo count($products) !== 1 ? 's' : ''; ?> found
            </p>
        <?php endif; ?>

        <?php if (count($products) === 0): ?>
            <p style="text-align:center; color:var(--text-muted);">No products match your filters. Try adjusting your search or price range.</p>
        <?php else: ?>
        <div class="grid">
            <?php foreach ($products as $product): $catColor = categoryBadgeColors($product['category_name']); ?>
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
                    <?php if (isset($ratingsByProduct[$product['product_id']])): $rs = $ratingsByProduct[$product['product_id']]; ?>
                        <p style="margin:4px 0;"><span style="color:var(--accent-dark);"><?php echo renderStars($rs['avg_rating']); ?></span>
                        <span style="color:var(--text-muted); font-size:13px;"> (<?php echo $rs['review_count']; ?>)</span></p>
                    <?php endif; ?>
                    <p style="font-size: 13px; color: var(--text-muted);">
                        Condition: <?php echo htmlspecialchars($product['condition']); ?> · 
                        <?php echo $product['stock_quantity'] > 0 
                            ? '<span style="color:var(--success);">In Stock</span>' 
                            : '<span style="color:var(--danger);">Out of Stock</span>'; ?>
                    </p>
                    <?php echo renderProductPrice($product['price'], $product['sale_price']); ?>
                    <a href="product-details.php?id=<?php echo $product['product_id']; ?>" class="btn btn-outline">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php require 'includes/footer.php'; ?>