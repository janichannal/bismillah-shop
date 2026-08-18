<?php
require '../includes/auth-check.php';
require '../../config/database.php';

$productId = (int) ($_GET['id'] ?? 0);

if ($productId > 0) {
    $stmt = $pdo->prepare("SELECT image FROM products WHERE product_id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        $imagePath = '../../uploads/products/' . $product['image'];
        if (file_exists($imagePath)) { unlink($imagePath); }
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$productId]);
}

header('Location: index.php?deleted=1');
exit;
?>