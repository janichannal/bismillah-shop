<?php
require '../includes/auth-check.php';
require '../../config/database.php';

$serviceId = (int) ($_GET['id'] ?? 0);

if ($serviceId > 0) {
    $stmt = $pdo->prepare("SELECT image FROM services WHERE service_id = ?");
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch();

    if ($service) {
        $imagePath = '../../uploads/services/' . $service['image'];
        if (file_exists($imagePath)) { unlink($imagePath); }
    }

    $stmt = $pdo->prepare("DELETE FROM services WHERE service_id = ?");
    $stmt->execute([$serviceId]);
}

header('Location: index.php?deleted=1');
exit;
?>