<?php
// This file connects our website to the MySQL database.
// Every other PHP page will "include" this file to get a working connection.

$host     = 'localhost';
$dbname   = 'bismillah_shop';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Make PDO throw real errors instead of failing silently
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Return rows as associative arrays, e.g. $row['product_name']
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Never show real database errors to visitors — just a safe message
    die("Sorry, something went wrong. Please try again later.");
}
?>