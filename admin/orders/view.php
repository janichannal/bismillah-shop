<?php
$pageTitle = 'Order Details';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';
require '../../includes/mailer.php';

$orderId = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

$statusUpdated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_status'])) {
    $newStatus = $_POST['order_status'];
    $validStatuses = ['pending_payment', 'payment_review', 'confirmed', 'processing', 'completed', 'cancelled'];
    if (in_array($newStatus, $validStatuses)) {
        $wasNotConfirmed = $order['order_status'] !== 'confirmed';
        $pdo->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?")->execute([$newStatus, $orderId]);
        $order['order_status'] = $newStatus;
        $statusUpdated = true;

        if ($newStatus === 'confirmed' && $wasNotConfirmed) {
            notifyCustomerOrderConfirmed($order);
        }
    }
}

require '../includes/admin-header.php';
$st = orderStatusInfo($order['order_status']);
?>

<?php if ($statusUpdated): ?><div class="alert alert-success">Order status updated.</div><?php endif; ?>

<div class="card" style="max-width:650px;">
    <div class="card-body">
        <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">REFERENCE NUMBER</p>
        <p style="font-family:var(--font-heading); font-weight:700; letter-spacing:1px; margin-bottom:20px;">ORD-<?php echo htmlspecialchars($order['reference_token']); ?></p>

        <div class="two-col-grid" style="margin-bottom:20px;">
            <div>
                <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">CUSTOMER</p>
                <p><?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p style="font-size:13px; color:var(--text-muted);"><?php echo htmlspecialchars($order['customer_email']); ?><br><?php echo htmlspecialchars($order['customer_phone']); ?></p>
            </div>
            <div>
                <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">DELIVERY ADDRESS</p>
                <p style="font-size:14px;"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
            </div>
        </div>

        <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">ORDER</p>
        <p style="margin-bottom:20px;"><?php echo htmlspecialchars($order['product_name_snapshot']); ?> &times; <?php echo $order['quantity']; ?> @ Rs. <?php echo number_format($order['unit_price']); ?> = <strong>Rs. <?php echo number_format($order['total_amount']); ?></strong></p>

        <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">PAYMENT METHOD</p>
        <p style="margin-bottom:20px;"><?php echo paymentMethodLabel($order['payment_method']); ?></p>

        <p style="color:var(--text-muted); font-size:13px; margin-bottom:8px;">PAYMENT PROOF</p>
        <?php if ($order['payment_proof_image']): ?>
            <a href="/bismillah-shop/uploads/payment-proofs/<?php echo htmlspecialchars($order['payment_proof_image']); ?>" target="_blank">
                <img src="/bismillah-shop/uploads/payment-proofs/<?php echo htmlspecialchars($order['payment_proof_image']); ?>" style="max-width:220px; border-radius:8px; margin-bottom:20px; display:block; border:1px solid var(--border);">
            </a>
        <?php else: ?>
            <p style="color:var(--text-muted); margin-bottom:20px;">No screenshot uploaded yet.</p>
        <?php endif; ?>

        <p style="color:var(--text-muted); font-size:13px; margin-bottom:8px;">CURRENT STATUS</p>
        <span class="category-badge" style="background:<?php echo $st['bg']; ?>; color:<?php echo $st['text']; ?>; font-size:14px; padding:6px 16px; margin-bottom:20px; display:inline-block;"><?php echo $st['label']; ?></span>

        <form method="POST" action="view.php?id=<?php echo $orderId; ?>" style="margin-top:16px;">
            <div class="form-group">
                <label>Update Status</label>
                <select name="order_status">
                    <option value="pending_payment" <?php echo $order['order_status'] === 'pending_payment' ? 'selected' : ''; ?>>Awaiting Payment</option>
                    <option value="payment_review" <?php echo $order['order_status'] === 'payment_review' ? 'selected' : ''; ?>>Payment Under Review</option>
                    <option value="confirmed" <?php echo $order['order_status'] === 'confirmed' ? 'selected' : ''; ?>>Payment Confirmed</option>
                    <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $order['order_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Setting to "Payment Confirmed" automatically emails the customer.</p>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>

        <a href="index.php" class="btn btn-outline" style="margin-top:10px;">Back to Orders</a>
    </div>
</div>

<?php require '../includes/admin-footer.php'; ?>