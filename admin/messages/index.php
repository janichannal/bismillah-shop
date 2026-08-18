<?php
$pageTitle = 'Messages';
require '../includes/auth-check.php';
require '../../config/database.php';

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();

require '../includes/admin-header.php';
?>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Message deleted successfully.</div><?php endif; ?>

<div class="table-responsive">
<table class="admin-table">
    <tr><th></th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th></tr>
    <?php foreach ($messages as $m): ?>
    <tr style="<?php echo $m['status'] === 'unread' ? 'font-weight:700;' : ''; ?>">
        <td><div class="avatar-circle" style="background:var(--success);"><?php echo strtoupper(substr($m['name'], 0, 1)); ?></div></td>
        <td><?php echo htmlspecialchars($m['name']); ?></td>
        <td><?php echo htmlspecialchars($m['email']); ?></td>
        <td><?php echo htmlspecialchars($m['subject']); ?></td>
        <td>
            <?php if ($m['status'] === 'unread'): ?>
                <span class="category-badge" style="background:var(--accent-light); color:var(--accent-dark);">Unread</span>
            <?php else: ?>
                <span class="category-badge" style="background:#f1f5f9; color:#475569;">Read</span>
            <?php endif; ?>
        </td>
        <td><?php echo date('d M Y', strtotime($m['created_at'])); ?></td>
        <td>
            <a href="view.php?id=<?php echo $m['message_id']; ?>">View</a> |
            <a href="delete.php?id=<?php echo $m['message_id']; ?>" onclick="return confirm('Delete this message?');" style="color:var(--danger);">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>

<?php require '../includes/admin-footer.php'; ?>