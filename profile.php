<?php
session_start();
require_once 'connection.php';

// Check if user is logged in, if not redirect to login page
$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$success_message = '';
$error_message = '';
$user_data = [];
$user_skills = [];
$portfolio_data = [];
$profile_completion = 0;
$portfolio_stats = [
    'total_portfolios' => 0,
    'pending_portfolios' => 0,
    'submitted_portfolios' => 0
];

try {
    // Fetch current user data from job_seekers table
    $sql = "SELECT * FROM job_seekers WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_data) {
        $error_message = "User profile not found!";
    } else {
        // Fetch latest portfolio data for this user
        $portfolio_sql = "SELECT * FROM portfolios WHERE user_id = ? ORDER BY submitted_at DESC, updated_at DESC LIMIT 1";
        $portfolio_stmt = $pdo->prepare($portfolio_sql);
        $portfolio_stmt->execute([$user_id]);
        $portfolio_data = $portfolio_stmt->fetch(PDO::FETCH_ASSOC);

        // FIXED: Get skills from portfolio data instead of job_seekers
        $user_skills = [];
        if (!empty($portfolio_data['selected_skills'])) {
            $skills_data = $portfolio_data['selected_skills'];
            if (is_string($skills_data)) {
                $raw = trim($skills_data);
                // strip surrounding quotes if present and unescape slashes/quotes
                if ((substr($raw, 0, 1) === '"' && substr($raw, -1) === '"') || (substr($raw, 0, 1) === "'" && substr($raw, -1) === "'")) {
                    $raw = substr($raw, 1, -1);
                }
                $raw = stripslashes($raw);
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (is_array($decoded)) {
                        $user_skills = $decoded;
                    } elseif (is_string($decoded)) {
                        // handle JSON string containing JSON
                        $decoded2 = json_decode($decoded, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded2)) {
                            $user_skills = $decoded2;
                        } else {
                            $user_skills = array_filter(array_map('trim', explode(',', $decoded)));
                        }
                    }
                } else {
                    // fallback: comma-separated string
                    $user_skills = array_filter(array_map('trim', explode(',', $raw)));
                }
            } elseif (is_array($skills_data)) {
                $user_skills = $skills_data;
            }
        }

        // FIXED: Calculate profile completion percentage using both user data and portfolio data
        $profile_completion = calculateProfileCompletion($user_data, $portfolio_data, $user_skills);
        
        // Get portfolio statistics
        $portfolio_stats_sql = "SELECT status, COUNT(*) as count FROM portfolios WHERE user_id = ? GROUP BY status";
        $portfolio_stats_stmt = $pdo->prepare($portfolio_stats_sql);
        $portfolio_stats_stmt->execute([$user_id]);
        $portfolio_counts = $portfolio_stats_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($portfolio_counts as $count) {
            $portfolio_stats['total_portfolios'] += $count['count'];
            if ($count['status'] === 'pending') {
                $portfolio_stats['pending_portfolios'] = $count['count'];
            } elseif ($count['status'] === 'submitted') {
                $portfolio_stats['submitted_portfolios'] = $count['count'];
            }
        }
    }
} catch (PDOException $e) {
    $error_message = "Error fetching user data: " . $e->getMessage();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Check if this is a password update only or full profile update
    $is_password_update = !empty($new_password);
    $is_profile_update = !empty($fullname) || !empty($email);
    
    if ($is_profile_update) {
        // Validate required fields for profile update
        if (empty($fullname)) {
            $errors[] = "Full name is required";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required";
        }
        
        // Check if email already exists (excluding current user)
        if (!empty($email) && $email !== $user_data['email']) {
            $check_sql = "SELECT id FROM job_seekers WHERE email = ? AND id != ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$email, $user_id]);
            if ($check_stmt->fetch()) {
                $errors[] = "Email already exists";
            }
        }
        
        // Phone validation (allow 7-20 characters including common separators)
        if (!empty($phone) && !preg_match('/^[\d\s\-\+\(\)]{7,20}$/', $phone)) {
            $errors[] = "Invalid phone number format";
        }
        
        // Bio validation
        if (!empty($bio) && strlen($bio) > 500) {
            $errors[] = "Bio must not exceed 500 characters";
        }
    }
    
    // Password change validation
    if ($is_password_update) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to set new password";
        } elseif (!password_verify($current_password, $user_data['password'])) {
            $errors[] = "Current password is incorrect";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
    }
    
    // Handle profile picture upload
    $profile_picture_path = $user_data['profile_picture'] ?? '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed";
        } elseif ($_FILES['profile_picture']['size'] > $max_file_size) {
            $errors[] = "File size must not exceed 2MB";
        } else {
            $new_filename = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                // Optionally delete old profile picture if exists
                if ($profile_picture_path && file_exists($profile_picture_path)) {
                    unlink($profile_picture_path);
                }
                $profile_picture_path = $upload_path;
            } else {
                $errors[] = "Failed to upload profile picture";
            }
        }
    }
    
    // Handle cover photo upload
    $cover_photo_path = $user_data['cover_photo'] ?? '';
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/covers/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed for cover photos";
        } elseif ($_FILES['cover_photo']['size'] > $max_file_size) {
            $errors[] = "Cover photo size must not exceed 5MB";
        } else {
            $new_filename = 'cover_' . $user_id . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], $upload_path)) {
                // Optionally delete old cover photo if exists
                if ($cover_photo_path && file_exists($cover_photo_path)) {
                    unlink($cover_photo_path);
                }
                $cover_photo_path = $upload_path;
            } else {
                $errors[] = "Failed to upload cover photo";
            }
        }
    }
    
    // Handle cover photo removal
    if (isset($_POST['remove_cover']) && $_POST['remove_cover'] == '1') {
        if ($cover_photo_path && file_exists($cover_photo_path)) {
            unlink($cover_photo_path);
        }
        $cover_photo_path = '';
    }
    
    if (empty($errors)) {
        try {
            // Prepare update query based on what's being updated
            if ($is_profile_update) {
                $update_fields = "fullname = ?, email = ?, phone = ?, bio = ?";
                $params = [$fullname, $email, $phone, $bio];
                
                if ($profile_picture_path !== $user_data['profile_picture']) {
                    $update_fields .= ", profile_picture = ?";
                    $params[] = $profile_picture_path;
                }
                
                if ($cover_photo_path !== ($user_data['cover_photo'] ?? '')) {
                    $update_fields .= ", cover_photo = ?";
                    $params[] = $cover_photo_path;
                }
                
                $update_sql = "UPDATE job_seekers SET $update_fields WHERE id = ?";
                $params[] = $user_id;
                
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute($params);
                
                // Update session data only if changed
                if (($fullname !== ($user_data['fullname'] ?? '')) || ($email !== ($user_data['email'] ?? ''))) {
                    $_SESSION['fullname'] = $fullname;
                    $_SESSION['email'] = $email;
                }
            }
            
            // Handle password update separately
            if ($is_password_update) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $password_sql = "UPDATE job_seekers SET password = ? WHERE id = ?";
                $password_stmt = $pdo->prepare($password_sql);
                $password_stmt->execute([$hashed_password, $user_id]);
            }
            
            // Refresh user data
            $sql = "SELECT * FROM job_seekers WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Refresh portfolio data
            $portfolio_sql = "SELECT * FROM portfolios WHERE user_id = ? ORDER BY submitted_at DESC, updated_at DESC LIMIT 1";
            $portfolio_stmt = $pdo->prepare($portfolio_sql);
            $portfolio_stmt->execute([$user_id]);
            $portfolio_data = $portfolio_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Refresh skills data with robust decoding from portfolio
            $user_skills = [];
            if (!empty($portfolio_data['selected_skills'])) {
                $skills_data = $portfolio_data['selected_skills'];
                if (is_string($skills_data)) {
                    $raw = trim($skills_data);
                    if ((substr($raw, 0, 1) === '"' && substr($raw, -1) === '"') || (substr($raw, 0, 1) === "'" && substr($raw, -1) === "'")) {
                        $raw = substr($raw, 1, -1);
                    }
                    $raw = stripslashes($raw);
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (is_array($decoded)) {
                            $user_skills = $decoded;
                        } elseif (is_string($decoded)) {
                            $decoded2 = json_decode($decoded, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded2)) {
                                $user_skills = $decoded2;
                            } else {
                                $user_skills = array_filter(array_map('trim', explode(',', $decoded)));
                            }
                        }
                    } else {
                        $user_skills = array_filter(array_map('trim', explode(',', $raw)));
                    }
                } elseif (is_array($skills_data)) {
                    $user_skills = $skills_data;
                }
            }
            
            $profile_completion = calculateProfileCompletion($user_data, $portfolio_data, $user_skills);
            
            $success_message = "Profile updated successfully!";
            
        } catch (PDOException $e) {
            $error_message = "Error updating profile: " . $e->getMessage();
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// FIXED: Accurate profile completion calculation using both user data and portfolio data
function calculateProfileCompletion($user_data, $portfolio_data, $user_skills) {
    $total_fields = 0;
    $completed_fields = 0;

    // Basic Information from job_seekers (6 fields)
    $basic_fields = [
        'fullname' => $user_data['fullname'] ?? '',
        'email' => $user_data['email'] ?? '',
        'phone' => $user_data['phone'] ?? '',
        'bio' => $user_data['bio'] ?? '',
        'profile_picture' => $user_data['profile_picture'] ?? '',
        'cover_photo' => $user_data['cover_photo'] ?? ''
    ];
    
    foreach ($basic_fields as $field => $value) {
        $total_fields++;
        if (!empty(trim($value))) {
            $completed_fields++;
        }
    }

    // Professional Information from portfolio (8 fields)
    $professional_fields = [
        'core_industry' => $portfolio_data['core_industry'] ?? '',
        'category' => $portfolio_data['category'] ?? '',
        'subcategory' => $portfolio_data['subcategory'] ?? '',
        'project_management_skills' => $portfolio_data['project_management_skills'] ?? '',
        'technical_strengths' => $portfolio_data['technical_strengths'] ?? '',
        'personal_strengths' => $portfolio_data['personal_strengths'] ?? '',
        'current_designation' => $portfolio_data['current_designation'] ?? '',
        'total_experience' => $portfolio_data['total_experience'] ?? ''
    ];
    
    foreach ($professional_fields as $field => $value) {
        $total_fields++;
        if (!empty(trim($value))) {
            $completed_fields++;
        }
    }

    // Skills from portfolio (1 field)
    $total_fields++;
    if (!empty($user_skills) && count($user_skills) > 0) {
        $has_valid_skills = false;
        foreach ($user_skills as $skill) {
            if (!empty(trim($skill))) {
                $has_valid_skills = true;
                break;
            }
        }
        if ($has_valid_skills) {
            $completed_fields++;
        }
    }

    // Experience Preferences from portfolio (2 fields)
    $experience_fields = [
        'project_management_exp' => $portfolio_data['project_management_exp'] ?? '',
        'cross_industry_exp' => $portfolio_data['cross_industry_exp'] ?? ''
    ];
    
    foreach ($experience_fields as $field => $value) {
        $total_fields++;
        if (!empty(trim($value))) {
            $completed_fields++;
        }
    }

    // US Experience from portfolio (1 field)
    $total_fields++;
    if (!empty($portfolio_data['us_experience'])) {
        $us_experience = $portfolio_data['us_experience'];
        if (is_string($us_experience)) {
            $decoded = json_decode($us_experience, true);
            if (is_array($decoded)) {
                $has_valid_experience = false;
                foreach ($decoded as $exp) {
                    if (isset($exp['employer']) && !empty(trim($exp['employer']))) {
                        $has_valid_experience = true;
                        break;
                    }
                }
                if ($has_valid_experience) {
                    $completed_fields++;
                }
            }
        }
    }

    // India Experience from portfolio (1 field)
    $total_fields++;
    if (!empty($portfolio_data['india_experience'])) {
        $india_experience = $portfolio_data['india_experience'];
        if (is_string($india_experience)) {
            $decoded = json_decode($india_experience, true);
            if (is_array($decoded)) {
                $has_valid_experience = false;
                foreach ($decoded as $exp) {
                    if (isset($exp['employer']) && !empty(trim($exp['employer']))) {
                        $has_valid_experience = true;
                        break;
                    }
                }
                if ($has_valid_experience) {
                    $completed_fields++;
                }
            }
        }
    }

    // Education from portfolio_education table (1 field)
    $total_fields++;
    if (!empty($portfolio_data['id'])) {
        global $pdo;
        $education_sql = "SELECT COUNT(*) as count FROM portfolio_education WHERE portfolio_id = ?";
        $education_stmt = $pdo->prepare($education_sql);
        $education_stmt->execute([$portfolio_data['id']]);
        $education_count = $education_stmt->fetch(PDO::FETCH_ASSOC);
        if ($education_count['count'] > 0) {
            $completed_fields++;
        }
    }

    return $total_fields > 0 ? round(($completed_fields / $total_fields) * 100) : 0;
}

// Function to check if user has portfolio
function checkExistingPortfolio($user_id) {
    global $pdo;
    try {
        $sql = "SELECT COUNT(*) as count FROM portfolios WHERE user_id = ? AND status IN ('pending', 'submitted')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Get industry name for display
function getIndustryName($industry_code) {
    $industry_names = [
        'CS' => 'Computer Science',
        'IT' => 'Information Technology',
        'EXTC' => 'Electronics & Telecommunication',
        'Mechanical' => 'Mechanical Engineering',
        'MFG' => 'Manufacturing'
    ];
    return $industry_names[$industry_code] ?? $industry_code;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Manage your profile and account settings">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    <title>My Profile | ORDER TECH CAREERS</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-MXxJHkPKt9gXvN3oS8H7AXH5Ncc+bwCTZcG4Dh7Pf3F+HjPtfGdQmAA1eM7t2sV+jEXsWzTukBi4RzP9Y3vUZg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    
    <style>
        :root {
            --primary: #aad01fff;
            --primary-dark: #7fd523ff;
            --secondary: #636df1ff;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --progress-color: #8b5cf6;
        }
        
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }
        
        .hero-section {
            position: relative;
            height: 300px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 0 0 20px 20px;
            overflow: hidden;
            margin-bottom: -100px;
        }
        
        .cover-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cover-photo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .cover-upload-btn {
            position: absolute;
            top: 50px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.8rem;
            color: var(--dark);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .cover-upload-btn:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .clickable-cover-text {
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.9);
        }

        .clickable-cover-text:hover {
            color: white;
            transform: translateY(-2px);
        }
        
        .profile-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin: 30px 0;
            border: 1px solid var(--border);
            position: relative;
            z-index: 2;
        }
        
        .profile-sidebar {
            background: white;
            padding: 40px 30px;
            position: relative;
            min-height: 100%;
            border-right: 1px solid var(--border);
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: -75px auto 20px;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: var(--shadow);
            position: relative;
            z-index: 3;
            background: white;
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-avatar-placeholder {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            font-weight: bold;
            width: 100%;
            height: 100%;
        }
        
        .profile-info {
            text-align: center;
            margin-top: 20px;
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .profile-title {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .profile-email {
            opacity: 0.7;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin: 30px 0;
        }
        
        .stat-item {
            background: var(--light);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            border: 1px solid var(--border);
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .skills-container {
            margin: 20px 0;
        }
        
        .skill-tag {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            margin: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .nav-tabs-custom {
            border: none;
            background: var(--light);
            border-radius: 15px;
            padding: 5px;
            margin-bottom: 30px;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 500;
            color: var(--gray);
            transition: all 0.3s ease;
        }
        
        .nav-tabs-custom .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(141, 221, 36, 0.3);
        }
        
        .nav-tabs-custom .nav-link:hover:not(.active) {
            background: rgba(16, 185, 129, 0.1);
            color: var(--primary);
        }
        
        .tab-content {
            padding: 0 10px;
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block;
        }
        
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }
        
        .section-title {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: var(--primary);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .required:after {
            content: " *";
            color: #ef4444;
        }
        
        .form-control {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(192, 214, 26, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(158, 222, 21, 0.4);
        }
        
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            z-index: 3;
        }
        
        .input-group {
            position: relative;
        }
        
        .feature-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
            border-color: var(--primary);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.5rem;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 25px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left-color: #10b981;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-left-color: #ef4444;
        }
        
        .profile-completion {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .profile-completion::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: cover;
        }
        
        .completion-text {
            position: relative;
            z-index: 2;
        }
        
        .completion-text h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1.2rem;
        }
        
        .completion-text p {
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 15px;
            font-size: 0.95rem;
            font-weight: 400;
        }
        
        .completion-text small {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .progress {
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
        }
        
        .progress-bar {
            background: var(--progress-color);
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        .no-skills {
            color: var(--gray);
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
        
        .session-item {
            background: var(--light);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .application-item {
            background: var(--light);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .stat-card {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            nav ul {
                margin-top: 15px;
                justify-content: center;
            }

            nav ul li {
                margin: 0 10px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 18px;
            }

            .form-container {
                padding: 25px;
            }
            
            .profile-sidebar {
                padding: 30px 20px;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
            
            .profile-avatar {
                width: 120px;
                height: 120px;
                margin: -60px auto 20px;
            }
            
            .form-section {
                padding: 20px;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .hero-section {
                height: 200px;
                margin-bottom: -50px;
            }
            
            .cover-upload-btn {
                top: 10px;
                right: 10px;
                padding: 6px 12px;
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header Include -->
    <?php include 'header.php'; ?>

    <!-- Hero Section with Cover Photo -->
    <div class="hero-section">
        <?php if (!empty($user_data['cover_photo'])): ?>
            <img src="<?php echo htmlspecialchars($user_data['cover_photo']); ?>" alt="Cover Photo" class="cover-photo">
        <?php else: ?>
            <div class="cover-photo-placeholder">
                <i class="fas fa-camera me-2"></i>
                <span class="clickable-cover-text" data-bs-toggle="modal" data-bs-target="#coverPhotoModal" style="cursor: pointer; text-decoration: underline;">
                    Upload Cover Photo
                </span>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <!-- Profile Sidebar -->
                <div class="profile-sidebar">
                    <div class="profile-avatar">
                        <?php if (!empty($user_data['profile_picture'])): ?>
                            <img src="<?php echo htmlspecialchars($user_data['profile_picture']); ?>" alt="Profile Picture">
                        <?php else: ?>
                            <div class="profile-avatar-placeholder">
                                <?php 
                                $initial = strtoupper(substr($user_data['fullname'] ?? 'U', 0, 1));
                                echo $initial;
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-info">
                        <h3 class="profile-name"><?php echo htmlspecialchars($_SESSION['fullname'] ?? $user_data['fullname'] ?? 'User'); ?></h3>
                        <p class="profile-title">
                            <?php 
                            $core_industry = $portfolio_data['core_industry'] ?? $user_data['core_industry'] ?? '';
                            if ($core_industry) {
                                echo htmlspecialchars(getIndustryName($core_industry));
                            } else {
                                echo 'Professional';
                            }
                            ?>
                        </p>
                        <p class="profile-email"><?php echo htmlspecialchars($user_data['email'] ?? ''); ?></p>
                        <p class="mb-0"><small>Member since <?php echo date('F Y', strtotime($user_data['created_at'] ?? 'now')); ?></small></p>
                    </div>
                    
                    <!-- Skills Section - FIXED: Now properly displays skills from portfolio -->
                    <div class="skills-container">
                        <h6 class="section-title" style="font-size: 1rem; margin-bottom: 15px;">
                            <i class="fas fa-star"></i>Skills & Expertise
                        </h6>
                        <?php if (!empty($user_skills) && count($user_skills) > 0): ?>
                            <div class="skills-list">
                                <?php foreach($user_skills as $skill): ?>
                                    <?php if (!empty(trim($skill))): ?>
                                        <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-skills">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                No skills added yet. 
                                <a href="add_portfolio.php" class="text-primary">Add skills in portfolio</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $portfolio_stats['total_portfolios']; ?></div>
                            <div class="stat-label">Total Portfolios</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $portfolio_stats['submitted_portfolios']; ?></div>
                            <div class="stat-label">Submitted</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $profile_completion; ?>%</div>
                            <div class="stat-label">Profile Complete</div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="add_portfolio.php" class="btn btn-primary btn-sm mb-2">
                            <i class="fas fa-edit me-2"></i>Edit Professional Profile
                        </a>
                        
                        <?php
                        $hasPortfolio = checkExistingPortfolio($user_id);
                        if ($hasPortfolio): ?>
                            <a href="update_portfolio.php" class="btn btn-info btn-sm">
                                <i class="fas fa-folder-open me-2"></i>Update Portfolio
                            </a>
                        <?php else: ?>
                            <a href="add_portfolio.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-2"></i>Create Portfolio
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="profile-container">
                    <!-- Profile Completion - FIXED: Now shows accurate percentage -->
                    <div class="profile-completion">
                        <div class="completion-text">
                            <h5 class="mb-2">Profile Completion</h5>
                            <p class="mb-3">Complete your profile to increase your chances of getting hired by showcasing your skills and experience to employers.</p>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $profile_completion; ?>%"></div>
                            </div>
                            <small><?php echo $profile_completion; ?>% Complete - <?php echo $profile_completion >= 80 ? 'Excellent! Your profile looks great!' : ($profile_completion >= 50 ? 'Good progress! Keep adding more details.' : 'Keep going! Add more information to complete your profile.'); ?></small>
                        </div>
                    </div>
                    
                    <!-- Success/Error Messages -->
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                <i class="fas fa-user me-2"></i>Personal
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                                <i class="fas fa-lock me-2"></i>Security
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" type="button" role="tab" aria-controls="professional" aria-selected="false">
                                <i class="fas fa-briefcase me-2"></i>Professional
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="portfolios-tab" data-bs-toggle="tab" data-bs-target="#portfolios" type="button" role="tab" aria-controls="portfolios" aria-selected="false">
                                <i class="fas fa-file-alt me-2"></i>Portfolios
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabsContent">
                        <!-- Personal Information Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <form method="POST" action="profile.php" enctype="multipart/form-data" id="personalForm">
                                <!-- Profile Picture Section -->
                                <div class="form-section">
                                    <h4 class="section-title">
                                        <i class="fas fa-camera"></i>Profile Picture
                                    </h4>
                                    <div class="row align-items-center">
                                        <div class="col-md-3 text-center">
                                            <div class="profile-avatar" style="width: 100px; height: 100px;">
                                                <?php if (!empty($user_data['profile_picture'])): ?>
                                                    <img src="<?php echo htmlspecialchars($user_data['profile_picture']); ?>" alt="Current Profile Picture">
                                                <?php else: ?>
                                                    <div class="profile-avatar-placeholder">
                                                        <?php 
                                                        $initial = strtoupper(substr($user_data['fullname'] ?? 'U', 0, 1));
                                                        echo $initial;
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="mb-3">
                                                <label for="profile_picture" class="form-label">Upload New Photo</label>
                                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                                <small class="text-muted">JPG, PNG, GIF. Max 2MB. Recommended: 400x400px</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Basic Information -->
                                <div class="form-section">
                                    <h4 class="section-title">
                                        <i class="fas fa-info-circle"></i>Basic Information
                                    </h4>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="fullname" class="form-label required">Full Name</label>
                                            <input type="text" class="form-control" id="fullname" name="fullname" 
                                                   value="<?php echo htmlspecialchars($user_data['fullname'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label required">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" 
                                                   value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>" readonly>
                                            <small class="text-muted">Username cannot be changed</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Professional Bio</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4" 
                                                  placeholder="Describe your professional background, skills, and career goals..."><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
                                        <small class="text-muted">Maximum 500 characters. <span id="bio-counter"><?php echo strlen($user_data['bio'] ?? ''); ?>/500</span></small>
                                    </div>
                                </div>

                                <!-- Cover Photo Section -->
                                <div class="form-section">
                                    <h4 class="section-title">
                                        <i class="fas fa-image"></i>Cover Photo
                                    </h4>
                                    <div class="row align-items-center">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="cover_photo" class="form-label">Upload Cover Photo</label>
                                                <input type="file" class="form-control" id="cover_photo" name="cover_photo" accept="image/*">
                                                <small class="text-muted">JPG, PNG. Max 5MB. Recommended: 1200x300px for best display</small>
                                            </div>
                                            <?php if (!empty($user_data['cover_photo'])): ?>
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" name="remove_cover" id="remove_cover" value="1">
                                                    <label class="form-check-label" for="remove_cover">
                                                        Remove current cover photo
                                                    </label>
                                                </div>
                                                <div class="current-cover-preview mt-3">
                                                    <h6>Current Cover Photo:</h6>
                                                    <img src="<?php echo htmlspecialchars($user_data['cover_photo']); ?>" alt="Current Cover Photo" class="img-fluid rounded" style="max-height: 150px;">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            <form method="POST" action="profile.php" id="securityForm">
                                <div class="form-section">
                                    <h4 class="section-title">
                                        <i class="fas fa-key"></i>Change Password
                                    </h4>
                                    <p class="text-muted mb-4">Update your password to keep your account secure.</p>
                                    
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="current_password" name="current_password" 
                                                       placeholder="Enter current password">
                                                <span class="password-toggle" onclick="togglePassword('current_password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="new_password" class="form-label">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                                       placeholder="Minimum 6 characters">
                                                <span class="password-toggle" onclick="togglePassword('new_password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="confirm_password" class="form-label">Confirm Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                                       placeholder="Re-enter new password">
                                                <span class="password-toggle" onclick="togglePassword('confirm_password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Professional Tab -->
                        <div class="tab-pane fade" id="professional" role="tabpanel" aria-labelledby="professional-tab">
                            <!-- Professional Summary -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-chart-line"></i>Professional Summary
                                </h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number text-primary"><?php echo count($user_skills); ?></div>
                                            <div class="stat-label">Skills Added</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number text-success"><?php echo !empty($portfolio_data['core_industry']) ? 'Yes' : 'No'; ?></div>
                                            <div class="stat-label">Industry Set</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number text-info"><?php echo !empty($portfolio_data['project_management_exp']) ? 'Yes' : 'No'; ?></div>
                                            <div class="stat-label">PM Experience</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Skills & Expertise -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-star"></i>Skills & Expertise
                                </h4>
                                <?php if (!empty($user_skills)): ?>
                                    <div class="skills-display mb-4">
                                        <h6>Your Skills:</h6>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            <?php foreach($user_skills as $skill): ?>
                                                <?php if (!empty(trim($skill))): ?>
                                                    <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No skills added yet. Add your professional skills to showcase your expertise.
                                    </div>
                                <?php endif; ?>
                                <div class="text-end">
                                    <a href="add_portfolio.php" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Manage Skills
                                    </a>
                                </div>
                            </div>

                            <!-- Experience Summary -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-briefcase"></i>Experience Summary
                                </h4>
                                <?php
                                $us_experience = !empty($portfolio_data['us_experience']) ? json_decode($portfolio_data['us_experience'], true) : [];
                                $india_experience = !empty($portfolio_data['india_experience']) ? json_decode($portfolio_data['india_experience'], true) : [];
                                $total_experience = count($us_experience) + count($india_experience);
                                ?>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number text-warning"><?php echo count($us_experience); ?></div>
                                            <div class="stat-label">US Experience Entries</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number text-warning"><?php echo count($india_experience); ?></div>
                                            <div class="stat-label">India Experience Entries</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-3">
                                    <a href="add_portfolio.php" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Manage Experience
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Portfolios Tab -->
                        <div class="tab-pane fade" id="portfolios" role="tabpanel" aria-labelledby="portfolios-tab">
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-file-alt"></i>My Portfolios
                                </h4>
                                
                                <!-- Portfolio Statistics -->
                                <div class="row mb-4">
                                    <div class="col-md-4 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number text-primary"><?php echo $portfolio_stats['total_portfolios']; ?></div>
                                            <div class="stat-label">Total Portfolios</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number text-success"><?php echo $portfolio_stats['submitted_portfolios']; ?></div>
                                            <div class="stat-label">Submitted</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number text-warning"><?php echo $portfolio_stats['pending_portfolios']; ?></div>
                                            <div class="stat-label">Drafts</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Portfolio Actions -->
                                <div class="text-center mb-4">
                                    <?php if ($portfolio_stats['pending_portfolios'] > 0): ?>
                                        <a href="add_portfolio.php" class="btn btn-warning me-2">
                                            <i class="fas fa-edit me-2"></i>Continue Draft
                                        </a>
                                    <?php endif; ?>
                                    <a href="add_portfolio.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Create New Portfolio
                                    </a>
                                </div>

                                <!-- Recent Portfolio Status -->
                                <?php
                                try {
                                    $recent_portfolios_sql = "SELECT * FROM portfolios WHERE user_id = ? ORDER BY updated_at DESC LIMIT 5";
                                    $recent_stmt = $pdo->prepare($recent_portfolios_sql);
                                    $recent_stmt->execute([$user_id]);
                                    $recent_portfolios = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if ($recent_portfolios): ?>
                                        <h5 class="mb-3">Recent Portfolios</h5>
                                        <?php foreach($recent_portfolios as $portfolio): ?>
                                            <div class="application-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">Portfolio #<?php echo $portfolio['id']; ?></h6>
                                                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($portfolio['full_name']); ?></p>
                                                        <small class="text-muted">Updated: <?php echo date('M j, Y g:i A', strtotime($portfolio['updated_at'])); ?></small>
                                                    </div>
                                                    <span class="badge <?php echo $portfolio['status'] === 'submitted' ? 'bg-success' : 'bg-warning'; ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $portfolio['status'])); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No portfolios found. Create your first portfolio to showcase your skills and experience.
                                        </div>
                                    <?php endif;
                                } catch (PDOException $e) {
                                    echo '<div class="alert alert-danger">Error loading portfolios: ' . $e->getMessage() . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cover Photo Modal -->
    <div class="modal fade" id="coverPhotoModal" tabindex="-1" aria-labelledby="coverPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="coverPhotoModalLabel">Change Cover Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="coverPhotoForm">
                        <div class="mb-3">
                            <label for="modal_cover_photo" class="form-label">Select Cover Photo</label>
                            <input type="file" class="form-control" id="modal_cover_photo" name="cover_photo" accept="image/*">
                            <div class="form-text">JPG, PNG files only. Max 5MB. Recommended: 1200x300px</div>
                        </div>
                        <?php if (!empty($user_data['cover_photo'])): ?>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="modal_remove_cover" name="remove_cover" value="1">
                            <label class="form-check-label" for="modal_remove_cover">Remove current cover photo</label>
                        </div>
                        <?php endif; ?>
                        <div id="coverUploadMessage"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="uploadCoverBtn">Upload Cover Photo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Include -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        // Initialize Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize tabs
            var triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs button'));
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl);
                
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });

            // Bio character counter
            const bioTextarea = document.getElementById('bio');
            const bioCounter = document.getElementById('bio-counter');
            
            if (bioTextarea && bioCounter) {
                bioTextarea.addEventListener('input', function() {
                    bioCounter.textContent = this.value.length + '/500';
                    if (this.value.length > 500) {
                        bioCounter.classList.add('text-danger');
                    } else {
                        bioCounter.classList.remove('text-danger');
                    }
                });
            }

            // Cover photo modal handling
            const coverPhotoModal = document.getElementById('coverPhotoModal');
            if (coverPhotoModal) {
                coverPhotoModal.addEventListener('show.bs.modal', function () {
                    document.getElementById('coverUploadMessage').innerHTML = '';
                });
            }

            // Form validation for all forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (form.id === 'securityForm') {
                        const newPassword = document.getElementById('new_password');
                        const confirmPassword = document.getElementById('confirm_password');
                        const currentPassword = document.getElementById('current_password');
                        
                        if (newPassword && confirmPassword && currentPassword) {
                            if (newPassword.value) {
                                if (newPassword.value.length < 6) {
                                    e.preventDefault();
                                    alert('New password must be at least 6 characters long.');
                                    return false;
                                }
                                
                                if (newPassword.value !== confirmPassword.value) {
                                    e.preventDefault();
                                    alert('New passwords do not match.');
                                    return false;
                                }
                                
                                if (!currentPassword.value) {
                                    e.preventDefault();
                                    alert('Please enter your current password to change your password.');
                                    return false;
                                }
                            }
                        }
                    }
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
                        submitBtn.disabled = true;
                    }
                });
            });
        });

        // Password visibility toggle
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.parentNode.querySelector('.fa');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>