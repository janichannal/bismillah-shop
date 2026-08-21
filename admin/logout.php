<?php
session_start();
session_destroy();
header('Location: /bismillah-shop/index.php');
exit;
?>