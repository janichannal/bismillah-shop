<?php
$pageTitle = 'Services';
require '../includes/auth-check.php';
require '../../config/database.php';

$services = $pdo->query("SELECT s.*, 
    (SELECT COUNT(*) FROM service_images si WHERE si.service_id = s.service_id) as photo_count 
    FROM services s ORDER BY s.created_at DESC")->fetchAll();

require '../includes/admin-header.php';
?>

<div style="margin-bottom:20px;">
    <a href="add.php" class="btn btn-primary">+ Add Service</a>
</div>

<?php if (isset($_GET['added'])): ?><div class="alert alert-success">Service added successfully.</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Service updated successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Service deleted successfully.</div><?php endif; ?>

<div class="table-responsive">
<table class="admin-table">
    <tr><th>Image</th><th>Name</th><th>Description</th><th>Photos</th><th>Price</th><th>Actions</th></tr>
    <?php foreach ($services as $s): ?>
    <tr>
        <td><img src="/bismillah-shop/uploads/services/<?php echo htmlspecialchars($s['image']); ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;"></td>
        <td><?php echo htmlspecialchars($s['service_name']); ?></td>
        <td><?php echo htmlspecialchars(substr($s['description'], 0, 50)); ?>...</td>
        <td><span class="category-badge" style="background:var(--primary-light); color:var(--primary-dark);"><?php echo (int)$s['photo_count'] + 1; ?> photo<?php echo $s['photo_count'] != 0 ? 's' : ''; ?></span></td>
        <td>Rs. <?php echo number_format($s['price']); ?></td>
        <td>
            <a href="edit.php?id=<?php echo $s['service_id']; ?>">Edit</a> |
            <a href="delete.php?id=<?php echo $s['service_id']; ?>" onclick="return confirm('Delete this service?');" style="color:var(--danger);">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>

<?php require '../includes/admin-footer.php'; ?>