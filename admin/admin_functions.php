<?php
session_start();

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect to login if not authenticated
function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Get admin details
function getAdminDetails($link) {
    if (isset($_SESSION['admin_user_id'])) {
        $username = $_SESSION['admin_user_id'];
        $sql = "SELECT * FROM admin WHERE user_id='$username' LIMIT 1";
        $result = mysqli_query($link, $sql);
        return mysqli_fetch_assoc($result);
    }
    return null;
}
?>