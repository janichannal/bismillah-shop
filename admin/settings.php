<?php
$pageTitle = 'Branding Settings';
require 'includes/auth-check.php';
require '../config/database.php';
require '../includes/functions.php';

$settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadError = '';
        $logoName = handleImageUpload('logo', '../uploads/branding/', $uploadError);

        if ($logoName) {
            if (!empty($settings['logo_image']) && file_exists('../uploads/branding/' . $settings['logo_image'])) {
                unlink('../uploads/branding/' . $settings['logo_image']);
            }

            if ($settings) {
                $stmt = $pdo->prepare("UPDATE settings SET logo_image = ? WHERE setting_id = ?");
                $stmt->execute([$logoName, $settings['setting_id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO settings (logo_image) VALUES (?)");
                $stmt->execute([$logoName]);
            }
            $successMessage = 'Logo updated successfully.';
            $settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
        } else {
            $errorMessage = $uploadError;
        }
    } else {
        $errorMessage = 'Please select a logo image to upload.';
    }
}

require 'includes/admin-header.php';
?>

<?php if ($successMessage): ?><div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div><?php endif; ?>
<?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

<div class="card section-mint" style="max-width:500px; border:none;">
    <div class="card-body">
        <h3 style="margin-bottom:16px;">Shop Logo</h3>

        <?php if (!empty($settings['logo_image'])): ?>
            <p style="color:var(--text-muted); margin-bottom:8px; font-size:13px;">CURRENT LOGO</p>
            <div style="background:#fff; border-radius:var(--radius); padding:16px; display:inline-block; margin-bottom:20px;">
                <img src="/bismillah-shop/uploads/branding/<?php echo htmlspecialchars($settings['logo_image']); ?>" style="max-width:200px; max-height:100px; display:block;">
            </div>
        <?php else: ?>
            <p style="color:var(--text-muted); margin-bottom:20px;">No logo uploaded yet — the site is currently showing the text logo.</p>
        <?php endif; ?>

        <form method="POST" action="settings.php" enctype="multipart/form-data">
            <div class="form-group">
                <label>Upload New Logo (JPG, PNG, or WEBP — transparent PNG recommended)</label>
                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Logo</button>
        </form>
    </div>
</div>

<?php require 'includes/admin-footer.php'; ?>