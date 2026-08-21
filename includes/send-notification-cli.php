<?php
// This file only runs from the command line (triggered by runInBackground()) -
// it should never be opened directly in a browser.
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

require __DIR__ . '/../config/database.php';
require __DIR__ . '/mailer.php';

$type = $argv[1] ?? '';
$id = (int) ($argv[2] ?? 0);
$host = $argv[3] ?? 'localhost';

if ($type === 'message' && $id > 0) {
    notifyAdminsOfNewMessage($pdo, $id, $host);
} elseif ($type === 'review' && $id > 0) {
    notifyAdminsOfNewReview($pdo, $id, $host);
} elseif ($type === 'order' && $id > 0) {
    notifyAdminsOfNewOrder($pdo, $id, $host);
}