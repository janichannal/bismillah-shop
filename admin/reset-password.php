<?php
require '../config/database.php';

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$errorMessage = '';
$successMessage = '';
$validToken = false;
$reset = null;

if ($token !== '') {
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();
    $validToken = (bool) $reset;
}

if ($validToken && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (strlen($newPassword) < 6) {
        $errorMessage = 'Password must be at least 6 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = 'Passwords do not match.';
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE admins SET password = ?, failed_attempts = 0, locked_until = NULL WHERE admin_id = ?")
            ->execute([$hashedPassword, $reset['admin_id']]);
        $pdo->prepare("UPDATE password_resets SET used = 1 WHERE reset_id = ?")->execute([$reset['reset_id']]);
        $successMessage = 'Your password has been reset successfully. You can now log in.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Reset Password - Bismillah Shop</title>
    <link rel="stylesheet" href="/bismillah-shop/assets/css/style.css">
    <script src="/bismillah-shop/assets/js/password-toggle.js" defer></script>
</head>
<body style="background: var(--bg-light); display:flex; align-items:center; justify-content:center; min-height:100vh;">

    <div class="card" style="width: 400px; padding: 10px;">
        <div class="card-body">
            <h2 style="text-align:center; margin-bottom: 20px;">Reset Password</h2>

            <?php if (!$validToken): ?>
                <div class="alert alert-danger">This reset link is invalid or has expired. Please request a new one.</div>
                <p style="text-align:center; margin-top:16px;"><a href="forgot-password.php" class="btn btn-primary" style="width:100%; display:block; text-align:center;">Request New Link</a></p>

            <?php elseif ($successMessage): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
                <a href="login.php" class="btn btn-primary" style="width:100%; display:block; text-align:center; margin-top:10px;">Go to Login</a>

            <?php else: ?>
                <?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

                <form method="POST" action="reset-password.php">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="new_password" name="new_password" required minlength="6">
                            <button type="button" class="toggle-password" data-target="new_password" aria-label="Show password"></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                            <button type="button" class="toggle-password" data-target="confirm_password" aria-label="Show password"></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>