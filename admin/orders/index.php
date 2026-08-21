<?php
$pageTitle = 'Orders';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';

$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();

require '../includes/admin-header.php';
?>

<div class="desktop-table-only">
<div class="table-responsive">
<table class="admin-table">
    <tr><th>Reference</th><th>Product</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Actions</th></tr>
    <?php foreach ($orders as $o): $st = orderStatusInfo($o['order_status']); ?>
    <tr>
        <td>ORD-<?php echo htmlspecialchars($o['reference_token']); ?></td>
        <td><?php echo htmlspecialchars($o['product_name_snapshot']); ?> x<?php echo $o['quantity']; ?></td>
        <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
        <td>Rs. <?php echo number_format($o['total_amount']); ?></td>
        <td><?php echo paymentMethodLabel($o['payment_method']); ?></td>
        <td><span class="category-badge" style="background:<?php echo $st['bg']; ?>; color:<?php echo $st['text']; ?>;"><?php echo $st['label']; ?></span></td>
        <td><a href="view.php?id=<?php echo $o['order_id']; ?>">View</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (count($orders) === 0): ?>
    <tr><td colspan="7" style="text-align:center; color:var(--text-muted);">No orders yet.</td></tr>
    <?php endif; ?>
</table>
</div>
</div>

<div class="mobile-list-only">
<div class="recent-card">
    <?php if (count($orders) === 0): ?><div class="empty-row">No orders yet.</div><?php endif; ?>
    <?php foreach ($orders as $o): $st = orderStatusInfo($o['order_status']); ?>
    <div class="recent-list-item wrap-actions">
        <div class="avatar-circle" style="background:var(--accent-dark);"><?php echo strtoupper(substr($o['customer_name'], 0, 1)); ?></div>
        <div class="recent-content">
            <div class="recent-title">ORD-<?php echo htmlspecialchars($o['reference_token']); ?></div>
            <div class="recent-subtitle"><?php echo htmlspecialchars($o['product_name_snapshot']); ?> x<?php echo $o['quantity']; ?></div>
        </div>
        <div class="recent-meta">
            <div class="recent-value">Rs. <?php echo number_format($o['total_amount']); ?></div>
            <span class="category-badge" style="background:<?php echo $st['bg']; ?>; color:<?php echo $st['text']; ?>; font-size:11px;"><?php echo $st['label']; ?></span>
        </div>
        <div class="mobile-item-actions">
            <a href="view.php?id=<?php echo $o['order_id']; ?>">View</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>

<?php require '../includes/admin-footer.php'; ?>