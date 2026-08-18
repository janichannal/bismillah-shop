<?php
require '../includes/auth-check.php';
require '../../config/database.php';

$galleryId = (int) ($_GET['id'] ?? 0);

if ($galleryId > 0) {
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE gallery_id = ?");
    $stmt->execute([$galleryId]);
    $item = $stmt->fetch();

    if ($item) {
        $imagePath = '../../uploads/gallery/' . $item['image'];
        if (file_exists($imagePath)) { unlink($imagePath); }
    }

    $stmt = $pdo->prepare("DELETE FROM gallery WHERE gallery_id = ?");
    $stmt->execute([$galleryId]);
}

header('Location: index.php?deleted=1');
exit;
?>