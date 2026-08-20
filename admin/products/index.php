<?php
$pageTitle = 'Products';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';

$products = $pdo->query("SELECT p.*, c.category_name FROM products p 
                          JOIN categories c ON p.category_id = c.category_id 
                          ORDER BY p.created_at DESC")->fetchAll();

require '../includes/admin-header.php';
?>

<div style="margin-bottom:20px;">
    <a href="add.php" class="btn btn-primary">+ Add Product</a>
</div>

<?php if (isset($_GET['added'])): ?><div class="alert alert-success">Product added successfully.</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Product updated successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Product deleted successfully.</div><?php endif; ?>

<div class="desktop-table-only">
<div class="table-responsive">
<table class="admin-table">
    <tr><th>Image</th><th>Name</th><th>Category</th><th>Brand</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
    <?php foreach ($products as $p): $catColor = categoryBadgeColors($p['category_name']); ?>
    <tr>
        <td><img src="/bismillah-shop/uploads/products/<?php echo htmlspecialchars($p['image']); ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;"></td>
        <td><?php echo htmlspecialchars($p['product_name']); ?></td>
        <td><span class="category-badge" style="background:<?php echo $catColor['bg']; ?>; color:<?php echo $catColor['text']; ?>;"><?php echo htmlspecialchars($p['category_name']); ?></span></td>
        <td><?php echo htmlspecialchars($p['brand']); ?></td>
        <td>
            <?php if ($p['sale_price'] !== null && $p['sale_price'] > 0 && $p['sale_price'] < $p['price']): ?>
                <span style="text-decoration:line-through; color:var(--text-muted); font-size:12px;">Rs. <?php echo number_format($p['price']); ?></span><br>
                <span style="color:var(--danger); font-weight:700;">Rs. <?php echo number_format($p['sale_price']); ?></span>
            <?php else: ?>
                Rs. <?php echo number_format($p['price']); ?>
            <?php endif; ?>
        </td>
        <td><?php echo $p['stock_quantity']; ?></td>
        <td>
            <a href="edit.php?id=<?php echo $p['product_id']; ?>">Edit</a> |
            <a href="delete.php?id=<?php echo $p['product_id']; ?>" onclick="return confirm('Delete this product?');" style="color:var(--danger);">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
</div>

<div class="mobile-list-only">
<div class="recent-card">
    <?php if (count($products) === 0): ?><div class="empty-row">No products yet.</div><?php endif; ?>
    <?php foreach ($products as $p): $catColor = categoryBadgeColors($p['category_name']); ?>
    <div class="recent-list-item wrap-actions">
        <img src="/bismillah-shop/uploads/products/<?php echo htmlspecialchars($p['image']); ?>" class="recent-thumb">
        <div class="recent-content">
            <div class="recent-title"><?php echo htmlspecialchars($p['product_name']); ?></div>
            <div class="recent-subtitle"><?php echo htmlspecialchars($p['category_name']); ?> · <?php echo htmlspecialchars($p['brand']); ?></div>
        </div>
        <div class="recent-meta">
            <?php if ($p['sale_price'] !== null && $p['sale_price'] > 0 && $p['sale_price'] < $p['price']): ?>
                <div style="text-decoration:line-through; color:var(--text-muted); font-size:11px;">Rs. <?php echo number_format($p['price']); ?></div>
                <div class="recent-value" style="color:var(--danger);">Rs. <?php echo number_format($p['sale_price']); ?></div>
            <?php else: ?>
                <div class="recent-value">Rs. <?php echo number_format($p['price']); ?></div>
            <?php endif; ?>
            <span class="stock-pill <?php echo $p['stock_quantity'] < 5 ? 'stock-low' : 'stock-healthy'; ?>"><?php echo $p['stock_quantity']; ?> left</span>
        </div>
        <div class="mobile-item-actions">
            <a href="edit.php?id=<?php echo $p['product_id']; ?>">Edit</a>
            <a href="delete.php?id=<?php echo $p['product_id']; ?>" onclick="return confirm('Delete this product?');" class="danger-link">Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>

<?php require '../includes/admin-footer.php'; ?>