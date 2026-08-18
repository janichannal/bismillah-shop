<?php
require '../includes/auth-check.php';
require '../../config/database.php';

$messageId = (int) ($_GET['id'] ?? 0);

if ($messageId > 0) {
    $stmt = $pdo->prepare("DELETE FROM messages WHERE message_id = ?");
    $stmt->execute([$messageId]);
}

header('Location: index.php?deleted=1');
exit;
?>