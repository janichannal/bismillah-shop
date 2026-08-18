<?php
$pageTitle = 'Upload Gallery Image';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $uploadError = '';
    $imageName = handleImageUpload('image', '../../uploads/gallery/', $uploadError);

    if (!$imageName) {
        $errorMessage = ($uploadError === 'no_file') ? 'Please select a cover image to upload.' : $uploadError;
    } else {
        $stmt = $pdo->prepare("INSERT INTO gallery (title, description, image) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $imageName]);
        $newGalleryId = $pdo->lastInsertId();

        $extraFiles = handleMultipleImageUpload('extra_images', '../../uploads/gallery/');
        foreach ($extraFiles as $order => $extraFile) {
            $stmt2 = $pdo->prepare("INSERT INTO gallery_images (gallery_id, image, sort_order) VALUES (?, ?, ?)");
            $stmt2->execute([$newGalleryId, $extraFile, $order]);
        }

        header('Location: index.php?added=1');
        exit;
    }
}

require '../includes/admin-header.php';
?>

<?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

<form method="POST" action="add.php" enctype="multipart/form-data" style="max-width:600px;">
    <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
    <div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
    <div class="form-group">
        <label>Cover Image (JPG, PNG, or WEBP)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
    </div>
    <div class="form-group">
        <label>Extra Photos (optional — hold Ctrl to pick several)</label>
        <input type="file" name="extra_images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
    <a href="index.php" class="btn btn-outline">Cancel</a>
</form>

<?php require '../includes/admin-footer.php'; ?>