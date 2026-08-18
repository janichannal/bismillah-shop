<?php
require '../includes/auth-check.php';
require '../../config/database.php';

$reviewId = (int) ($_GET['id'] ?? 0);
if ($reviewId > 0) {
    $pdo->prepare("UPDATE reviews SET status = 'approved' WHERE review_id = ?")->execute([$reviewId]);
}
header('Location: index.php?approved=1');
exit;
?>