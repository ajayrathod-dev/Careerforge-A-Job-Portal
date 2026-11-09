<?php
require_once 'connection.php';

function checkExistingPortfolio($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM portfolios WHERE user_id = ?");
        $stmt->execute([$userId]);
        $count = $stmt->fetchColumn();
        
        return $count > 0;
    } catch (PDOException $e) {
        error_log("Error checking portfolio: " . $e->getMessage());
        return false;
    }
}
?>