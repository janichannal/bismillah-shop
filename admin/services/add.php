<?php
$pageTitle = 'Add Service';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceName = trim($_POST['service_name'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($serviceName === '' || $price <= 0) {
        $errorMessage = 'Please fill in service name and a valid price.';
    } else {
        $uploadError = '';
        $imageName = handleImageUpload('image', '../../uploads/services/', $uploadError);

        if (!$imageName) {
            $errorMessage = ($uploadError === 'no_file') ? 'Please select a main service image.' : $uploadError;
        } else {
            $stmt = $pdo->prepare("INSERT INTO services (service_name, description, price, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$serviceName, $description, $price, $imageName]);
            $newServiceId = $pdo->lastInsertId();

            $extraFiles = handleMultipleImageUpload('extra_images', '../../uploads/services/');
            foreach ($extraFiles as $order => $extraFile) {
                $stmt2 = $pdo->prepare("INSERT INTO service_images (service_id, image, sort_order) VALUES (?, ?, ?)");
                $stmt2->execute([$newServiceId, $extraFile, $order]);
            }

            header('Location: index.php?added=1');
            exit;
        }
    }
}

require '../includes/admin-header.php';
?>

<?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

<form method="POST" action="add.php" enctype="multipart/form-data" style="max-width:600px;">
    <div class="form-group"><label>Service Name</label><input type="text" name="service_name" required></div>
    <div class="form-group"><label>Price (Rs.)</label><input type="number" step="0.01" name="price" required></div>
    <div class="form-group"><label>Description</label><textarea name="description" rows="4"></textarea></div>
    <div class="form-group">
        <label>Main Service Image (JPG, PNG, or WEBP)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
    </div>
    <div class="form-group">
        <label>Extra Photos (optional — hold Ctrl to pick several)</label>
        <input type="file" name="extra_images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
    </div>
    <button type="submit" class="btn btn-primary">Add Service</button>
    <a href="index.php" class="btn btn-outline">Cancel</a>
</form>

<?php require '../includes/admin-footer.php'; ?>