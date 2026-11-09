<?php
/**
 * Database Configuration Template
 * 
 * Rename or copy this file to connection.php and admin/connection.php
 * with your database credentials.
 */

$host = 'localhost';
$dbname = 'job_portal';
$username = 'root';     // Default XAMPP username
$password = '';         // Default XAMPP password is empty

// 1. PDO Connection (Used across frontend)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("PDO Database Connection Failed: " . $e->getMessage());
}

// 2. MySQLi Connection (Used across admin panel)
$link = mysqli_connect($host, $username, $password, $dbname);
if ($link === false) {
    die("MySQLi Database Connection Failed: " . mysqli_connect_error());
}
?>
