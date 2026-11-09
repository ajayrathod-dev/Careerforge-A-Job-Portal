<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include 'connection.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM job_seekers WHERE id = ?");
$stmt->execute([$id]);

header('Location: user_list.php');
exit;