<?php
function saveUploadedImage($file, $uploadDir, &$error) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $error = 'no_file';
        return false;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'There was a problem uploading the image.';
        return false;
    }

    $maxSize = 5 * 1024 * 1024; // allow up to 5MB upload - we'll compress it down after
    if ($file['size'] > $maxSize) {
        $error = 'Image is too large. Maximum size is 5MB.';
        return false;
    }

    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!array_key_exists($mimeType, $allowedTypes)) {
        $error = 'Only JPG, PNG, and WEBP images are allowed.';
        return false;
    }

    $extension = $allowedTypes[$mimeType];
    $safeName = uniqid('img_', true) . '.' . $extension;
    $destination = $uploadDir . $safeName;

    // If GD is available, resize + compress. Otherwise fall back to a plain copy.
    if (function_exists('imagecreatefromjpeg') && function_exists('imagecreatefrompng') && function_exists('imagecreatefromwebp')) {
        $sourceImage = null;
        if ($mimeType === 'image/jpeg') { $sourceImage = @imagecreatefromjpeg($file['tmp_name']); }
        elseif ($mimeType === 'image/png') { $sourceImage = @imagecreatefrompng($file['tmp_name']); }
        elseif ($mimeType === 'image/webp') { $sourceImage = @imagecreatefromwebp($file['tmp_name']); }

        if ($sourceImage) {
            $origWidth = imagesx($sourceImage);
            $origHeight = imagesy($sourceImage);
            $maxWidth = 1400;

            if ($origWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) round($origHeight * ($maxWidth / $origWidth));
            } else {
                $newWidth = $origWidth;
                $newHeight = $origHeight;
            }

            $resized = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG (important for logos)
            if ($mimeType === 'image/png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            if ($mimeType === 'image/jpeg') { imagejpeg($resized, $destination, 82); }
            elseif ($mimeType === 'image/png') { imagepng($resized, $destination, 6); }
            elseif ($mimeType === 'image/webp') { imagewebp($resized, $destination, 82); }

            imagedestroy($sourceImage);
            imagedestroy($resized);
            return $safeName;
        }
    }

    // Fallback: GD not available, just move the original file as-is
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $error = 'Failed to save the uploaded image.';
        return false;
    }

    return $safeName;
}

function handleImageUpload($fileKey, $uploadDir, &$error) {
    $file = $_FILES[$fileKey] ?? null;
    return saveUploadedImage($file, $uploadDir, $error);
}

function handleMultipleImageUpload($fileKey, $uploadDir) {
    $saved = [];
    if (!isset($_FILES[$fileKey]) || !is_array($_FILES[$fileKey]['name'])) {
        return $saved;
    }

    $files = $_FILES[$fileKey];
    $count = count($files['name']);

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $singleFile = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i],
        ];
        $error = '';
        $savedName = saveUploadedImage($singleFile, $uploadDir, $error);
        if ($savedName) {
            $saved[] = $savedName;
        }
    }

    return $saved;
}

/**
 * Turns a numeric rating (e.g. 4.3) into a row of filled/empty stars.
 */
function renderStars($rating) {
    $rounded = round($rating);
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= ($i <= $rounded) ? '&#9733;' : '&#9734;';
    }
    return $html;
}
/**
 * Returns a consistent badge color for each product category,
 * so the catalog reads with visual variety instead of one flat look.
 */
function categoryBadgeColors($categoryName) {
    $map = [
        'Mobile Phones' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
        'Laptops'       => ['bg' => '#ede9fe', 'text' => '#6d28d9'],
        'Tablets'       => ['bg' => '#ccfbf1', 'text' => '#0f766e'],
        'Smart Watches' => ['bg' => '#fce7f3', 'text' => '#be185d'],
        'Headphones'    => ['bg' => '#ffedd5', 'text' => '#c2410c'],
        'Accessories'   => ['bg' => '#f1f5f9', 'text' => '#475569'],
    ];
    return $map[$categoryName] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
}
/**
 * Renders a product's price block. Shows a normal price, or if a valid
 * sale price is set, shows a strikethrough original price + sale price + % off badge.
 */
function renderProductPrice($price, $salePrice = null, $size = 'normal') {
    $fontSize = $size === 'large' ? '28px' : '18px';
    if ($salePrice !== null && $salePrice > 0 && $salePrice < $price) {
        $discountPercent = round((($price - $salePrice) / $price) * 100);
        return '<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:8px 0;">'
             . '<span style="font-family:var(--font-heading); color:var(--text-muted); text-decoration:line-through; font-size:14px;">Rs. ' . number_format($price) . '</span>'
             . '<span style="font-family:var(--font-heading); color:var(--primary); font-weight:700; font-size:' . $fontSize . ';">Rs. ' . number_format($salePrice) . '</span>'
             . '<span style="background:var(--danger); color:#fff; font-size:11px; font-weight:700; padding:2px 8px; border-radius:20px;">' . $discountPercent . '% OFF</span>'
             . '</div>';
    }
    return '<div class="price" style="font-size:' . $fontSize . ';">Rs. ' . number_format($price) . '</div>';
}
/**
 * Renders a breadcrumb trail. Pass an array of steps, each with a 'label'
 * and optional 'url' - the last item (current page) should have no 'url'.
 * Example: [['label'=>'Home','url'=>'/bismillah-shop/index.php'], ['label'=>'Products']]
 */
function renderBreadcrumbs($items) {
    $html = '<div class="breadcrumb-bar"><div class="container"><nav class="breadcrumb" aria-label="Breadcrumb">';
    $count = count($items);
    foreach ($items as $i => $item) {
        if ($i > 0) { $html .= '<span class="sep">/</span>'; }
        if (isset($item['url']) && $i < $count - 1) {
            $html .= '<a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['label']) . '</a>';
        } else {
            $html .= '<span class="current">' . htmlspecialchars($item['label']) . '</span>';
        }
    }
    $html .= '</nav></div></div>';
    return $html;
}
/**
 * Compresses an existing image file on disk, in place (same filename).
 * Returns [originalSize, newSize] in bytes, or false if it couldn't process the file.
 */
function compressExistingImage($filePath, $maxWidth = 1400, $quality = 82) {
    if (!file_exists($filePath)) return false;

    $imageInfo = @getimagesize($filePath);
    if (!$imageInfo) return false;

    $mimeType = $imageInfo['mime'];
    $originalSize = filesize($filePath);

    $sourceImage = null;
    if ($mimeType === 'image/jpeg') { $sourceImage = @imagecreatefromjpeg($filePath); }
    elseif ($mimeType === 'image/png') { $sourceImage = @imagecreatefrompng($filePath); }
    elseif ($mimeType === 'image/webp') { $sourceImage = @imagecreatefromwebp($filePath); }
    else { return false; }

    if (!$sourceImage) return false;

    $origWidth = imagesx($sourceImage);
    $origHeight = imagesy($sourceImage);

    if ($origWidth > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = (int) round($origHeight * ($maxWidth / $origWidth));
    } else {
        $newWidth = $origWidth;
        $newHeight = $origHeight;
    }

    $resized = imagecreatetruecolor($newWidth, $newHeight);

    if ($mimeType === 'image/png') {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    if ($mimeType === 'image/jpeg') { imagejpeg($resized, $filePath, $quality); }
    elseif ($mimeType === 'image/png') { imagepng($resized, $filePath, 6); }
    elseif ($mimeType === 'image/webp') { imagewebp($resized, $filePath, $quality); }

    imagedestroy($sourceImage);
    imagedestroy($resized);

    clearstatcache();
    $newSize = filesize($filePath);

    return [$originalSize, $newSize];
}
/**
 * Generates a random, hard-to-guess 6-character reference code for enquiry
 * tracking, and guarantees it's unique in the database.
 */
function generateUniqueReferenceToken($pdo) {
    do {
        $token = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE reference_token = ?");
        $stmt->execute([$token]);
    } while ($stmt->fetchColumn() > 0);
    return $token;
}
/**
 * Runs a PHP script in the background (doesn't make the current page wait for it).
 * Used so email sending doesn't slow down the page for the visitor.
 * Returns true if it started successfully, false if this environment doesn't support it
 * (in which case the caller should send the email normally as a fallback).
 */
function runInBackground($scriptAbsolutePath, $args = []) {
    if (!function_exists('popen') || !function_exists('pclose')) {
        return false;
    }
    if (!defined('PHP_CLI_PATH') || !file_exists(PHP_CLI_PATH)) {
        return false;
    }

    $escapedArgs = array_map('escapeshellarg', $args);
    $cmd = '"' . PHP_CLI_PATH . '" "' . $scriptAbsolutePath . '" ' . implode(' ', $escapedArgs);

    $handle = @popen('cmd /c start /B "" ' . $cmd, 'r');
    if ($handle === false) {
        return false;
    }
    pclose($handle);
    return true;
}
function generateUniqueOrderReference($pdo) {
    do {
        $token = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE reference_token = ?");
        $stmt->execute([$token]);
    } while ($stmt->fetchColumn() > 0);
    return $token;
}

function orderStatusInfo($status) {
    $map = [
        'pending_payment' => ['label' => 'Awaiting Payment', 'bg' => '#fef3c7', 'text' => '#a16207'],
        'payment_review'  => ['label' => 'Payment Under Review', 'bg' => '#dbeafe', 'text' => '#1e40af'],
        'confirmed'       => ['label' => 'Payment Confirmed', 'bg' => '#d1fae5', 'text' => '#047857'],
        'processing'      => ['label' => 'Processing', 'bg' => '#ede9fe', 'text' => '#6d28d9'],
        'completed'       => ['label' => 'Completed', 'bg' => '#dcfce7', 'text' => '#15803d'],
        'cancelled'       => ['label' => 'Cancelled', 'bg' => '#fee2e2', 'text' => '#b91c1c'],
    ];
    return $map[$status] ?? $map['pending_payment'];
}

function paymentMethodLabel($method) {
    return $method === 'bank_transfer' ? 'Bank Transfer' : 'JazzCash / EasyPaisa';
}
?>