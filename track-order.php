<?php
$pageTitle = 'Track Your Order';
require 'config/database.php';
require 'includes/functions.php';

$searchedRef = trim($_GET['ref'] ?? $_POST['ref'] ?? '');
$order = null;
$notFound = false;
$uploadSuccess = false;
$uploadError = '';

if ($searchedRef !== '') {
    $cleanToken = strtoupper(preg_replace('/[^A-Z0-9]/i', '', str_ireplace('ORD', '', $searchedRef)));
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE reference_token = ?");
    $stmt->execute([$cleanToken]);
    $order = $stmt->fetch();
    if (!$order) { $notFound = true; }
}

if ($order && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_proof'])) {
    $err = '';
    $proofImage = handleImageUpload('payment_proof', 'uploads/payment-proofs/', $err);
    if ($proofImage) {
        if (!empty($order['payment_proof_image']) && file_exists('uploads/payment-proofs/' . $order['payment_proof_image'])) {
            unlink('uploads/payment-proofs/' . $order['payment_proof_image']);
        }
        $newStatus = ($order['order_status'] === 'pending_payment') ? 'payment_review' : $order['order_status'];
        $pdo->prepare("UPDATE orders SET payment_proof_image = ?, order_status = ? WHERE order_id = ?")
            ->execute([$proofImage, $newStatus, $order['order_id']]);
        $order['payment_proof_image'] = $proofImage;
        $order['order_status'] = $newStatus;
        $uploadSuccess = true;
    } else {
        $uploadError = ($err === 'no_file') ? 'Please choose a screenshot to upload.' : $err;
    }
}

require 'includes/header.php';
?>

<section class="hero" style="padding: 60px 0;">
    <div class="container">
        <h1>Track Your Order</h1>
        <p>Enter your order reference number to check status or upload payment proof.</p>
    </div>
</section>

<?php echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
    ['label' => 'Track Order'],
]); ?>

<section class="section">
    <div class="container" style="max-width:560px;">

        <form method="GET" action="track-order.php" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:30px;">
            <input type="text" name="ref" placeholder="e.g. ORD-7F3K9A" value="<?php echo htmlspecialchars($searchedRef); ?>" style="flex:1; min-width:180px; padding:12px; border:1px solid var(--border); border-radius:var(--radius); font-size:15px; text-transform:uppercase;">
            <button type="submit" class="btn btn-primary">Track Order</button>
        </form>

        <?php if ($notFound): ?>
            <div class="alert alert-danger">We couldn't find an order with that reference number.</div>
        <?php endif; ?>

        <?php if ($order): $st = orderStatusInfo($order['order_status']); ?>
        <div class="card">
            <div class="card-body">
                <?php if ($uploadSuccess): ?>
                    <div class="alert alert-success">Payment screenshot uploaded! We'll review it and confirm your order soon.</div>
                <?php endif; ?>
                <?php if ($uploadError): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($uploadError); ?></div>
                <?php endif; ?>

                <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">REFERENCE NUMBER</p>
                <p style="font-family:var(--font-heading); font-weight:700; font-size:20px; letter-spacing:1px; margin-bottom:20px;">ORD-<?php echo htmlspecialchars($order['reference_token']); ?></p>

                <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">ORDER</p>
                <p style="margin-bottom:20px;"><?php echo htmlspecialchars($order['product_name_snapshot']); ?> &times; <?php echo $order['quantity']; ?> — <strong>Rs. <?php echo number_format($order['total_amount']); ?></strong></p>

                <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">PAYMENT METHOD</p>
                <p style="margin-bottom:20px;"><?php echo paymentMethodLabel($order['payment_method']); ?></p>

                <p style="color:var(--text-muted); font-size:13px; margin-bottom:8px;">STATUS</p>
                <span class="category-badge" style="background:<?php echo $st['bg']; ?>; color:<?php echo $st['text']; ?>; font-size:14px; padding:6px 16px;"><?php echo $st['label']; ?></span>

                <?php if (in_array($order['order_status'], ['pending_payment', 'payment_review'])): ?>
                <div style="margin-top:24px; padding-top:20px; border-top:1px solid var(--border);">
                    <p style="font-weight:600; margin-bottom:10px;">
                        <?php echo $order['payment_proof_image'] ? 'Replace Payment Screenshot' : 'Upload Payment Screenshot'; ?>
                    </p>
                    <?php if ($order['payment_proof_image']): ?>
                        <img src="uploads/payment-proofs/<?php echo htmlspecialchars($order['payment_proof_image']); ?>" style="max-width:150px; border-radius:8px; margin-bottom:12px; display:block;">
                    <?php endif; ?>
                    <form method="POST" action="track-order.php?ref=<?php echo htmlspecialchars($order['reference_token']); ?>" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Screenshot</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php require 'includes/footer.php'; ?>