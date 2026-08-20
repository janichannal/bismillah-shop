<?php
$pageTitle = 'Contact Us';
$pageDescription = 'Contact Bismillah Mobile & Laptop Shop in Khuzdar - visit us, call, or send a message for products or repair services.';
require 'config/database.php';
require 'includes/functions.php';

$successMessage = '';
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
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, subject, message) 
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            $successMessage = 'Thank you! Your message has been sent. We will get back to you soon.';
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
                    Main Bazaar Road, Khuzdar, Balochistan
                </p>
                <p style="margin-bottom: 18px;">
                    <strong style="color:var(--accent); display:block; margin-bottom:4px;">Phone</strong>
                    0300-1234567
                </p>
                <p style="margin-bottom: 18px;">
                    <strong style="color:var(--accent); display:block; margin-bottom:4px;">Email</strong>
                    info@bismillahshop.com
                </p>
                <p>
                    <strong style="color:var(--accent); display:block; margin-bottom:4px;">Opening Hours</strong>
                    Saturday – Thursday: 10:00 AM – 9:00 PM<br>Friday: 3:00 PM – 9:00 PM
                </p>
            </div>

            <div class="card" style="padding: 34px 28px;">
                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
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
    </div>
</section>

<?php require 'includes/footer.php'; ?>