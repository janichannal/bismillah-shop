<?php
require '../includes/auth-check.php';
require '../../config/database.php';

$imageId = (int) ($_GET['image_id'] ?? 0);
$galleryId = (int) ($_GET['gallery_id'] ?? 0);

if ($imageId > 0) {
    $stmt = $pdo->prepare("SELECT image FROM gallery_images WHERE image_id = ?");
    $stmt->execute([$imageId]);
    $img = $stmt->fetch();

    if ($img) {
        $path = '../../uploads/gallery/' . $img['image'];
        if (file_exists($path)) { unlink($path); }
        $pdo->prepare("DELETE FROM gallery_images WHERE image_id = ?")->execute([$imageId]);
    }
}

header('Location: edit.php?id=' . $galleryId);
exit;
?>