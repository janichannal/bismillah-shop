<?php
$pageTitle = 'Optimize Images';
require 'includes/auth-check.php';
require '../config/database.php';
require '../includes/functions.php';

$results = [];
$totalBefore = 0;
$totalAfter = 0;
$ran = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_optimize'])) {
    $ran = true;
    $folders = ['products', 'services', 'gallery', 'branding'];

    foreach ($folders as $folder) {
        $dir = '../uploads/' . $folder . '/';
        if (!is_dir($dir)) continue;

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.htaccess') continue;

            $fullPath = $dir . $file;
            if (!is_file($fullPath)) continue;

            // Skip files already reasonably small - no need to reprocess
            if (filesize($fullPath) < 150 * 1024) continue;

            $outcome = compressExistingImage($fullPath);
            if ($outcome) {
                [$before, $after] = $outcome;
                $results[] = [
                    'folder' => $folder,
                    'file' => $file,
                    'before' => $before,
                    'after' => $after,
                ];
                $totalBefore += $before;
                $totalAfter += $after;
            }
        }
    }
}

function formatKb($bytes) {
    return number_format($bytes / 1024, 1) . ' KB';
}

require 'includes/admin-header.php';
?>

<div class="card" style="max-width:800px;">
    <div class="card-body">
        <h3 style="margin-bottom:10px;">Optimize Existing Images</h3>
        <p style="color:var(--text-muted); margin-bottom:20px;">
            This compresses every image already uploaded to your site (Products, Services, Gallery, Branding) that's larger than 150 KB — 
            resizing to a maximum width of 1400px and reducing quality slightly, without any visible difference on the website. 
            This only needs to be run occasionally, whenever you notice large photos slowing things down. It's safe to run more than once — 
            already-optimized images will simply be skipped.
        </p>

        <?php if (!$ran): ?>
        <form method="POST" action="optimize-images.php">
            <button type="submit" name="run_optimize" value="1" class="btn btn-primary" onclick="return confirm('This will compress all large images currently on your site. Continue?');">
                Run Optimization Now
            </button>
        </form>
        <?php else: ?>

            <?php if (count($results) === 0): ?>
                <div class="alert alert-success">Nothing to optimize — all your images are already a reasonable size.</div>
            <?php else: ?>
                <div class="alert alert-success">
                    Optimized <?php echo count($results); ?> image<?php echo count($results) !== 1 ? 's' : ''; ?> — 
                    saved <?php echo formatKb($totalBefore - $totalAfter); ?> 
                    (<?php echo number_format((($totalBefore - $totalAfter) / max($totalBefore,1)) * 100, 0); ?>% smaller overall).
                </div>

                <div class="table-responsive">
                <table class="admin-table">
                    <tr><th>Folder</th><th>File</th><th>Before</th><th>After</th><th>Saved</th></tr>
                    <?php foreach ($results as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['folder']); ?></td>
                        <td><?php echo htmlspecialchars($r['file']); ?></td>
                        <td><?php echo formatKb($r['before']); ?></td>
                        <td style="color:var(--success); font-weight:600;"><?php echo formatKb($r['after']); ?></td>
                        <td><?php echo number_format((($r['before'] - $r['after']) / max($r['before'],1)) * 100, 0); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                </div>
            <?php endif; ?>

            <a href="optimize-images.php" class="btn btn-outline" style="margin-top:20px;">Run Again</a>
        <?php endif; ?>
    </div>
</div>

<?php require 'includes/admin-footer.php'; ?>