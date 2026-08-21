<?php $siteSettings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch(); ?>
<aside class="admin-sidebar">
    <div class="admin-logo">
        <?php if (!empty($siteSettings['logo_image'])): ?>
            <span style="display:inline-flex; align-items:center; gap:8px;">
                <img src="/bismillah-shop/uploads/branding/<?php echo htmlspecialchars($siteSettings['logo_image']); ?>" alt="Logo" class="site-logo-img" style="height:32px; width:32px;">
                <span>Bismillah<span style="color:var(--accent);">Admin</span></span>
            </span>
        <?php else: ?>
            Bismillah<span>Admin</span>
        <?php endif; ?>
    </div>
    <button class="admin-nav-toggle" aria-label="Toggle menu">&#9776;</button>
    <nav class="admin-nav">
        <a href="/bismillah-shop/admin/dashboard.php" class="admin-nav-link">Dashboard</a>
        <a href="/bismillah-shop/admin/products/index.php" class="admin-nav-link">Products</a>
        <a href="/bismillah-shop/admin/services/index.php" class="admin-nav-link">Services</a>
        <a href="/bismillah-shop/admin/gallery/index.php" class="admin-nav-link">Gallery</a>
        <a href="/bismillah-shop/admin/messages/index.php" class="admin-nav-link">Messages</a>
        <a href="/bismillah-shop/admin/orders/index.php" class="admin-nav-link">Orders</a>
        <a href="/bismillah-shop/admin/reviews/index.php" class="admin-nav-link">Reviews</a>
        <a href="/bismillah-shop/admin/optimize-images.php" class="admin-nav-link">Optimize Images</a>
        <a href="/bismillah-shop/admin/settings.php" class="admin-nav-link">Settings</a>
        <a href="/bismillah-shop/admin/profile.php" class="admin-nav-link">My Profile</a>
        <a href="/bismillah-shop/admin/logout.php" class="admin-nav-link" style="color:#fca5a5;">Logout</a>
    </nav>
</aside>