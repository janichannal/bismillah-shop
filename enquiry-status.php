<?php
$pageTitle = 'Check Enquiry Status';
$pageDescription = 'Check the status of your enquiry to Bismillah Mobile & Laptop Shop using your reference number.';
require 'config/database.php';
require 'includes/functions.php';

$searchedRef = trim($_GET['ref'] ?? '');
$result = null;
$notFound = false;

if ($searchedRef !== '') {
    $cleanToken = strtoupper(preg_replace('/[^A-Z0-9]/i', '', str_ireplace('ENQ', '', $searchedRef)));
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE reference_token = ?");
    $stmt->execute([$cleanToken]);
    $result = $stmt->fetch();
    if (!$result) {
        $notFound = true;
    }
}

require 'includes/header.php';
?>

<section class="hero" style="padding: 60px 0;">
    <div class="container">
        <h1>Check Enquiry Status</h1>
        <p>Enter your reference number to see the current status of your message.</p>
    </div>
</section>

<?php echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
    ['label' => 'Enquiry Status'],
]); ?>

<section class="section">
    <div class="container" style="max-width:560px;">

        <form method="GET" action="enquiry-status.php" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:30px;">
            <input type="text" name="ref" placeholder="e.g. ENQ-7F3K9A" value="<?php echo htmlspecialchars($searchedRef); ?>" style="flex:1; min-width:180px; padding:12px; border:1px solid var(--border); border-radius:var(--radius); font-size:15px; text-transform:uppercase;">
            <button type="submit" class="btn btn-primary">Check Status</button>
        </form>

        <?php if ($notFound): ?>
            <div class="alert alert-danger">We couldn't find an enquiry with that reference number. Please double-check and try again.</div>
        <?php endif; ?>

        <?php if ($result): ?>
        <div class="card">
            <div class="card-body">
                <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">REFERENCE NUMBER</p>
                <p style="font-family:var(--font-heading); font-weight:700; font-size:20px; letter-spacing:1px; margin-bottom:20px;">ENQ-<?php echo htmlspecialchars($result['reference_token']); ?></p>

                <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">SUBJECT</p>
                <p style="font-weight:600; margin-bottom:20px;"><?php echo htmlspecialchars($result['subject'] ?: '(No subject)'); ?></p>

                <p style="color:var(--text-muted); font-size:13px; margin-bottom:4px;">SUBMITTED</p>
                <p style="margin-bottom:20px;"><?php echo date('d M Y, h:i A', strtotime($result['created_at'])); ?></p>

                <p style="color:var(--text-muted); font-size:13px; margin-bottom:8px;">STATUS</p>
                <?php
                $statusMap = [
                    'pending'     => ['label' => 'Pending', 'bg' => '#fef3c7', 'text' => '#a16207'],
                    'in_progress' => ['label' => 'In Progress', 'bg' => '#dbeafe', 'text' => '#1e40af'],
                    'resolved'    => ['label' => 'Resolved', 'bg' => '#d1fae5', 'text' => '#047857'],
                ];
                $st = $statusMap[$result['customer_status']] ?? $statusMap['pending'];
                ?>
                <span class="category-badge" style="background:<?php echo $st['bg']; ?>; color:<?php echo $st['text']; ?>; font-size:14px; padding:6px 16px;"><?php echo $st['label']; ?></span>

                <?php if ($result['customer_status'] === 'pending'): ?>
                    <p style="color:var(--text-muted); font-size:14px; margin-top:20px;">We've received your message and haven't started reviewing it yet.</p>
                <?php elseif ($result['customer_status'] === 'in_progress'): ?>
                    <p style="color:var(--text-muted); font-size:14px; margin-top:20px;">We're currently looking into your enquiry.</p>
                <?php else: ?>
                    <p style="color:var(--text-muted); font-size:14px; margin-top:20px;">This enquiry has been resolved. Feel free to contact us again if you need anything else.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php require 'includes/footer.php'; ?>