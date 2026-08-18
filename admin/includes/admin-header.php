<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Admin' : 'Admin'; ?> - Bismillah Shop</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="/bismillah-shop/assets/css/style.css">
    <link rel="stylesheet" href="/bismillah-shop/assets/css/admin.css">
    <script src="/bismillah-shop/assets/js/admin.js" defer></script>
    <link rel="stylesheet" href="/bismillah-shop/assets/css/chatbot.css">
    <script src="/bismillah-shop/assets/js/chatbot.js" defer></script>
    <link rel="stylesheet" href="/bismillah-shop/assets/css/slider.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="admin-layout">
<?php require __DIR__ . '/admin-sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-topbar">
        <h2><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin'; ?></h2>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
    </div>