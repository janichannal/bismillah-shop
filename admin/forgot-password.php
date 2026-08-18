<?php
require '../config/database.php';
require '../includes/mailer.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $errorMessage = 'Please enter your email address.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        // Always show the same success message whether or not the email exists.
        // This prevents someone from using this form to discover valid admin emails.
        $successMessage = 'If an account with that email exists, a password reset link has been sent.';

        if ($admin) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $stmt = $pdo->prepare("INSERT INTO password_resets (admin_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$admin['admin_id'], $token, $expiresAt]);

            $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . '/bismillah-shop/admin/reset-password.php?token=' . $token;

            $body = "
                <h2>Password Reset Request</h2>
                <p>Hi " . htmlspecialchars($admin['name']) . ",</p>
                <p>We received a request to reset your admin password for Bismillah Mobile & Laptop Shop.</p>
                <p><a href='$resetLink' style='background:#059669; color:#fff; padding:12px 24px; border-radius:8px; text-decoration:none; display:inline-block;'>Reset Password</a></p>
                <p>Or copy this link into your browser: $resetLink</p>
                <p>This link expires in 30 minutes. If you didn't request this, you can safely ignore this email.</p>
            ";

            sendEmail($admin['email'], $admin['name'], 'Password Reset - Bismillah Shop Admin', $body);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Forgot Password - Bismillah Shop</title>
    <link rel="stylesheet" href="/bismillah-shop/assets/css/style.css">
</head>
<body style="background-color: var(--bg-light); background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='36' height='36'%3E%3Ccircle cx='2' cy='2' r='1.6' fill='%23059669' opacity='0.22'/%3E%3C/svg%3E&quot;); display:flex; align-items:center; justify-content:center; min-height:100vh;">

    <div class="card" style="width: 400px; padding: 10px;">
        <div class="card-body">
            <h2 style="text-align:center; margin-bottom: 6px;">Forgot Password</h2>
            <p style="text-align:center; color:var(--text-muted); margin-bottom: 24px;">Enter your admin email to receive a reset link</p>

            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <?php if (!$successMessage): ?>
            <form method="POST" action="forgot-password.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Send Reset Link</button>
            </form>
            <?php endif; ?>

            <p style="text-align:center; margin-top:20px; font-size:14px;">
                <a href="login.php">Back to Login</a>
            </p>
        </div>
    </div>

</body>
</html>