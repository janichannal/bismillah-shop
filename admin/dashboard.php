<?php
$pageTitle = 'Dashboard';
require 'includes/auth-check.php';
require '../config/database.php';

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalServices = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
$totalGallery  = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$pendingReviews = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn();

$recentProducts = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentMessages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

$messagesPerDay = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $messagesPerDay[$day] = 0;
}
$rows = $pdo->query("SELECT DATE(created_at) as day, COUNT(*) as count FROM messages 
                      WHERE created_at >= CURDATE() - INTERVAL 6 DAY 
                      GROUP BY DATE(created_at)")->fetchAll();
foreach ($rows as $row) {
    if (isset($messagesPerDay[$row['day']])) {
        $messagesPerDay[$row['day']] = (int) $row['count'];
    }
}
$chartLabels = [];
foreach (array_keys($messagesPerDay) as $day) {
    $chartLabels[] = date('D', strtotime($day));
}
$chartValues = array_values($messagesPerDay);

$topPriced = $pdo->query("SELECT product_name, price FROM products ORDER BY price DESC LIMIT 5")->fetchAll();
$topPricedLabels = array_column($topPriced, 'product_name');
$topPricedValues = array_column($topPriced, 'price');

$lowStockProducts = $pdo->query("SELECT product_id, product_name, stock_quantity FROM products WHERE stock_quantity < 5 ORDER BY stock_quantity ASC")->fetchAll();

require 'includes/admin-header.php';
?>

    <div class="stats-grid">
        <div class="stat-card accent-green">
            <div class="stat-number"><?php echo $totalProducts; ?></div>
            <div class="stat-label">Total Products</div>
        </div>
        <div class="stat-card accent-gold">
            <div class="stat-number"><?php echo $totalServices; ?></div>
            <div class="stat-label">Total Services</div>
        </div>
        <div class="stat-card accent-teal">
            <div class="stat-number"><?php echo $totalGallery; ?></div>
            <div class="stat-label">Gallery Images</div>
        </div>
        <div class="stat-card accent-blue">
            <div class="stat-number"><?php echo $totalMessages; ?></div>
            <div class="stat-label">Messages</div>
        </div>
        <div class="stat-card accent-pink">
            <div class="stat-number"><?php echo $pendingReviews; ?></div>
            <div class="stat-label">Pending Reviews</div>
        </div>
    </div>

    <div style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 14px;">Quick Actions</h3>
        <a href="products/add.php" class="btn btn-primary">Add Product</a>
        <a href="services/add.php" class="btn btn-primary">Add Service</a>
        <a href="gallery/add.php" class="btn btn-primary">Upload Gallery Image</a>
        <a href="messages/index.php" class="btn btn-outline">View Messages</a>
        <a href="reviews/index.php" class="btn btn-outline">View Reviews</a>
    </div>

    <?php if (count($lowStockProducts) > 0): ?>
    <div class="card" style="margin-bottom:30px; border-left: 4px solid var(--danger); border-radius: 0 var(--radius) var(--radius) 0;">
        <div class="card-body">
            <h3 style="margin-bottom:14px; color:var(--danger);">Low Stock Warning</h3>
            <div class="table-responsive">
            <table class="admin-table">
                <tr><th>Product</th><th>Stock Left</th><th>Action</th></tr>
                <?php foreach ($lowStockProducts as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                    <td style="color:var(--danger); font-weight:700;"><?php echo $p['stock_quantity']; ?></td>
                    <td><a href="products/edit.php?id=<?php echo $p['product_id']; ?>">Restock</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="two-col-grid" style="margin-bottom:30px;">
        <div class="card">
            <div class="card-body">
                <h3 style="margin-bottom:14px;">Messages This Week</h3>
                <div style="height:220px;">
                    <canvas id="messagesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3 style="margin-bottom:14px;">Top 5 Highest-Priced Products</h3>
                <div style="height:220px;">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

   <div class="two-col-grid">
        <div class="recent-card">
            <div class="recent-card-header">
                <h3>Recent Products</h3>
                <a href="products/index.php">View all</a>
            </div>
            <?php if (count($recentProducts) === 0): ?>
                <div class="empty-row">No products yet.</div>
            <?php endif; ?>
            <?php foreach ($recentProducts as $p): ?>
            <a href="products/edit.php?id=<?php echo $p['product_id']; ?>" class="recent-list-item" style="text-decoration:none; color:inherit;">
                <img src="/bismillah-shop/uploads/products/<?php echo htmlspecialchars($p['image']); ?>" class="recent-thumb">
                <div class="recent-content">
                    <div class="recent-title"><?php echo htmlspecialchars($p['product_name']); ?></div>
                    <div class="recent-subtitle"><?php echo htmlspecialchars($p['brand']); ?></div>
                </div>
                <div class="recent-meta">
                    <div class="recent-value">Rs. <?php echo number_format($p['price']); ?></div>
                    <span class="stock-pill <?php echo $p['stock_quantity'] < 5 ? 'stock-low' : 'stock-healthy'; ?>">
                        <?php echo $p['stock_quantity']; ?> in stock
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="recent-card">
            <div class="recent-card-header">
                <h3>Recent Messages</h3>
                <a href="messages/index.php">View all</a>
            </div>
            <?php if (count($recentMessages) === 0): ?>
                <div class="empty-row">No messages yet.</div>
            <?php endif; ?>
            <?php foreach ($recentMessages as $m): ?>
            <a href="messages/view.php?id=<?php echo $m['message_id']; ?>" class="recent-list-item" style="text-decoration:none; color:inherit;">
                <div class="avatar-circle" style="background:<?php echo $m['status'] === 'unread' ? 'var(--success)' : '#94a3b8'; ?>;">
                    <?php echo strtoupper(substr($m['name'], 0, 1)); ?>
                </div>
                <div class="recent-content">
                    <div class="recent-title"><?php echo htmlspecialchars($m['name']); ?></div>
                    <div class="recent-subtitle"><?php echo htmlspecialchars($m['subject'] ?: '(No subject)'); ?></div>
                </div>
                <div class="recent-meta">
                    <?php if ($m['status'] === 'unread'): ?>
                        <span class="category-badge" style="background:var(--accent-light); color:var(--accent-dark);">Unread</span>
                    <?php else: ?>
                        <span class="category-badge" style="background:#f1f5f9; color:#475569;">Read</span>
                    <?php endif; ?>
                    <div class="recent-sub"><?php echo date('d M', strtotime($m['created_at'])); ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
<script>
new Chart(document.getElementById('messagesChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'Messages',
            data: <?php echo json_encode($chartValues); ?>,
            borderColor: '#059669',
            backgroundColor: 'rgba(5, 150, 105, 0.15)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#059669'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($topPricedLabels); ?>,
        datasets: [{
            label: 'Price (Rs.)',
            data: <?php echo json_encode($topPricedValues); ?>,
            backgroundColor: '#eab308'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<?php require 'includes/admin-footer.php'; ?>