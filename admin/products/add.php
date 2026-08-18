<?php
$pageTitle = 'Add Product';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';

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
        $uploadError = '';
        $imageName = handleImageUpload('image', '../../uploads/products/', $uploadError);

        if (!$imageName) {
            $errorMessage = ($uploadError === 'no_file') ? 'Please select a main product image.' : $uploadError;
        } else {
            $stmt = $pdo->prepare("INSERT INTO products 
                (category_id, product_name, brand, model_number, price, sale_price, stock_quantity, `condition`, description, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$categoryId, $productName, $brand, $modelNumber, $price, $salePrice, $stock, $condition, $description, $imageName]);
            $newProductId = $pdo->lastInsertId();

            $extraFiles = handleMultipleImageUpload('extra_images', '../../uploads/products/');
            foreach ($extraFiles as $order => $extraFile) {
                $stmt2 = $pdo->prepare("INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)");
                $stmt2->execute([$newProductId, $extraFile, $order]);
            }

            header('Location: index.php?added=1');
            exit;
        }
    }
}

require '../includes/admin-header.php';
?>

<?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

<form method="POST" action="add.php" enctype="multipart/form-data" style="max-width:600px;">
    <div class="form-group">
        <label>Category</label>
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><label>Product Name</label><input type="text" name="product_name" required></div>
    <div class="form-group"><label>Brand</label><input type="text" name="brand"></div>
    <div class="form-group"><label>Model Number</label><input type="text" name="model_number"></div>
    <div class="form-group"><label>Price (Rs.)</label><input type="number" step="0.01" name="price" required></div>
    <div class="form-group">
        <label>Sale Price (Rs.) — optional, leave blank if not on sale</label>
        <input type="number" step="0.01" name="sale_price">
    </div>
    <div class="form-group"><label>Stock Quantity</label><input type="number" name="stock_quantity" required></div>
    <div class="form-group">
        <label>Condition</label>
        <select name="condition">
            <option value="New">New</option>
            <option value="Used">Used</option>
            <option value="Refurbished">Refurbished</option>
        </select>
    </div>
    <div class="form-group"><label>Description</label><textarea name="description" rows="4"></textarea></div>
    <div class="form-group">
        <label>Main Product Image (JPG, PNG, or WEBP)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
    </div>
    <div class="form-group">
        <label>Extra Photos (optional — hold Ctrl to pick several at once)</label>
        <input type="file" name="extra_images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
    </div>
    <button type="submit" class="btn btn-primary">Add Product</button>
    <a href="index.php" class="btn btn-outline">Cancel</a>
</form>

<?php require '../includes/admin-footer.php'; ?>