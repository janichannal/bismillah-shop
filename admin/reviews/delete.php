<?php
require '../includes/auth-check.php';
require '../../config/database.php';

$reviewId = (int) ($_GET['id'] ?? 0);
if ($reviewId > 0) {
    $pdo->prepare("DELETE FROM reviews WHERE review_id = ?")->execute([$reviewId]);
}
header('Location: index.php?deleted=1');
exit;
?>