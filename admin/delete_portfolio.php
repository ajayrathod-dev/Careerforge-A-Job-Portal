<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include 'connection.php';

$id = $_GET['id'];

// First, delete related records in portfolio_education and portfolio_certificates
$stmt = $pdo->prepare("DELETE FROM portfolio_education WHERE portfolio_id = ?");
$stmt->execute([$id]);

$stmt = $pdo->prepare("DELETE FROM portfolio_certificates WHERE portfolio_id = ?");
$stmt->execute([$id]);

// Then delete the portfolio
$stmt = $pdo->prepare("DELETE FROM portfolios WHERE id = ?");
$stmt->execute([$id]);

header('Location: portfolio_list.php');
exit;