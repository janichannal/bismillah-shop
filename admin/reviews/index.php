<?php
$pageTitle = 'Reviews';
require '../includes/auth-check.php';
require '../../config/database.php';

$reviews = $pdo->query("SELECT r.*, p.product_name FROM reviews r 
                         JOIN products p ON r.product_id = p.product_id 
                         ORDER BY r.status ASC, r.created_at DESC")->fetchAll();

require '../includes/admin-header.php';
?>

<?php if (isset($_GET['approved'])): ?><div class="alert alert-success">Review approved and now visible to customers.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Review deleted.</div><?php endif; ?>

<div class="table-responsive">
<table class="admin-table">
    <tr><th></th><th>Product</th><th>Customer</th><th>Rating</th><th>Review</th><th>Status</th><th>Actions</th></tr>
    <?php foreach ($reviews as $r): ?>
    <tr style="<?php echo $r['status'] === 'pending' ? 'font-weight:700;' : ''; ?>">
        <td><div class="avatar-circle" style="background:#db2777;"><?php echo strtoupper(substr($r['customer_name'], 0, 1)); ?></div></td>
        <td><?php echo htmlspecialchars($r['product_name']); ?></td>
        <td><?php echo htmlspecialchars($r['customer_name']); ?></td>
        <td style="color:var(--accent-dark);"><?php echo str_repeat('&#9733;', $r['rating']) . str_repeat('&#9734;', 5 - $r['rating']); ?></td>
        <td><?php echo htmlspecialchars(substr($r['review_text'], 0, 60)); ?><?php echo strlen($r['review_text']) > 60 ? '...' : ''; ?></td>
        <td>
            <?php if ($r['status'] === 'pending'): ?>
                <span class="category-badge" style="background:var(--accent-light); color:var(--accent-dark);">Pending</span>
            <?php else: ?>
                <span class="category-badge" style="background:#dbeafe; color:var(--success);">Approved</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if ($r['status'] === 'pending'): ?>
                <a href="approve.php?id=<?php echo $r['review_id']; ?>" style="color:var(--success);">Approve</a> |
            <?php endif; ?>
            <a href="delete.php?id=<?php echo $r['review_id']; ?>" onclick="return confirm('Delete this review?');" style="color:var(--danger);">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (count($reviews) === 0): ?>
    <tr><td colspan="7" style="text-align:center; color:var(--text-muted);">No reviews yet.</td></tr>
    <?php endif; ?>
</table>
</div>

<?php require '../includes/admin-footer.php'; ?>