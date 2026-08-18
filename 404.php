<?php
$pageTitle = 'Page Not Found';
$pageDescription = 'The page you are looking for could not be found.';
require 'includes/header.php';
?>

<section class="section" style="padding: 100px 0; text-align:center;">
    <div class="container">
        <div style="font-family: var(--font-heading); font-size: 100px; font-weight:700; color: var(--primary-light); line-height:1;">404</div>
        <h2 style="margin-top:10px;">This page wandered off somewhere</h2>
        <p style="color:var(--text-muted); max-width:480px; margin: 12px auto 30px;">
            The page you're looking for doesn't exist, may have been moved, or the link might be broken.
        </p>
        <a href="/bismillah-shop/index.php" class="btn btn-primary">Back to Home</a>
        <a href="/bismillah-shop/products.php" class="btn btn-outline">Browse Products</a>
    </div>
</section>

<?php require 'includes/footer.php'; ?>