<?php
$pageTitle = 'Contact Us';
$pageDescription = 'Contact Bismillah Mobile & Laptop Shop in Khuzdar - visit us, call, or send a message for products or repair services.';
require 'config/database.php';
require 'config/constants.php';
require 'includes/functions.php';
require 'includes/mailer.php';

$successMessage = '';
$successReference = '';
$errorMessage = '';

$name    = '';
$email   = '';
$phone   = '';
$subject = isset($_GET['subject']) ? trim($_GET['subject']) : '';
$message = isset($_GET['message']) ? trim($_GET['message']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $errorMessage = 'Please fill in your name, email, and message.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
               try {
            $refToken = generateUniqueReferenceToken($pdo);
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, subject, message, reference_token) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message, $refToken]);
            $newMessageId = $pdo->lastInsertId();

            $sentInBackground = runInBackground(
                __DIR__ . '/includes/send-notification-cli.php',
                ['message', (string) $newMessageId, $_SERVER['HTTP_HOST']]
            );
            if (!$sentInBackground) {
                notifyAdminsOfNewMessage($pdo, $newMessageId, $_SERVER['HTTP_HOST']);
            }

            $successMessage = 'Thank you! Your message has been sent. We will get back to you soon.';
            $successReference = $refToken;
            $name = $email = $phone = $subject = $message = '';
        } catch (PDOException $e) {
            $errorMessage = 'Sorry, something went wrong. Please try again later.';
        }
    }
}

require 'includes/header.php';
?>

<section class="hero" style="padding: 60px 0;">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Have a question? Send us a message or visit our shop.</p>
    </div>
</section>

<?php echo renderBreadcrumbs([
    ['label' => 'Home', 'url' => '/bismillah-shop/index.php'],
    ['label' => 'Contact'],
]); ?>

<section class="section">
    <div class="container">
        <div class="split-contact">

            <div style="background: var(--secondary); border-radius: var(--radius); padding: 34px 28px; color: #cbd5e1;">
                <h3 style="color:#fff; margin-bottom:20px;">Get In Touch</h3>
                <p style="margin-bottom: 18px;">
                    <strong style="color:var(--accent); display:block; margin-bottom:4px;">Address</strong>
                    Main Bazaar Azadii chowk mashke Road, Khuzdar, Balochistan
                </p>
                <p style="margin-bottom: 18px;">
                    <strong style="color:var(--accent); display:block; margin-bottom:4px;">Phone</strong>
                    03379788440
                </p>
                <p style="margin-bottom: 18px;">
                    <strong style="color:var(--accent); display:block; margin-bottom:4px;">Email</strong>
                    info@bismillahshop.com
                </p>
                <p style="margin-bottom: 24px;">
                    <strong style="color:var(--accent); display:block; margin-bottom:4px;">Opening Hours</strong>
                    Saturday – Thursday: 9:00 AM – 11:00 PM<br>Friday: 9:00 AM – 3:00 PM
                </p>
                <a href="enquiry-status.php" class="btn btn-outline" style="border-color:#fff; color:#fff; font-size:15px; padding:10px 16px; margin-bottom: 8px;">
                  Already messaged us? Check status for Enquiry product/services
                </a>

                <a href="track-order.php" class="btn btn-outline" style="border-color:#fff; color:#fff; font-size:15px; padding:10px 16px;">
                 Already Orderd us? Check for orders or payment status
                </a>
            </div>

            <div class="card" style="padding: 34px 28px;">
                <?php if ($successMessage): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($successMessage); ?>
                        <?php if ($successReference): ?>
                            <br><br>
                            Your reference number: <strong style="font-family:var(--font-heading); letter-spacing:1px;">ENQ-<?php echo htmlspecialchars($successReference); ?></strong>
                            <br>Save this to check your enquiry status anytime on our
                            <a href="enquiry-status.php" style="font-weight:600;">Enquiry Status page</a>.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <form method="POST" action="contact.php">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

        </div>

                <div style="margin-top:60px;">
            <div class="section-title">
                <h2>Find Us</h2>
                <p>Main Bazaar Azadii chowk mashke Road, Khuzdar, Balochistan</p>
            </div>
            <div class="map-section-wrap">
                <div class="map-embed-wrap">
                    <iframe 
                        src="https://www.google.com/maps?q=Main+Bazaar+Road,+Khuzdar,+Balochistan,+Pakistan&output=embed" 
                        width="100%" 
                        height="400" 
                        style="width:100%; height:400px; border:0; display:block;"
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Bismillah Mobile & Laptop Shop location">
                    </iframe>
                </div>
                <div class="map-directions-btn">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=Main+Bazaar+Road,+Khuzdar,+Balochistan,+Pakistan" target="_blank" rel="noopener" class="btn btn-primary">Get Directions</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>