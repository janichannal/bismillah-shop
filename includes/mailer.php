<?php
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../config/mail_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($toEmail, $toName, $subject, $bodyHtml) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_APP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}
/**
 * Builds a nicely styled, responsive HTML email for admin notifications.
 * Works well on mobile, tablet, and desktop email clients.
 */
function buildNotificationEmail($heading, $introText, $detailsRows, $ctaText, $ctaUrl) {
    $rowsHtml = '';
    foreach ($detailsRows as $label => $value) {
        $rowsHtml .= '<tr>
            <td style="padding:10px 0; border-bottom:1px solid #e2e8f0; font-size:13px; color:#64748b; width:110px; vertical-align:top; font-family:Arial,sans-serif;">' . htmlspecialchars($label) . '</td>
            <td style="padding:10px 0; border-bottom:1px solid #e2e8f0; font-size:15px; color:#1e293b; font-family:Arial,sans-serif;">' . $value . '</td>
        </tr>';
    }

    return '
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6f4; padding:24px 10px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden;">
                <tr><td style="background:#0f172a; padding:22px 26px;">
                    <span style="color:#ffffff; font-size:18px; font-weight:bold; font-family:Arial,sans-serif;">Bismillah<span style="color:#eab308;">Shop</span></span>
                </td></tr>
                <tr><td style="padding:26px;">
                    <h2 style="margin:0 0 10px; color:#0f172a; font-size:20px; font-family:Arial,sans-serif;">' . htmlspecialchars($heading) . '</h2>
                    <p style="margin:0 0 20px; color:#64748b; font-size:15px; line-height:1.6; font-family:Arial,sans-serif;">' . htmlspecialchars($introText) . '</p>
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $rowsHtml . '</table>
                    <div style="margin-top:24px;">
                        <a href="' . htmlspecialchars($ctaUrl) . '" style="display:inline-block; background:#059669; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:8px; font-size:15px; font-weight:bold; font-family:Arial,sans-serif;">' . htmlspecialchars($ctaText) . '</a>
                    </div>
                </td></tr>
                <tr><td style="background:#f3f6f4; padding:14px 26px; text-align:center;">
                    <span style="color:#94a3b8; font-size:12px; font-family:Arial,sans-serif;">Bismillah Mobile & Laptop Shop &middot; Admin Notification</span>
                </td></tr>
            </table>
        </td></tr>
    </table>';
}

/**
 * Sends the notification email to every admin account on file.
 */
function notifyAllAdmins($pdo, $subject, $heading, $introText, $detailsRows, $ctaText, $ctaUrl) {
    $admins = $pdo->query("SELECT name, email FROM admins")->fetchAll();
    $body = buildNotificationEmail($heading, $introText, $detailsRows, $ctaText, $ctaUrl);
    foreach ($admins as $admin) {
        sendEmail($admin['email'], $admin['name'], $subject, $body);
    }
}
function notifyAdminsOfNewMessage($pdo, $messageId, $host = 'localhost') {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE message_id = ?");
    $stmt->execute([$messageId]);
    $m = $stmt->fetch();
    if (!$m) return;

    notifyAllAdmins(
        $pdo,
        'New Message: ' . ($m['subject'] ?: 'No subject'),
        'New Customer Message',
        'You just received a new message through your website contact form.',
        [
            'From'      => htmlspecialchars($m['name']),
            'Email'     => htmlspecialchars($m['email']),
            'Phone'     => htmlspecialchars($m['phone'] ?: 'Not provided'),
            'Subject'   => htmlspecialchars($m['subject'] ?: '(No subject)'),
            'Reference' => !empty($m['reference_token']) ? 'ENQ-' . htmlspecialchars($m['reference_token']) : 'N/A',
            'Message'   => nl2br(htmlspecialchars($m['message'])),
        ],
        'View in Admin Panel',
        'http://' . $host . '/bismillah-shop/admin/messages/index.php'
    );
}

function notifyAdminsOfNewReview($pdo, $reviewId, $host = 'localhost') {
    $stmt = $pdo->prepare("SELECT r.*, p.product_name FROM reviews r JOIN products p ON r.product_id = p.product_id WHERE r.review_id = ?");
    $stmt->execute([$reviewId]);
    $r = $stmt->fetch();
    if (!$r) return;

    notifyAllAdmins(
        $pdo,
        'New Review Pending Approval - ' . $r['product_name'],
        'New Review Submitted',
        'A customer just submitted a new review that needs your approval before it goes live on the site.',
        [
            'Product'  => htmlspecialchars($r['product_name']),
            'Customer' => htmlspecialchars($r['customer_name']),
            'Rating'   => str_repeat('&#9733;', $r['rating']) . str_repeat('&#9734;', 5 - $r['rating']),
            'Review'   => $r['review_text'] ? nl2br(htmlspecialchars($r['review_text'])) : '<em>(no written comment)</em>',
        ],
        'Review in Admin Panel',
        'http://' . $host . '/bismillah-shop/admin/reviews/index.php'
    );
}
function notifyAdminsOfNewOrder($pdo, $orderId, $host = 'localhost') {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $o = $stmt->fetch();
    if (!$o) return;

    notifyAllAdmins(
        $pdo,
        'New Order: ' . $o['product_name_snapshot'] . ' - ORD-' . $o['reference_token'],
        'New Order Placed',
        'A customer just placed a new order on your website.',
        [
            'Reference' => 'ORD-' . htmlspecialchars($o['reference_token']),
            'Product'   => htmlspecialchars($o['product_name_snapshot']) . ' x' . $o['quantity'],
            'Total'     => 'Rs. ' . number_format($o['total_amount']),
            'Customer'  => htmlspecialchars($o['customer_name']) . ' (' . htmlspecialchars($o['customer_phone']) . ')',
            'Payment'   => paymentMethodLabel($o['payment_method']),
            'Proof'     => $o['payment_proof_image'] ? 'Uploaded - ready to review' : 'Not uploaded yet',
        ],
        'View Order in Admin Panel',
        'http://' . $host . '/bismillah-shop/admin/orders/view.php?id=' . $orderId
    );
}

function notifyCustomerOrderConfirmed($order) {
    $body = buildNotificationEmail(
        'Your Payment Has Been Confirmed',
        'Good news! We\'ve confirmed your payment for order ORD-' . $order['reference_token'] . '. We\'re now preparing your order.',
        [
            'Order' => htmlspecialchars($order['product_name_snapshot']) . ' x' . $order['quantity'],
            'Total' => 'Rs. ' . number_format($order['total_amount']),
        ],
        'Track Your Order',
        'http://' . $_SERVER['HTTP_HOST'] . '/bismillah-shop/track-order.php?ref=' . $order['reference_token']
    );
    sendEmail($order['customer_email'], $order['customer_name'], 'Payment Confirmed - Order ORD-' . $order['reference_token'], $body);
}
?>