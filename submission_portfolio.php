<?php
// submission_portfolio.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_SESSION['user_id'];
        
        // Get all form data
        $profile_type = $_POST['profileType'] ?? '';
        $full_name = $_POST['fullName'] ?? '';
        // ... (get all other form fields similar to action.php)
        
        $draft_id = $_POST['draft_id'] ?? '';
        
        if (!empty($draft_id)) {
            // Update existing portfolio to submitted status
            $stmt = $pdo->prepare("UPDATE portfolios SET status = 'submitted', submitted_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$draft_id, $user_id]);
            $portfolio_id = $draft_id;
        } else {
            // Insert new portfolio
            $stmt = $pdo->prepare("INSERT INTO portfolios (...) VALUES (...)");
            // ... (similar to action.php but with status 'submitted')
            $portfolio_id = $pdo->lastInsertId();
        }
        
        // Handle file uploads and other data (similar to action.php)
        
        // Redirect to success page
        header("Location: add_portfolio.php?success=1&portfolio_id=" . $portfolio_id);
        exit();
        
    } catch (Exception $e) {
        header("Location: add_portfolio.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>