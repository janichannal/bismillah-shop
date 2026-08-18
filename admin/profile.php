<?php
$pageTitle = 'My Profile';
require 'includes/auth-check.php';
require '../config/database.php';

$stmt = $pdo->prepare("SELECT * FROM admins WHERE admin_id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

$profileError = '';
$profileSuccess = '';
$passwordError = '';
$passwordSuccess = '';

// --- Update name/email ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '') {
        $profileError = 'Name and email cannot be empty.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profileError = 'Please enter a valid email address.';
    } else {
        // Make sure no OTHER admin already uses this email
        $stmt = $pdo->prepare("SELECT admin_id FROM admins WHERE email = ? AND admin_id != ?");
        $stmt->execute([$email, $admin['admin_id']]);
        if ($stmt->fetch()) {
            $profileError = 'That email is already in use by another admin account.';
        } else {
            $pdo->prepare("UPDATE admins SET name = ?, email = ? WHERE admin_id = ?")
                ->execute([$name, $email, $admin['admin_id']]);
            $_SESSION['admin_name'] = $name;
            $profileSuccess = 'Profile updated successfully.';
            $admin['name'] = $name;
            $admin['email'] = $email;
        }
    }
}

// --- Change password ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = trim($_POST['current_password'] ?? '');
    $newPassword     = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (!password_verify($currentPassword, $admin['password'])) {
        $passwordError = 'Your current password is incorrect.';
    } elseif (strlen($newPassword) < 6) {
        $passwordError = 'New password must be at least 6 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $passwordError = 'New passwords do not match.';
    } else {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE admins SET password = ? WHERE admin_id = ?")->execute([$hashed, $admin['admin_id']]);
        $passwordSuccess = 'Password changed successfully.';
    }
}

require 'includes/admin-header.php';
?>
<script src="/bismillah-shop/assets/js/password-toggle.js" defer></script>

<div class="two-col-grid">

    <div class="card">
        <div class="card-body">
            <h3 style="margin-bottom:18px;">Profile Information</h3>

            <?php if ($profileSuccess): ?><div class="alert alert-success"><?php echo htmlspecialchars($profileSuccess); ?></div><?php endif; ?>
            <?php if ($profileError): ?><div class="alert alert-danger"><?php echo htmlspecialchars($profileError); ?></div><?php endif; ?>

            <form method="POST" action="profile.php">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 style="margin-bottom:18px;">Change Password</h3>

            <?php if ($passwordSuccess): ?><div class="alert alert-success"><?php echo htmlspecialchars($passwordSuccess); ?></div><?php endif; ?>
            <?php if ($passwordError): ?><div class="alert alert-danger"><?php echo htmlspecialchars($passwordError); ?></div><?php endif; ?>

            <form method="POST" action="profile.php">
                <input type="hidden" name="change_password" value="1">
                <div class="form-group">
                    <label>Current Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="current_password" name="current_password" required>
                        <button type="button" class="toggle-password" data-target="current_password" aria-label="Show password"></button>
                    </div>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new_password" name="new_password" required minlength="6">
                        <button type="button" class="toggle-password" data-target="new_password" aria-label="Show password"></button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                        <button type="button" class="toggle-password" data-target="confirm_password" aria-label="Show password"></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>

</div>

<?php require 'includes/admin-footer.php'; ?>