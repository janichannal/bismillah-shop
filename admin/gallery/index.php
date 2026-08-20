<?php
$pageTitle = 'Gallery';
require '../includes/auth-check.php';
require '../../config/database.php';

$galleryImages = $pdo->query("SELECT g.*, 
    (SELECT COUNT(*) FROM gallery_images gi WHERE gi.gallery_id = g.gallery_id) as photo_count 
    FROM gallery g ORDER BY g.created_at DESC")->fetchAll();

require '../includes/admin-header.php';
?>

<div style="margin-bottom:20px;">
    <a href="add.php" class="btn btn-primary">+ Upload Gallery Image</a>
</div>

<?php if (isset($_GET['added'])): ?><div class="alert alert-success">Image uploaded successfully.</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Image updated successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Image deleted successfully.</div><?php endif; ?>

<div class="desktop-table-only">
<div class="table-responsive">
<table class="admin-table">
    <tr><th>Image</th><th>Title</th><th>Description</th><th>Photos</th><th>Actions</th></tr>
    <?php foreach ($galleryImages as $g): ?>
    <tr>
        <td><img src="/bismillah-shop/uploads/gallery/<?php echo htmlspecialchars($g['image']); ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;"></td>
        <td><?php echo htmlspecialchars($g['title']); ?></td>
        <td><?php echo htmlspecialchars($g['description']); ?></td>
        <td><span class="category-badge" style="background:#ccfbf1; color:#0f766e;"><?php echo (int)$g['photo_count'] + 1; ?> photo<?php echo $g['photo_count'] != 0 ? 's' : ''; ?></span></td>
        <td>
            <a href="edit.php?id=<?php echo $g['gallery_id']; ?>">Edit</a> |
            <a href="delete.php?id=<?php echo $g['gallery_id']; ?>" onclick="return confirm('Delete this image?');" style="color:var(--danger);">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
</div>

<div class="mobile-list-only">
<div class="recent-card">
    <?php if (count($galleryImages) === 0): ?><div class="empty-row">No gallery items yet.</div><?php endif; ?>
    <?php foreach ($galleryImages as $g): ?>
    <div class="recent-list-item wrap-actions">
        <img src="/bismillah-shop/uploads/gallery/<?php echo htmlspecialchars($g['image']); ?>" class="recent-thumb">
        <div class="recent-content">
            <div class="recent-title"><?php echo htmlspecialchars($g['title']); ?></div>
            <div class="recent-subtitle"><?php echo htmlspecialchars(substr($g['description'], 0, 40)); ?></div>
        </div>
        <div class="recent-meta">
            <span class="category-badge" style="background:#ccfbf1; color:#0f766e; font-size:11px;"><?php echo (int)$g['photo_count'] + 1; ?> photos</span>
        </div>
        <div class="mobile-item-actions">
            <a href="edit.php?id=<?php echo $g['gallery_id']; ?>">Edit</a>
            <a href="delete.php?id=<?php echo $g['gallery_id']; ?>" onclick="return confirm('Delete this image?');" class="danger-link">Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>

<?php require '../includes/admin-footer.php'; ?>