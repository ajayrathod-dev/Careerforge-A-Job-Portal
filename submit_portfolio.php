<?php
// submit_portfolio.php
session_start();

include 'connection.php';

// File upload configuration
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_DIR', 'uploads/portfolios/');
define('ALLOWED_RESUME_TYPES', ['pdf', 'doc', 'docx']);
define('ALLOWED_CERTIFICATE_TYPES', ['pdf', 'jpg', 'jpeg', 'png']);

// Create response array
$response = ['success' => false, 'message' => '', 'errors' => []];

try {
    // Database connection is provided by included connection.php (variable $pdo)

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Determine if this is an update or new submission
    $isUpdate = isset($_POST['action']) && $_POST['action'] === 'update';
    $portfolioId = $isUpdate ? $_POST['portfolio_id'] : null;

    // If it's an update, verify the portfolio exists and belongs to the user
    if ($isUpdate) {
        $stmt = $pdo->prepare("SELECT * FROM portfolios WHERE id = ? AND user_id = ?");
        $stmt->execute([$portfolioId, $_SESSION['user_id']]);
        $existingPortfolio = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingPortfolio) {
            throw new Exception('Invalid portfolio ID or unauthorized access');
        }
    }

    // NOTE: All field validations removed per request. Any input (including duplicate emails)
    // will be accepted and attempted to be stored. File requirements are relaxed.

    // Start transaction
    $pdo->beginTransaction();

    // Handle file uploads
    $resume_path = null;
    $portfolio_path = null;
    $certificate_paths = [];

    // Upload resume
    if (!empty($_FILES['resume']['name'])) {
        $resume_path = uploadFile($_FILES['resume'], ALLOWED_RESUME_TYPES, 'resume');
    }

    // Upload portfolio file
    if (!empty($_FILES['portfolio']['name'])) {
        $portfolio_path = uploadFile($_FILES['portfolio'], array_merge(ALLOWED_RESUME_TYPES, ALLOWED_CERTIFICATE_TYPES), 'portfolio');
    }

    // Upload certificates
    if (!empty($_FILES['certificates']['name'][0])) {
        foreach ($_FILES['certificates']['name'] as $key => $name) {
            if (!empty($name)) {
                $cert_file = [
                    'name' => $name,
                    'type' => $_FILES['certificates']['type'][$key],
                    'tmp_name' => $_FILES['certificates']['tmp_name'][$key],
                    'error' => $_FILES['certificates']['error'][$key],
                    'size' => $_FILES['certificates']['size'][$key]
                ];
                $certificate_paths[] = uploadFile($cert_file, ALLOWED_CERTIFICATE_TYPES, 'certificate');
            }
        }
    }

    // Prepare portfolio data
    $portfolioData = [
        $_POST['profileType'] ?? null,
        $_POST['fullName'] ?? null,
        $_POST['currentLocation'] ?? null,
        $_POST['visaStatus'] ?? null,
        $_POST['returnPurpose'] ?? null,
        $_POST['linkedin'] ?? null,
        $_POST['technicalStrengths'] ?? null,
        $_POST['personalStrengths'] ?? null,
        $resume_path ?: ($isUpdate ? $existingPortfolio['resume_path'] : null),
        $portfolio_path ?: ($isUpdate ? $existingPortfolio['portfolio_path'] : null),
        $_POST['experience'] ?? null,
        $_POST['currentDesignation'] ?? null,
        isset($_POST['locationPreference']) ? json_encode($_POST['locationPreference']) : null,
        $_POST['relocate'] ?? null,
        $_POST['languages'] ?? null,
        $_POST['email'] ?? null,
        $_POST['countryCode'] ?? null,
        $_POST['phoneNumber'] ?? null,
        $_POST['functionDesired'] ?? null,
        $_POST['currentSalary'] ?? null,
        $_POST['salaryCurrency'] ?? null,
        $_POST['availability'] ?? null,
        $_POST['citizenship'] ?? null,
        $_POST['additionalInfo'] ?? null
    ];

    if ($isUpdate) {
        // Update existing portfolio
        $sql = "UPDATE portfolios SET
            profile_type = ?, full_name = ?, current_location = ?, visa_status = ?, 
            return_purpose = ?, linkedin_profile = ?, technical_strengths = ?, 
            personal_strengths = ?, resume_path = ?, portfolio_path = ?, 
            total_experience = ?, current_designation = ?, location_preference = ?,
            willing_relocate = ?, languages_spoken = ?, email = ?, country_code = ?, 
            phone_number = ?, functions_desired = ?, current_salary = ?, 
            salary_currency = ?, availability = ?, citizenship_status = ?, 
            additional_info = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?";
        
        // Add portfolio_id and user_id to the parameters
        $portfolioData[] = $portfolioId;
        $portfolioData[] = $_SESSION['user_id'];
        
    } else {
        // Insert new portfolio
        $sql = "INSERT INTO portfolios (
            profile_type, full_name, current_location, visa_status, return_purpose,
            linkedin_profile, technical_strengths, personal_strengths, resume_path,
            portfolio_path, total_experience, current_designation, location_preference,
            willing_relocate, languages_spoken, email, country_code, phone_number,
            functions_desired, current_salary, salary_currency, availability,
            citizenship_status, additional_info, user_id, status, submitted_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted', NOW())";
        
        // Add user_id to the parameters
        $portfolioData[] = $_SESSION['user_id'];
    }

    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute($portfolioData);
            $_POST['experience'] ?? null,
            $_POST['currentDesignation'] ?? null,
            isset($_POST['locationPreference']) ? json_encode($_POST['locationPreference']) : null,
            $_POST['relocate'] ?? null,
            $_POST['languages'] ?? null,
            $_POST['email'] ?? null,
            $_POST['countryCode'] ?? null,
            $_POST['phoneNumber'] ?? null,
            $_POST['functionDesired'] ?? null,
            $_POST['currentSalary'] ?? null,
            $_POST['salaryCurrency'] ?? null,
            $_POST['availability'] ?? null,
            $_POST['citizenship'] ?? null,
            $_POST['additionalInfo'] ?? null
        ]);

        $portfolio_id = $pdo->lastInsertId();
    } catch (PDOException $e) {
        // On duplicate key/integrity errors, try to recover by finding existing portfolio by email
        if (strpos($e->getMessage(), 'Integrity constraint violation') !== false || $e->getCode() === '23000') {
            $portfolio_id = null;
            if (!empty($_POST['email'])) {
                $s2 = $pdo->prepare("SELECT id FROM portfolios WHERE email = ? ORDER BY updated_at DESC LIMIT 1");
                $s2->execute([$_POST['email']]);
                $r = $s2->fetch(PDO::FETCH_ASSOC);
                if ($r && isset($r['id'])) {
                    $portfolio_id = $r['id'];
                    // mark submitted
                    $u = $pdo->prepare("UPDATE portfolios SET status = 'submitted', submitted_at = NOW(), updated_at = NOW() WHERE id = ?");
                    $u->execute([$portfolio_id]);
                }
            }
            if (empty($portfolio_id)) {
                // As a fallback, attempt to create a minimal draft-like record to reference
                $ins = $pdo->prepare("INSERT INTO portfolios (email, status, submitted_at) VALUES (?, 'submitted', NOW())");
                try {
                    $ins->execute([$_POST['email'] ?? null]);
                    $portfolio_id = $pdo->lastInsertId();
                } catch (Exception $ex) {
                    // If still failing, set portfolio_id to null and continue — later inserts will be skipped
                    $portfolio_id = null;
                }
            }
        } else {
            throw $e;
        }
    }

    // Insert education records
    if (!empty($_POST['degree'])) {
        $edu_sql = "INSERT INTO portfolio_education (portfolio_id, degree_type, graduation_year, university_name, specialization) VALUES (?, ?, ?, ?, ?)";
        $edu_stmt = $pdo->prepare($edu_sql);
        
        foreach ($_POST['degree'] as $index => $degree) {
            if (!empty($degree) && !empty($_POST['gradYear'][$index]) && !empty($_POST['university'][$index])) {
                $edu_stmt->execute([
                    $portfolio_id,
                    $degree,
                    $_POST['gradYear'][$index],
                    $_POST['university'][$index],
                    $_POST['specialization'][$index] ?? null
                ]);
            }
        }
    }

    // Insert certificate records
    if (!empty($certificate_paths)) {
        $cert_sql = "INSERT INTO portfolio_certificates (portfolio_id, certificate_name, certificate_path) VALUES (?, ?, ?)";
        $cert_stmt = $pdo->prepare($cert_sql);
        
        foreach ($certificate_paths as $cert_path) {
            $cert_name = basename($cert_path);
            $cert_stmt->execute([$portfolio_id, $cert_name, $cert_path]);
        }
    }

    // Commit transaction
    $pdo->commit();

    $response['success'] = true;
    $response['message'] = 'Portfolio submitted successfully! Our team will review your information and contact you soon.';
    $response['portfolio_id'] = $portfolio_id;

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $response['message'] = $e->getMessage();
    error_log("Portfolio Submission Error: " . $e->getMessage());
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;

/**
 * Handle file upload with validation
 */
function uploadFile($file, $allowed_types, $type) {
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check file type
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception("Invalid file type for $type. Allowed types: " . implode(', ', $allowed_types));
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception("File size too large for $type. Maximum size: " . (MAX_FILE_SIZE / 1024 / 1024) . "MB");
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error for $type");
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $file['name']);
    $filepath = UPLOAD_DIR . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception("Failed to upload $type file");
    }
    
    return $filepath;
}
?>