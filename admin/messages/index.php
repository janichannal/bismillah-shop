<?php
$pageTitle = 'Messages';
require '../includes/auth-check.php';
require '../../config/database.php';

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();

require '../includes/admin-header.php';
?>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Message deleted successfully.</div><?php endif; ?>

<div class="desktop-table-only">
<div class="table-responsive">
<table class="admin-table">
    <tr><th></th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th></tr>
    <?php foreach ($messages as $m): ?>
    <tr style="<?php echo $m['status'] === 'unread' ? 'font-weight:700;' : ''; ?>">
        <td><div class="avatar-circle" style="background:<?php echo $m['status'] === 'unread' ? 'var(--success)' : '#94a3b8'; ?>;"><?php echo strtoupper(substr($m['name'], 0, 1)); ?></div></td>
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
</div>

<div class="mobile-list-only">
<div class="recent-card">
    <?php if (count($messages) === 0): ?><div class="empty-row">No messages yet.</div><?php endif; ?>
    <?php foreach ($messages as $m): ?>
    <div class="recent-list-item wrap-actions">
        <div class="avatar-circle" style="background:<?php echo $m['status'] === 'unread' ? 'var(--success)' : '#94a3b8'; ?>;"><?php echo strtoupper(substr($m['name'], 0, 1)); ?></div>
        <div class="recent-content">
            <div class="recent-title"><?php echo htmlspecialchars($m['name']); ?></div>
            <div class="recent-subtitle"><?php echo htmlspecialchars($m['subject'] ?: '(No subject)'); ?></div>
        </div>
        <div class="recent-meta">
            <?php if ($m['status'] === 'unread'): ?>
                <span class="category-badge" style="background:var(--accent-light); color:var(--accent-dark); font-size:11px;">Unread</span>
            <?php else: ?>
                <span class="category-badge" style="background:#f1f5f9; color:#475569; font-size:11px;">Read</span>
            <?php endif; ?>
            <div class="recent-sub"><?php echo date('d M', strtotime($m['created_at'])); ?></div>
        </div>
        <div class="mobile-item-actions">
            <a href="view.php?id=<?php echo $m['message_id']; ?>">View</a>
            <a href="delete.php?id=<?php echo $m['message_id']; ?>" onclick="return confirm('Delete this message?');" class="danger-link">Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>

<?php require '../includes/admin-footer.php'; ?>