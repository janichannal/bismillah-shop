<?php
$pageTitle = 'About Us';
$pageDescription = 'Learn about Bismillah Mobile & Laptop Shop - a trusted local electronics business in Khuzdar offering genuine products and honest repair services.';
require 'includes/functions.php';
require 'includes/header.php';
?>

<section class="hero" style="padding: 60px 0;">
    <div class="container">
        <h1>About Bismillah Mobile & Laptop Shop</h1>
        <p>Your trusted technology partner in Khuzdar, since day one.</p>
    </div>
</section>

<?php echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
    ['label' => 'About'],
]); ?>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Our Story</h2>
        </div>
        <p style="max-width: 800px; margin: 0 auto; text-align: center; color: var(--text-muted);">
            Bismillah Mobile & Laptop Shop started as a small repair counter in Khuzdar, built on one simple
            idea: give local customers honest prices and real technical help, without having to travel to a
            bigger city. Over the years, we've grown into a full electronics shop — selling phones, laptops,
            and accessories, while still doing what we started with: fixing devices right, the first time.
        </p>
    </div>
</section>

<section class="section section-mint">
    <div class="container">
        <div class="grid">
            <div class="card">
                <div class="card-body">
                    <h3>Our Mission</h3>
                    <p style="color: var(--text-muted); margin-top: 8px;">
                        To provide the Khuzdar community with genuine technology products and reliable repair
                        services at fair prices, backed by honest advice.
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h3>Our Vision</h3>
                    <p style="color: var(--text-muted); margin-top: 8px;">
                        To be the most trusted name in mobile and laptop sales and service across Balochistan,
                        known for quality and integrity.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-cream">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose Us</h2>
        </div>
        <div class="grid">
            <div class="card"><div class="card-body">
                <h3>Genuine Products Only</h3>
                <p style="color:var(--text-muted);">We never sell counterfeit or unverified devices.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <h3>Skilled Technicians</h3>
                <p style="color:var(--text-muted);">Our repair team has years of real hands-on experience.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <h3>Transparent Pricing</h3>
                <p style="color:var(--text-muted);">You'll always know the cost before we start any work.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <h3>Community Focused</h3>
                <p style="color:var(--text-muted);">We're proud to be a local Khuzdar business, for the people here.</p>
            </div></div>
        </div>
    </div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="grid" style="text-align:center;">
            <div><h2 style="color:var(--accent); font-size:36px;">500+</h2><p style="color:#94a3b8;">Happy Customers</p></div>
            <div><h2 style="color:var(--accent); font-size:36px;">1000+</h2><p style="color:#94a3b8;">Devices Repaired</p></div>
            <div><h2 style="color:var(--accent); font-size:36px;">5+</h2><p style="color:#94a3b8;">Years in Business</p></div>
            <div><h2 style="color:var(--accent); font-size:36px;">100%</h2><p style="color:#94a3b8;">Genuine Products</p></div>
        </div>
    </div>
</section>

<section class="section" style="text-align: center;">
    <div class="container">
        <h2>Visit Us Today</h2>
        <p style="color:var(--text-muted); margin: 12px 0 24px;">Come see our products in person or reach out with any questions.</p>
        <a href="contact.php" class="btn btn-primary">Contact Us</a>
    </div>
</section>

<?php require 'includes/footer.php'; ?>