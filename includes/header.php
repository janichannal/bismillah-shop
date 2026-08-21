<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Bismillah Mobile & Laptop Shop' : 'Bismillah Mobile & Laptop Shop'; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(isset($pageDescription) ? $pageDescription : 'Bismillah Mobile & Laptop Shop - genuine mobile phones, laptops, tablets, accessories, and expert repair services in Khuzdar, Balochistan.'); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars(isset($pageTitle) ? $pageTitle . ' - Bismillah Mobile & Laptop Shop' : 'Bismillah Mobile & Laptop Shop'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(isset($pageDescription) ? $pageDescription : 'Genuine mobile phones, laptops, tablets, accessories, and expert repair services in Khuzdar, Balochistan.'); ?>">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="/bismillah-shop/assets/css/style.css">
    <link rel="stylesheet" href="/bismillah-shop/assets/css/chatbot.css">
    <link rel="stylesheet" href="/bismillah-shop/assets/css/slider.css">
    <link rel="stylesheet" href="/bismillah-shop/assets/css/whatsapp.css">
    <script src="/bismillah-shop/assets/js/main.js" defer></script>
    <script src="/bismillah-shop/assets/js/chatbot.js" defer></script>
    <script src="/bismillah-shop/assets/js/slider.js" defer></script>
</head>
<body>

<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
$siteSettings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
$waMessage = isset($whatsappMessage) ? $whatsappMessage : 'Hi, I have a question about your products or services.';
$waLink = 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . urlencode($waMessage);
?>

<nav class="navbar">
    <div class="container">
        <a href="/bismillah-shop/index.php" class="logo">
            <?php if (!empty($siteSettings['logo_image'])): ?>
                <span style="display:inline-flex; align-items:center; gap:10px;">
                    <img src="/bismillah-shop/uploads/branding/<?php echo htmlspecialchars($siteSettings['logo_image']); ?>" alt="Bismillah Mobile & Laptop Shop" class="site-logo-img">
                    <span>Bismillah<span style="color:var(--primary);">Shop</span></span>
                </span>
            <?php else: ?>
                Bismillah<span>Shop</span>
            <?php endif; ?>
        </a>
        <ul>
            <li><a href="/bismillah-shop/index.php">Home</a></li>
            <li><a href="/bismillah-shop/products.php">Products</a></li>
            <li><a href="/bismillah-shop/services.php">Services</a></li>
            <li><a href="/bismillah-shop/gallery.php">Gallery</a></li>
            <li><a href="/bismillah-shop/about.php">About</a></li>
            <li><a href="/bismillah-shop/contact.php">Contact</a></li>
            <li><a href="/bismillah-shop/admin/login.php" class="btn btn-outline" style="padding:8px 18px; font-size:13px;">Admin Login</a></li>
        </ul>
        <button class="nav-toggle">&#9776;</button>
    </div>
</nav>

<a href="<?php echo htmlspecialchars($waLink); ?>" target="_blank" rel="noopener noreferrer" class="whatsapp-fab" aria-label="Chat with us on WhatsApp">
    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.35 5.07L2 22l5.07-1.32A9.94 9.94 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm5.2 14.2c-.22.62-1.28 1.2-1.77 1.24-.47.05-.9.22-3.03-.63-2.56-1.03-4.2-3.6-4.33-3.77-.13-.17-1.03-1.37-1.03-2.6s.65-1.85.88-2.1c.22-.25.5-.3.66-.3.17 0 .33 0 .48.01.15.01.36-.06.56.43.22.53.75 1.83.82 1.96.07.13.11.28.02.45-.09.17-.13.28-.26.43-.13.15-.28.34-.4.46-.13.13-.27.27-.12.53.15.26.68 1.12 1.46 1.81 1 .89 1.85 1.17 2.11 1.3.26.13.41.11.56-.07.15-.18.65-.76.82-1.02.17-.26.35-.22.58-.13.24.09 1.5.71 1.76.84.26.13.43.19.5.3.06.11.06.63-.16 1.25z"/></svg>
</a>