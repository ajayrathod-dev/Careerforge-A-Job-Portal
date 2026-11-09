<?php
error_reporting(0);
ini_set('display_errors', 0);

$host = 'localhost';
$dbname = 'job_portal';
$username = 'job_portal';
$password = 'job_portal@878889';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>