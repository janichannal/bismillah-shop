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
    $message['status'] = 'read';
}

$statusUpdated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_status'])) {
    $newStatus = $_POST['customer_status'];
    if (in_array($newStatus, ['pending', 'in_progress', 'resolved'])) {
        $pdo->prepare("UPDATE messages SET customer_status = ? WHERE message_id = ?")->execute([$newStatus, $messageId]);
        $message['customer_status'] = $newStatus;
        $statusUpdated = true;
    }
}

require '../includes/admin-header.php';
?>

<?php if ($statusUpdated): ?><div class="alert alert-success">Enquiry status updated — the customer can now see this when checking their reference number.</div><?php endif; ?>

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

        <?php if (!empty($message['reference_token'])): ?>
        <p style="color:var(--text-muted); margin-bottom:6px; font-size:13px;">REFERENCE NUMBER</p>
        <p style="font-family:var(--font-heading); font-weight:700; letter-spacing:1px; margin-bottom:20px;">ENQ-<?php echo htmlspecialchars($message['reference_token']); ?></p>
        <?php endif; ?>

        <p style="color:var(--text-muted); margin-bottom:6px;">SUBJECT</p>
        <p style="margin-bottom:20px; font-weight:600;"><?php echo htmlspecialchars($message['subject'] ?: '(No subject)'); ?></p>

        <p style="color:var(--text-muted); margin-bottom:6px;">MESSAGE</p>
        <div style="background:var(--bg-light); border-radius:var(--radius); padding:16px; margin-bottom:20px;">
            <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
        </div>

        <p style="color:var(--text-muted); font-size:13px; margin-bottom:20px;">
            Received: <?php echo date('d M Y, h:i A', strtotime($message['created_at'])); ?>
        </p>

        <?php if (!empty($message['reference_token'])): ?>
        <form method="POST" action="view.php?id=<?php echo $message['message_id']; ?>" style="margin-bottom:20px;">
            <div class="form-group">
                <label>Enquiry Status (visible to the customer)</label>
                <select name="customer_status">
                    <option value="pending" <?php echo $message['customer_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $message['customer_status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?php echo $message['customer_status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
        <?php endif; ?>

        <a href="index.php" class="btn btn-outline">Back to Messages</a>
        <a href="delete.php?id=<?php echo $message['message_id']; ?>" onclick="return confirm('Delete this message?');" class="btn" style="background:var(--danger); color:#fff;">Delete</a>
    </div>
</div>

<?php require '../includes/admin-footer.php'; ?>