<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    try {
        // Handle first name and last name combination for full_name
        $full_name = '';
        if (isset($_POST['firstName']) || isset($_POST['lastName'])) {
            $first_name = $_POST['firstName'] ?? '';
            $last_name = $_POST['lastName'] ?? '';
            $full_name = trim($first_name . ' ' . $last_name);
        } elseif (isset($_POST['fullname'])) {
            $full_name = $_POST['fullname'];
        }
        
        // Prepare data for update
        $fields = [
            'fullname' => $full_name,
            'email', 'username', 'phone', 'bio',
            'core_industry', 'category', 'subcategory', 
            'project_management_exp', 'project_management_skills', 
            'cross_industry_exp'
        ];
        
        $updates = [];
        $params = [];
        
        foreach ($fields as $key => $value) {
            if (is_numeric($key)) {
                // Regular field
                $field = $value;
                if (isset($_POST[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $_POST[$field];
                }
            } else {
                // Special field (like fullname)
                $field = $key;
                if (!empty($value)) {
                    $updates[] = "$field = ?";
                    $params[] = $value;
                }
            }
        }
        
        // Handle selected skills
        if (isset($_POST['selected_skills'])) {
            $updates[] = "selected_skills = ?";
            $params[] = json_encode($_POST['selected_skills']);
        }
        
        // Handle experience arrays
        if (isset($_POST['us_experience'])) {
            $updates[] = "us_experience = ?";
            $params[] = json_encode($_POST['us_experience']);
        }
        
        if (isset($_POST['india_experience'])) {
            $updates[] = "india_experience = ?";
            $params[] = json_encode($_POST['india_experience']);
        }
        
        // Handle individual name fields if provided separately
        if (isset($_POST['firstName']) && !isset($_POST['fullname'])) {
            // Get current full_name to preserve last name if only first name is updated
            $stmt = $pdo->prepare("SELECT fullname FROM job_seekers WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            $current_full_name = $current['fullname'] ?? '';
            
            // Split current full name
            $names = explode(' ', $current_full_name, 2);
            $current_first_name = $names[0] ?? '';
            $current_last_name = $names[1] ?? '';
            
            // Update first name only
            $current_first_name = $_POST['firstName'];
            $full_name = trim($current_first_name . ' ' . $current_last_name);
            
            $updates[] = "fullname = ?";
            $params[] = $full_name;
        }
        
        if (isset($_POST['lastName']) && !isset($_POST['fullname'])) {
            // Get current full_name to preserve first name if only last name is updated
            $stmt = $pdo->prepare("SELECT fullname FROM job_seekers WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            $current_full_name = $current['fullname'] ?? '';
            
            // Split current full name
            $names = explode(' ', $current_full_name, 2);
            $current_first_name = $names[0] ?? '';
            $current_last_name = $names[1] ?? '';
            
            // Update last name only
            $current_last_name = $_POST['lastName'];
            $full_name = trim($current_first_name . ' ' . $current_last_name);
            
            $updates[] = "fullname = ?";
            $params[] = $full_name;
        }
        
        if (!empty($updates)) {
            $sql = "UPDATE job_seekers SET " . implode(', ', $updates) . " WHERE id = ?";
            $params[] = $_SESSION['user_id'];
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Profile updated successfully',
                'full_name' => $full_name
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No data to update']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}
?>