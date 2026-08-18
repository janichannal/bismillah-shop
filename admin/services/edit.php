<?php
$pageTitle = 'Edit Service';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';

$serviceId = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->execute([$serviceId]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: index.php');
    exit;
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceName = trim($_POST['service_name'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($serviceName === '' || $price <= 0) {
        $errorMessage = 'Please fill in service name and a valid price.';
    } else {
        $imageName = $service['image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadError = '';
            $newImage = handleImageUpload('image', '../../uploads/services/', $uploadError);
            if ($newImage) { $imageName = $newImage; } else { $errorMessage = $uploadError; }
        }

        if (!$errorMessage) {
            $stmt = $pdo->prepare("UPDATE services SET service_name = ?, description = ?, price = ?, image = ? WHERE service_id = ?");
            $stmt->execute([$serviceName, $description, $price, $imageName, $serviceId]);

            $extraFiles = handleMultipleImageUpload('extra_images', '../../uploads/services/');
            if (count($extraFiles) > 0) {
                $maxOrder = (int) $pdo->query("SELECT COALESCE(MAX(sort_order), -1) FROM service_images WHERE service_id = $serviceId")->fetchColumn();
                foreach ($extraFiles as $i => $extraFile) {
                    $stmt2 = $pdo->prepare("INSERT INTO service_images (service_id, image, sort_order) VALUES (?, ?, ?)");
                    $stmt2->execute([$serviceId, $extraFile, $maxOrder + 1 + $i]);
                }
            }

            header('Location: edit.php?id=' . $serviceId . '&updated=1');
            exit;
        }
    }
}

require '../includes/admin-header.php';
?>

<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Service updated successfully.</div><?php endif; ?>
<?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

<form method="POST" action="edit.php?id=<?php echo $service['service_id']; ?>" enctype="multipart/form-data" style="max-width:600px;">
    <div class="form-group"><label>Service Name</label><input type="text" name="service_name" value="<?php echo htmlspecialchars($service['service_name']); ?>" required></div>
    <div class="form-group"><label>Price (Rs.)</label><input type="number" step="0.01" name="price" value="<?php echo $service['price']; ?>" required></div>
    <div class="form-group"><label>Description</label><textarea name="description" rows="4"><?php echo htmlspecialchars($service['description']); ?></textarea></div>
    <div class="form-group">
        <label>Current Main Image</label><br>
        <img src="/bismillah-shop/uploads/services/<?php echo htmlspecialchars($service['image']); ?>" style="width:100px; border-radius:6px; margin-bottom:10px;">
        <label>Replace Main Image (optional)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <?php
    $existingExtra = $pdo->prepare("SELECT * FROM service_images WHERE service_id = ? ORDER BY sort_order ASC");
    $existingExtra->execute([$serviceId]);
    $existingExtra = $existingExtra->fetchAll();
    ?>
    <div class="form-group">
        <label>Extra Photos</label>
        <?php if (count($existingExtra) > 0): ?>
        <div class="admin-thumb-grid">
            <?php foreach ($existingExtra as $img): ?>
            <div class="admin-thumb-item">
                <img src="/bismillah-shop/uploads/services/<?php echo htmlspecialchars($img['image']); ?>">
                <a href="delete-image.php?image_id=<?php echo $img['image_id']; ?>&service_id=<?php echo $serviceId; ?>" 
                   class="admin-thumb-delete" onclick="return confirm('Remove this photo?');">&times;</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color:var(--text-muted); font-size:14px; margin-bottom:10px;">No extra photos yet.</p>
        <?php endif; ?>

        <label style="margin-top:14px; display:block;">Add More Photos</label>
        <input type="file" name="extra_images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="index.php" class="btn btn-outline">Cancel</a>
</form>

<?php require '../includes/admin-footer.php'; ?>