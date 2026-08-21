<?php
$pageTitle = 'Checkout';
require 'config/database.php';
require 'config/constants.php';
require 'includes/functions.php';
require 'includes/mailer.php';

$productId = (int) ($_GET['id'] ?? $_POST['product_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

$errorMessage = '';
$orderReference = '';
$orderHadProof = false;

if ($product && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $address = trim($_POST['delivery_address'] ?? '');
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
    $paymentMethod = $_POST['payment_method'] ?? '';

    if ($name === '' || $email === '' || $phone === '' || $address === '') {
        $errorMessage = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } elseif (!in_array($paymentMethod, ['bank_transfer', 'jazzcash_easypaisa'])) {
        $errorMessage = 'Please select a payment method.';
    } elseif ($quantity > $product['stock_quantity']) {
        $errorMessage = 'Sorry, only ' . $product['stock_quantity'] . ' unit(s) available in stock.';
    } else {
        $unitPrice = ($product['sale_price'] !== null && $product['sale_price'] > 0 && $product['sale_price'] < $product['price'])
            ? $product['sale_price'] : $product['price'];
        $totalAmount = $unitPrice * $quantity;
        $refToken = generateUniqueOrderReference($pdo);

        $proofImage = null;
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadErr = '';
            $proofImage = handleImageUpload('payment_proof', 'uploads/payment-proofs/', $uploadErr);
        }
        $orderHadProof = (bool) $proofImage;
        $orderStatus = $proofImage ? 'payment_review' : 'pending_payment';

        $stmt = $pdo->prepare("INSERT INTO orders 
            (reference_token, product_id, product_name_snapshot, unit_price, quantity, total_amount, 
             customer_name, customer_email, customer_phone, delivery_address, payment_method, payment_proof_image, order_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$refToken, $productId, $product['product_name'], $unitPrice, $quantity, $totalAmount,
            $name, $email, $phone, $address, $paymentMethod, $proofImage, $orderStatus]);
        $newOrderId = $pdo->lastInsertId();

        $sentInBackground = runInBackground(__DIR__ . '/includes/send-notification-cli.php', ['order', (string) $newOrderId, $_SERVER['HTTP_HOST']]);
        if (!$sentInBackground) {
            notifyAdminsOfNewOrder($pdo, $newOrderId, $_SERVER['HTTP_HOST']);
        }

        $orderReference = $refToken;
    }
}

require 'includes/header.php';
?>

<section class="hero" style="padding: 60px 0;">
    <div class="container">
        <h1>Checkout</h1>
        <p>Complete your order below.</p>
    </div>
</section>

<?php echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
    ['label' => 'Checkout'],
]); ?>

<section class="section">
    <div class="container" style="max-width:640px;">

        <?php if (!$product): ?>
            <div style="text-align:center; padding:40px 0;">
                <h2>Product Not Found</h2>
                <p style="color:var(--text-muted); margin:12px 0 24px;">This product doesn't exist or has been removed.</p>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
            </div>

        <?php elseif ($orderReference): ?>
            <div class="card">
                <div class="card-body">
                    <h2 style="color:var(--success); margin-bottom:10px;">Order Placed Successfully!</h2>
                    <p style="color:var(--text-muted); margin-bottom:20px;">Your reference number is:</p>
                    <p style="font-family:var(--font-heading); font-weight:700; font-size:24px; letter-spacing:1px; color:var(--primary); margin-bottom:24px;">ORD-<?php echo htmlspecialchars($orderReference); ?></p>

                    <?php if ($orderHadProof): ?>
                        <div class="alert alert-success">Your payment screenshot has been submitted. We'll review it and confirm your order shortly.</div>
                    <?php else: ?>
                        <div class="alert alert-danger" style="background:#fef3c7; color:#a16207; border-color:#fde68a;">
                            Please complete your payment now, then upload your payment screenshot using your reference number on the
                            <a href="track-order.php?ref=<?php echo htmlspecialchars($orderReference); ?>" style="font-weight:600;">Track Order page</a> so we can confirm it.
                        </div>
                    <?php endif; ?>

                    <p style="margin-top:20px;"><strong>Save your reference number</strong> — you'll need it to check your order status and upload payment proof.</p>

                    <a href="track-order.php?ref=<?php echo htmlspecialchars($orderReference); ?>" class="btn btn-primary" style="margin-top:16px;">Go to Track Order</a>
                </div>
            </div>

        <?php else:
            $unitPrice = ($product['sale_price'] !== null && $product['sale_price'] > 0 && $product['sale_price'] < $product['price']) ? $product['sale_price'] : $product['price'];
        ?>

            <div class="order-summary-box" style="display:flex; gap:16px; align-items:center;">
                <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" style="width:70px; height:70px; object-fit:cover; border-radius:8px;">
                <div>
                    <h3 style="font-size:16px;"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p style="color:var(--primary); font-weight:700; font-family:var(--font-heading);">Rs. <?php echo number_format($unitPrice); ?> each</p>
                </div>
            </div>

            <?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

            <form method="POST" action="checkout.php?id=<?php echo $productId; ?>" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">

                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="customer_email" required>
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" name="customer_phone" required>
                </div>
                <div class="form-group">
                    <label>Delivery Address *</label>
                    <textarea name="delivery_address" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo max(1, $product['stock_quantity']); ?>">
                    <p style="font-size:12px; color:var(--text-muted); margin-top:4px;"><?php echo $product['stock_quantity']; ?> available in stock</p>
                </div>

                <div class="form-group">
                    <label>Payment Method *</label>

                    <div class="payment-method-option" id="option-bank">
                        <label><input type="radio" name="payment_method" value="bank_transfer" onchange="updatePaymentUI()"> Bank Transfer</label>
                        <div class="account-details-box" id="details-bank">
                            <table>
                                <tr><td>Bank</td><td><?php echo htmlspecialchars(BANK_NAME); ?></td></tr>
                                <tr><td>Account Title</td><td><?php echo htmlspecialchars(BANK_ACCOUNT_TITLE); ?></td></tr>
                                <tr><td>Account Number</td><td><strong><?php echo htmlspecialchars(BANK_ACCOUNT_NUMBER); ?></strong></td></tr>
                            </table>
                        </div>
                    </div>

                    <div class="payment-method-option" id="option-jazz">
                        <label><input type="radio" name="payment_method" value="jazzcash_easypaisa" onchange="updatePaymentUI()"> JazzCash / EasyPaisa</label>
                        <div class="account-details-box" id="details-jazz">
                            <table>
                                <tr><td>Number</td><td><strong><?php echo htmlspecialchars(JAZZCASH_EASYPAISA_NUMBER); ?></strong></td></tr>
                                <tr><td>Account Name</td><td><?php echo htmlspecialchars(JAZZCASH_EASYPAISA_NAME); ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload Payment Screenshot (optional now — you can also do this after paying, via the Track Order page)</label>
                    <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Place Order</button>
            </form>

            <script>
            function updatePaymentUI() {
                const bankRadio = document.querySelector('input[value="bank_transfer"]');
                const jazzRadio = document.querySelector('input[value="jazzcash_easypaisa"]');

                document.getElementById('option-bank').classList.toggle('selected', bankRadio.checked);
                document.getElementById('option-jazz').classList.toggle('selected', jazzRadio.checked);
                document.getElementById('details-bank').classList.toggle('active', bankRadio.checked);
                document.getElementById('details-jazz').classList.toggle('active', jazzRadio.checked);
            }
            </script>

        <?php endif; ?>

    </div>
</section>

<?php require 'includes/footer.php'; ?>