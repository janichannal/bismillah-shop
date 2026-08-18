<?php
$pageTitle = 'View Message';
require '../includes/auth-check.php';
require '../../config/database.php';

$messageId = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM messages WHERE message_id = ?");
$stmt->execute([$messageId]);
$message = $stmt->fetch();

if (!$message) {
    header('Location: index.php');
    exit;
}

if ($message['status'] === 'unread') {
    $pdo->prepare("UPDATE messages SET status = 'read' WHERE message_id = ?")->execute([$messageId]);
}

require '../includes/admin-header.php';
?>

<div class="card" style="max-width:650px;">
    <div class="card-body">
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:20px;">
            <div class="avatar-circle" style="background:var(--success); width:52px; height:52px; font-size:18px;"><?php echo strtoupper(substr($message['name'], 0, 1)); ?></div>
            <div>
                <h3 style="margin-bottom:2px;"><?php echo htmlspecialchars($message['name']); ?></h3>
                <p style="color:var(--text-muted); font-size:14px;">
                    <?php echo htmlspecialchars($message['email']); ?>
                    <?php if ($message['phone']): ?> · <?php echo htmlspecialchars($message['phone']); ?><?php endif; ?>
                </p>
            </div>
        </div>

        <p style="color:var(--text-muted); margin-bottom:6px; font-size:13px;">SUBJECT</p>
        <p style="margin-bottom:20px; font-weight:600;"><?php echo htmlspecialchars($message['subject'] ?: '(No subject)'); ?></p>

        <p style="color:var(--text-muted); margin-bottom:6px; font-size:13px;">MESSAGE</p>
        <div style="background:var(--bg-light); border-radius:var(--radius); padding:16px; margin-bottom:20px;">
            <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
        </div>

        <p style="color:var(--text-muted); font-size:13px; margin-bottom:20px;">
            Received: <?php echo date('d M Y, h:i A', strtotime($message['created_at'])); ?>
        </p>

        <a href="index.php" class="btn btn-outline">Back to Messages</a>
        <a href="delete.php?id=<?php echo $message['message_id']; ?>" onclick="return confirm('Delete this message?');" class="btn" style="background:var(--danger); color:#fff;">Delete</a>
    </div>
</div>

<?php require '../includes/admin-footer.php'; ?>