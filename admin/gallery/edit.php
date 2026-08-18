<?php
$pageTitle = 'Edit Gallery Image';
require '../includes/auth-check.php';
require '../../config/database.php';
require '../../includes/functions.php';

$galleryId = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM gallery WHERE gallery_id = ?");
$stmt->execute([$galleryId]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: index.php');
    exit;
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $imageName   = $item['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadError = '';
        $newImage = handleImageUpload('image', '../../uploads/gallery/', $uploadError);
        if ($newImage) { $imageName = $newImage; } else { $errorMessage = $uploadError; }
    }

    if (!$errorMessage) {
        $stmt = $pdo->prepare("UPDATE gallery SET title = ?, description = ?, image = ? WHERE gallery_id = ?");
        $stmt->execute([$title, $description, $imageName, $galleryId]);

        $extraFiles = handleMultipleImageUpload('extra_images', '../../uploads/gallery/');
        if (count($extraFiles) > 0) {
            $maxOrder = (int) $pdo->query("SELECT COALESCE(MAX(sort_order), -1) FROM gallery_images WHERE gallery_id = $galleryId")->fetchColumn();
            foreach ($extraFiles as $i => $extraFile) {
                $stmt2 = $pdo->prepare("INSERT INTO gallery_images (gallery_id, image, sort_order) VALUES (?, ?, ?)");
                $stmt2->execute([$galleryId, $extraFile, $maxOrder + 1 + $i]);
            }
        }

        header('Location: edit.php?id=' . $galleryId . '&updated=1');
        exit;
    }
}

require '../includes/admin-header.php';
?>

<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Gallery item updated successfully.</div><?php endif; ?>
<?php if ($errorMessage): ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div><?php endif; ?>

<form method="POST" action="edit.php?id=<?php echo $item['gallery_id']; ?>" enctype="multipart/form-data" style="max-width:600px;">
    <div class="form-group"><label>Title</label><input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required></div>
    <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?php echo htmlspecialchars($item['description']); ?></textarea></div>
    <div class="form-group">
        <label>Current Cover Image</label><br>
        <img src="/bismillah-shop/uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" style="width:100px; border-radius:6px; margin-bottom:10px;">
        <label>Replace Cover Image (optional)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <?php
    $existingExtra = $pdo->prepare("SELECT * FROM gallery_images WHERE gallery_id = ? ORDER BY sort_order ASC");
    $existingExtra->execute([$galleryId]);
    $existingExtra = $existingExtra->fetchAll();
    ?>
    <div class="form-group">
        <label>Extra Photos</label>
        <?php if (count($existingExtra) > 0): ?>
        <div class="admin-thumb-grid">
            <?php foreach ($existingExtra as $img): ?>
            <div class="admin-thumb-item">
                <img src="/bismillah-shop/uploads/gallery/<?php echo htmlspecialchars($img['image']); ?>">
                <a href="delete-image.php?image_id=<?php echo $img['image_id']; ?>&gallery_id=<?php echo $galleryId; ?>" 
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