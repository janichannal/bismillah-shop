<?php
session_start();
require '../config/database.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$siteSettings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();

$errorMessage = '';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errorMessage = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && $admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
            $minutesLeft = ceil((strtotime($admin['locked_until']) - time()) / 60);
            $errorMessage = "Too many failed attempts. Please try again in $minutesLeft minute(s).";
        } elseif ($admin && password_verify($password, $admin['password'])) {
            $pdo->prepare("UPDATE admins SET failed_attempts = 0, locked_until = NULL WHERE admin_id = ?")->execute([$admin['admin_id']]);
            $_SESSION['admin_id']   = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'];
            header('Location: dashboard.php');
            exit;
        } else {
            if ($admin) {
                $newAttempts = $admin['failed_attempts'] + 1;
                if ($newAttempts >= 5) {
                    $lockUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                    $pdo->prepare("UPDATE admins SET failed_attempts = ?, locked_until = ? WHERE admin_id = ?")->execute([$newAttempts, $lockUntil, $admin['admin_id']]);
                    $errorMessage = 'Too many failed attempts. Account locked for 15 minutes.';
                } else {
                    $pdo->prepare("UPDATE admins SET failed_attempts = ? WHERE admin_id = ?")->execute([$newAttempts, $admin['admin_id']]);
                    $errorMessage = 'Invalid email or password.';
                }
            } else {
                $errorMessage = 'Invalid email or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bismillah Shop</title>
    <link rel="stylesheet" href="/bismillah-shop/assets/css/style.css">
    <script src="/bismillah-shop/assets/js/password-toggle.js" defer></script>
</head>
<body style="background-color: var(--bg-light); background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='36' height='36'%3E%3Ccircle cx='2' cy='2' r='1.6' fill='%23059669' opacity='0.22'/%3E%3C/svg%3E&quot;); display:flex; align-items:center; justify-content:center; min-height:100vh;">

    <div class="card" style="width: 380px; padding: 10px;">
        <div class="card-body">

            <div style="text-align:center; margin-bottom:18px;">
                <?php if (!empty($siteSettings['logo_image'])): ?>
                    <img src="/bismillah-shop/uploads/branding/<?php echo htmlspecialchars($siteSettings['logo_image']); ?>" alt="Logo" style="width:56px; height:56px; border-radius:14px; object-fit:cover;">
                <?php else: ?>
                    <div style="width:56px; height:56px; border-radius:14px; background:var(--primary); display:flex; align-items:center; justify-content:center; margin:0 auto; color:#fff; font-family:var(--font-heading); font-weight:700; font-size:22px;">B</div>
                <?php endif; ?>
            </div>

            <h2 style="text-align:center; margin-bottom: 6px;">Admin Login</h2>
            <p style="text-align:center; color:var(--text-muted); margin-bottom: 24px;">Bismillah Mobile & Laptop Shop</p>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" data-target="password" aria-label="Show password"></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Login</button>
            </form>
            <p style="text-align:center; margin-top:16px; font-size:14px;">
                <a href="forgot-password.php">Forgot your password?</a>
            </p>
        </div>
    </div>

</body>
</html>