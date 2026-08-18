<?php
require 'includes/mailer.php';

$result = sendEmail(
    'jawadchannal22@gmail.com',
    'Test',
    'Bismillah Shop - Test Email',
    '<h2>It works!</h2><p>This is a test email from your project.</p>'
);

echo $result ? "SUCCESS: Email sent! Check your inbox." : "FAILED: Check the error log for details.";
?>

