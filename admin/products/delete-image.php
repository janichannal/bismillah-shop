<?php
require '../includes/auth-check.php';
require '../../config/database.php';

$imageId = (int) ($_GET['image_id'] ?? 0);
$productId = (int) ($_GET['product_id'] ?? 0);

if ($imageId > 0) {
    $stmt = $pdo->prepare("SELECT image FROM product_images WHERE image_id = ?");
    $stmt->execute([$imageId]);
    $img = $stmt->fetch();

    if ($img) {
        $path = '../../uploads/products/' . $img['image'];
        if (file_exists($path)) { unlink($path); }
        $pdo->prepare("DELETE FROM product_images WHERE image_id = ?")->execute([$imageId]);
    }
}

header('Location: edit.php?id=' . $productId);
exit;
?>