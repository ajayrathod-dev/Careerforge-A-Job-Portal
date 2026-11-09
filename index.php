<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect logged-in users to home dashboard, others to public landing page
if (isset($_SESSION['user_id'])) {
    header("Location: index-2.php");
    exit();
} else {
    header("Location: index.html");
    exit();
}
?>
