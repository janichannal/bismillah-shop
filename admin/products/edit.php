<?php
$pageTitle = 'Edit Product';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';

$productId = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId  = (int) ($_POST['category_id'] ?? 0);
    $productName = trim($_POST['product_name'] ?? '');
    $brand       = trim($_POST['brand'] ?? '');
    $modelNumber = trim($_POST['model_number'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $salePriceRaw = trim($_POST['sale_price'] ?? '');
    $salePrice   = ($salePriceRaw !== '') ? (float) $salePriceRaw : null;
    $stock       = (int) ($_POST['stock_quantity'] ?? 0);
    $condition   = $_POST['condition'] ?? 'New';
    $description = trim($_POST['description'] ?? '');

    if ($categoryId === 0 || $productName === '' || $price <= 0) {
        $errorMessage = 'Please fill in category, product name, and a valid price.';
    } elseif ($salePrice !== null && $salePrice >= $price) {
        $errorMessage = 'Sale price must be lower than the regular price.';
    } else {
        $imageName = $product['image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadError = '';
            $newImage = handleImageUpload('image', '../../uploads/products/', $uploadError);
            if ($newImage) { $imageName = $newImage; } else { $errorMessage = $uploadError; }
        }

        if (!$errorMessage) {
            $stmt = $pdo->prepare("UPDATE products SET 
                category_id = ?, product_name = ?, brand = ?, model_number = ?, price = ?, sale_price = ?,
                stock_quantity = ?, `condition` = ?, description = ?, image = ? WHERE product_id = ?");
            $stmt->execute([$categoryId, $productName, $brand, $modelNumber, $price, $salePrice, $stock, $condition, $description, $imageName, $productId]);

            $extraFiles = handleMultipleImageUpload('extra_images', '../../uploads/products/');
            if (count($extraFiles) > 0) {
                $maxOrder = (int) $pdo->query("SELECT COALESCE(MAX(sort_order), -1) FROM product_images WHERE product_id = $productId")->fetchColumn();
                foreach ($extraFiles as $i => $extraFile) {
                    $stmt2 = $pdo->prepare("INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)");
                    $stmt2->execute([$productId, $extraFile, $maxOrder + 1 + $i]);
                }
            }

            header('Location: edit.php?id=' . $productId . '&updated=1');
            exit;
        }
    }
}

require '../includes/admin-header.php';
?>

<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Product updated successfully.</div><?php endif; ?>
<?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

<form method="POST" action="edit.php?id=<?php echo $product['product_id']; ?>" enctype="multipart/form-data" style="max-width:600px;">
    <div class="form-group">
        <label>Category</label>
        <select name="category_id" required>
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c['category_id']; ?>" <?php echo $c['category_id'] == $product['category_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c['category_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><label>Product Name</label><input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required></div>
    <div class="form-group"><label>Brand</label><input type="text" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>"></div>
    <div class="form-group"><label>Model Number</label><input type="text" name="model_number" value="<?php echo htmlspecialchars($product['model_number']); ?>"></div>
    <div class="form-group"><label>Price (Rs.)</label><input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required></div>
    <div class="form-group">
        <label>Sale Price (Rs.) — optional, leave blank if not on sale</label>
        <input type="number" step="0.01" name="sale_price" value="<?php echo $product['sale_price'] !== null ? $product['sale_price'] : ''; ?>">
    </div>
    <div class="form-group"><label>Stock Quantity</label><input type="number" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required></div>
    <div class="form-group">
        <label>Condition</label>
        <select name="condition">
            <?php foreach (['New','Used','Refurbished'] as $cond): ?>
                <option value="<?php echo $cond; ?>" <?php echo $product['condition'] === $cond ? 'selected' : ''; ?>><?php echo $cond; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><label>Description</label><textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea></div>
    <div class="form-group">
        <label>Current Main Image</label><br>
        <img src="/bismillah-shop/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" style="width:100px; border-radius:6px; margin-bottom:10px;">
        <label>Replace Main Image (optional)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <?php
    $existingExtra = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
    $existingExtra->execute([$productId]);
    $existingExtra = $existingExtra->fetchAll();
    ?>
    <div class="form-group">
        <label>Extra Photos</label>
        <?php if (count($existingExtra) > 0): ?>
        <div class="admin-thumb-grid">
            <?php foreach ($existingExtra as $img): ?>
            <div class="admin-thumb-item">
                <img src="/bismillah-shop/uploads/products/<?php echo htmlspecialchars($img['image']); ?>">
                <a href="delete-image.php?image_id=<?php echo $img['image_id']; ?>&product_id=<?php echo $productId; ?>" 
                   class="admin-thumb-delete" onclick="return confirm('Remove this photo?');">&times;</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color:var(--text-muted); font-size:14px; margin-bottom:10px;">No extra photos yet.</p>
        <?php endif; ?>

        <label style="margin-top:14px; display:block;">Add More Photos (hold Ctrl to pick several)</label>
        <input type="file" name="extra_images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="index.php" class="btn btn-outline">Cancel</a>
</form>

<?php require '../includes/admin-footer.php'; ?>