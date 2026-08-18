<?php
session_start();

// If there's no logged-in admin session, kick them back to the login page.
// Using an absolute path (starting with /) so this works correctly
// no matter which subfolder includes this file.
if (!isset($_SESSION['admin_id'])) {
    header('Location: /bismillah-shop/admin/login.php');
    exit;
}
?>