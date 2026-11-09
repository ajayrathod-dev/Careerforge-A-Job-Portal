<?php
// action.php - Complete file with all database operations
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'connection.php';

// Function to send JSON response and exit
function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Handle different actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save_draft':
            saveDraft();
            break;
        case 'submit_portfolio':
            submitPortfolio();
            break;
        case 'update_field':
            updateField();
            break;
        default:
            sendJsonResponse(['success' => false, 'error' => 'Invalid action']);
            break;
    }
}

function isPortfolioComplete($portfolio_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM portfolios WHERE id = ?");
    $stmt->execute([$portfolio_id]);
    $portfolio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$portfolio) return false;
    
    $required_fields = [
        'profile_type', 'full_name', 'current_location', 'return_purpose',
        'technical_strengths', 'personal_strengths', 'total_experience',
        'current_designation', 'location_preference', 'willing_relocate',
        'languages_spoken', 'country_code', 'phone_number', 'email',
        'functions_desired', 'current_salary', 'salary_currency', 'availability',
        'citizenship_status', 'core_industry', 'category', 'subcategory',
        'project_management_exp', 'cross_industry_exp'
    ];
    
    foreach ($required_fields as $field) {
        if (empty($portfolio[$field]) || $portfolio[$field] === '[]' || $portfolio[$field] === '0.00') {
            return false;
        }
    }
    
    // Check if resume is uploaded
    if (empty($portfolio['resume_path'])) {
        return false;
    }
    
    return true;
}

function ensureDraftPortfolioId($user_id) {
    global $pdo;
    // Try to use provided draft_id first
    $portfolio_id = $_POST['draft_id'] ?? '';
    if (!empty($portfolio_id)) {
        return $portfolio_id;
    }
    // Find existing pending draft
    $stmt = $pdo->prepare("SELECT id FROM portfolios WHERE user_id = ? AND status = 'pending' ORDER BY submitted_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['id'])) {
        return $row['id'];
    }
    // Create minimal pending draft with valid defaults including new columns
    $placeholderEmail = sprintf('user_%d_%s@placeholder.invalid', $user_id, time());
    
    $defaults = [
        'profile_type' => '1',
        'full_name' => '',
        'current_location' => '',
        'visa_status' => null,
        'return_purpose' => '',
        'linkedin_profile' => null,
        'technical_strengths' => '',
        'personal_strengths' => '',
        'resume_path' => null,
        'portfolio_path' => null,
        'total_experience' => 0.0,
        'current_designation' => '',
        'location_preference' => '[]',
        'willing_relocate' => 'No',
        'languages_spoken' => '',
        'email' => $placeholderEmail,
        'country_code' => '',
        'phone_number' => '',
        'functions_desired' => '',
        'current_salary' => 0.00,
        'salary_currency' => 'INR',
        'availability' => '',
        'citizenship_status' => '',
        'additional_info' => null,
        // New professional experience fields
        'core_industry' => '',
        'category' => '',
        'subcategory' => '',
        'selected_skills' => '[]',
        'project_management_exp' => 'no',
        'project_management_skills' => '',
        'cross_industry_exp' => 'no',
        'us_experience' => '[]',
        'india_experience' => '[]',
    ];

    $stmt = $pdo->prepare(
        "INSERT INTO portfolios (
            profile_type, full_name, current_location, visa_status, return_purpose, linkedin_profile,
            technical_strengths, personal_strengths, resume_path, portfolio_path, total_experience,
            current_designation, location_preference, willing_relocate, languages_spoken, email,
            country_code, phone_number, functions_desired, current_salary, salary_currency,
            availability, citizenship_status, additional_info, status, submitted_at, user_id,
            core_industry, category, subcategory, selected_skills, project_management_exp,
            project_management_skills, cross_industry_exp, us_experience, india_experience
        ) VALUES (
            :profile_type, :full_name, :current_location, :visa_status, :return_purpose, :linkedin_profile,
            :technical_strengths, :personal_strengths, :resume_path, :portfolio_path, :total_experience,
            :current_designation, :location_preference, :willing_relocate, :languages_spoken, :email,
            :country_code, :phone_number, :functions_desired, :current_salary, :salary_currency,
            :availability, :citizenship_status, :additional_info, 'pending', NOW(), :user_id,
            :core_industry, :category, :subcategory, :selected_skills, :project_management_exp,
            :project_management_skills, :cross_industry_exp, :us_experience, :india_experience
        )"
    );

    $params = $defaults;
    $params['user_id'] = $user_id;
    $stmt->execute($params);
    return $pdo->lastInsertId();
}

function updateField() {
    global $pdo;
    try {
        if (!isset($_SESSION['user_id'])) {
            sendJsonResponse(['success' => false, 'error' => 'Unauthorized']);
        }

        $user_id = $_SESSION['user_id'];
        $portfolio_id = ensureDraftPortfolioId($user_id);

        // File fields (resume / portfolio)
        if ((isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) || (isset($_FILES['portfolio']) && $_FILES['portfolio']['error'] === UPLOAD_ERR_OK)) {
            $messages = [];
            
            // Reuse existing upload logic but scoped to specified file
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/resumes/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                $file_name = time() . '_' . basename($_FILES['resume']['name']);
                $file_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path)) {
                    $stmt = $pdo->prepare("UPDATE portfolios SET resume_path = ?, updated_at = NOW() WHERE id = ? AND user_id = ? AND status = 'pending'");
                    $stmt->execute([$file_path, $portfolio_id, $user_id]);
                    $messages[] = "Resume uploaded successfully";
                }
            }
            if (isset($_FILES['portfolio']) && $_FILES['portfolio']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/portfolios/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                $file_name = time() . '_' . basename($_FILES['portfolio']['name']);
                $file_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['portfolio']['tmp_name'], $file_path)) {
                    $stmt = $pdo->prepare("UPDATE portfolios SET portfolio_path = ?, updated_at = NOW() WHERE id = ? AND user_id = ? AND status = 'pending'");
                    $stmt->execute([$file_path, $portfolio_id, $user_id]);
                    $messages[] = "Portfolio uploaded successfully";
                }
            }

            // Check if portfolio is complete after file upload
            if (isPortfolioComplete($portfolio_id)) {
                $stmt = $pdo->prepare("UPDATE portfolios SET status = 'complete', updated_at = NOW() WHERE id = ? AND user_id = ?");
                $stmt->execute([$portfolio_id, $user_id]);
                $messages[] = "Portfolio status updated to complete";
            }

            sendJsonResponse([
                'success' => true, 
                'portfolio_id' => $portfolio_id,
                'messages' => $messages
            ]);
        }

        // Regular fields
        $field = $_POST['field'] ?? '';
        if ($field === '') {
            sendJsonResponse(['success' => false, 'error' => 'Missing field']);
        }

        // Map form field names to DB columns
        $fieldMap = [
            'profileType' => 'profile_type',
            'firstName' => 'full_name', // Will be combined with lastName
            'lastName' => 'full_name',  // Will be combined with firstName
            'fullName' => 'full_name',  // Direct full name update
            'currentLocation' => 'current_location',
            'visaHeld' => 'visa_status',
            'returnPurpose' => 'return_purpose',
            'linkedin' => 'linkedin_profile',
            'technicalStrengths' => 'technical_strengths',
            'personalStrengths' => 'personal_strengths',
            'experience' => 'total_experience',
            'currentDesignation' => 'current_designation',
            'locationPreference' => 'location_preference',
            'relocate' => 'willing_relocate',
            'languages' => 'languages_spoken',
            'countryCode' => 'country_code',
            'phoneNumber' => 'phone_number',
            'email' => 'email',
            'functionsDesired' => 'functions_desired',
            'currentSalary' => 'current_salary',
            'salaryCurrency' => 'salary_currency',
            'availability' => 'availability',
            'citizenship' => 'citizenship_status',
            'additionalInfo' => 'additional_info',
            // Professional Experience
            'core_industry' => 'core_industry',
            'category' => 'category',
            'subcategory' => 'subcategory',
            'selected_skills' => 'selected_skills',
            'project_management_exp' => 'project_management_exp',
            'project_management_skills' => 'project_management_skills',
            'cross_industry_exp' => 'cross_industry_exp',
            'us_experience' => 'us_experience',
            'india_experience' => 'india_experience',
        ];

        if (!isset($fieldMap[$field])) {
            sendJsonResponse(['success' => false, 'error' => 'Unsupported field']);
        }

        $column = $fieldMap[$field];

        // Resolve value
        $value = $_POST['value'] ?? null;

        // Handle first name and last name combination for full_name
        if ($field === 'firstName' || $field === 'lastName') {
            // Get current full_name from database
            $stmt = $pdo->prepare("SELECT full_name FROM portfolios WHERE id = ? AND user_id = ?");
            $stmt->execute([$portfolio_id, $user_id]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            $current_full_name = $current['full_name'] ?? '';
            
            // Split current full name
            $names = explode(' ', $current_full_name, 2);
            $current_first_name = $names[0] ?? '';
            $current_last_name = $names[1] ?? '';
            
            // Update the appropriate part
            if ($field === 'firstName') {
                $current_first_name = $value;
            } else {
                $current_last_name = $value;
            }
            
            // Combine back to full name
            $value = trim($current_first_name . ' ' . $current_last_name);
            $column = 'full_name';
        }

        // NOTE: email validation intentionally removed to allow any email input (per request).

        // JSON encode for arrays/complex
        $jsonFields = ['location_preference','selected_skills','us_experience','india_experience'];
        if (in_array($column, $jsonFields, true)) {
            // Value may come as JSON string or array
            if (is_string($value)) {
                // if it's a JSON string, accept as-is after validation
                $decoded = json_decode($value, true);
                $value = json_last_error() === JSON_ERROR_NONE ? json_encode($decoded) : json_encode([$value]);
            } else {
                $value = json_encode($value ?? []);
            }
        }

        $stmt = $pdo->prepare("UPDATE portfolios SET {$column} = ?, updated_at = NOW() WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$value, $portfolio_id, $user_id]);
        
        $messages = ["Field {$field} updated successfully"];

        // Check if portfolio is complete after field update
        if (isPortfolioComplete($portfolio_id)) {
            $stmt = $pdo->prepare("UPDATE portfolios SET status = 'complete', updated_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$portfolio_id, $user_id]);
            $messages[] = "Portfolio status updated to complete";
        }

        sendJsonResponse([
            'success' => true, 
            'portfolio_id' => $portfolio_id, 
            'field' => $field,
            'messages' => $messages
        ]);
    } catch (Exception $e) {
        sendJsonResponse(['success' => false, 'error' => $e->getMessage()]);
    }
}

function saveDraft() {
    global $pdo;
    
    try {
        $user_id = $_SESSION['user_id'];
        
        // Combine first name and last name into full_name
        $first_name = $_POST['firstName'] ?? '';
        $last_name = $_POST['lastName'] ?? '';
        $full_name = trim($first_name . ' ' . $last_name);
        
        // Get all form data including professional experience
        $profile_type = $_POST['profileType'] ?? '';
        $current_location = $_POST['currentLocation'] ?? '';
        $visa_status = $_POST['visaHeld'] ?? '';
        $return_purpose = $_POST['returnPurpose'] ?? '';
        $linkedin_profile = $_POST['linkedin'] ?? '';
        $technical_strengths = $_POST['technicalStrengths'] ?? '';
        $personal_strengths = $_POST['personalStrengths'] ?? '';
        $total_experience = $_POST['experience'] ?? '';
        $current_designation = $_POST['currentDesignation'] ?? '';
        $location_preference = isset($_POST['locationPreference']) ? json_encode($_POST['locationPreference']) : '[]';
        $willing_relocate = $_POST['relocate'] ?? '';
        $languages_spoken = $_POST['languages'] ?? '';
        $country_code = $_POST['countryCode'] ?? '';
        $phone_number = $_POST['phoneNumber'] ?? '';
        $email = $_POST['email'] ?? '';
        $functions_desired = $_POST['functionsDesired'] ?? '';
        $current_salary = $_POST['currentSalary'] ?? '';
        $salary_currency = $_POST['salaryCurrency'] ?? '';
        $availability = $_POST['availability'] ?? '';
        $citizenship_status = $_POST['citizenship'] ?? '';
        $additional_info = $_POST['additionalInfo'] ?? '';
        
        // Professional Experience fields
        $core_industry = $_POST['core_industry'] ?? '';
        $category = $_POST['category'] ?? '';
        $subcategory = $_POST['subcategory'] ?? '';
        $selected_skills = isset($_POST['selected_skills']) ? json_encode($_POST['selected_skills']) : '[]';
        $project_management_exp = $_POST['project_management_exp'] ?? '';
        $project_management_skills = $_POST['project_management_skills'] ?? '';
        $cross_industry_exp = $_POST['cross_industry_exp'] ?? '';
        $us_experience = isset($_POST['us_experience']) ? json_encode($_POST['us_experience']) : '[]';
        $india_experience = isset($_POST['india_experience']) ? json_encode($_POST['india_experience']) : '[]';
        
        // Handle "other" fields
        if ($current_location === 'other') {
            $current_location = $_POST['currentLocation_other'] ?? '';
        }
        if ($visa_status === 'others') {
            $visa_status = $_POST['visaHeld_other'] ?? '';
        }
        if ($return_purpose === 'others') {
            $return_purpose = $_POST['returnPurpose_other'] ?? '';
        }
        if ($functions_desired === 'other') {
            $functions_desired = $_POST['functionsDesired_other'] ?? '';
        }
        if ($citizenship_status === 'other') {
            $citizenship_status = $_POST['citizenship_other'] ?? '';
        }
        if ($country_code === 'other') {
            $country_code = $_POST['countryCode_other'] ?? '';
        }
        if ($core_industry === 'other') {
            $core_industry = $_POST['core_industry_other'] ?? '';
        }
        if ($category === 'other') {
            $category = $_POST['category_other'] ?? '';
        }
        if ($subcategory === 'other') {
            $subcategory = $_POST['subcategory_other'] ?? '';
        }
        
        // NOTE: removed email validation for draft saves so any email (including duplicates) will be accepted.

        $draft_id = $_POST['draft_id'] ?? '';
        $messages = [];
        
        if (!empty($draft_id)) {
            // Update existing draft with professional experience fields
            $stmt = $pdo->prepare("UPDATE portfolios SET 
                profile_type = ?, full_name = ?, current_location = ?, visa_status = ?, 
                return_purpose = ?, linkedin_profile = ?, technical_strengths = ?, 
                personal_strengths = ?, total_experience = ?, current_designation = ?, 
                location_preference = ?, willing_relocate = ?, languages_spoken = ?, 
                country_code = ?, phone_number = ?, email = ?, functions_desired = ?, 
                current_salary = ?, salary_currency = ?, availability = ?, 
                citizenship_status = ?, additional_info = ?,
                core_industry = ?, category = ?, subcategory = ?, selected_skills = ?,
                project_management_exp = ?, project_management_skills = ?, cross_industry_exp = ?,
                us_experience = ?, india_experience = ?, updated_at = NOW() 
                WHERE id = ? AND user_id = ? AND status = 'pending'");
            
            $stmt->execute([
                $profile_type, $full_name, $current_location, $visa_status,
                $return_purpose, $linkedin_profile, $technical_strengths,
                $personal_strengths, $total_experience, $current_designation,
                $location_preference, $willing_relocate, $languages_spoken,
                $country_code, $phone_number, $email, $functions_desired,
                $current_salary, $salary_currency, $availability,
                $citizenship_status, $additional_info,
                $core_industry, $category, $subcategory, $selected_skills,
                $project_management_exp, $project_management_skills, $cross_industry_exp,
                $us_experience, $india_experience, $draft_id, $user_id
            ]);
            
            $portfolio_id = $draft_id;
            $messages[] = "Draft updated successfully";
        } else {
            // Insert new draft with professional experience fields
            $stmt = $pdo->prepare("INSERT INTO portfolios 
                (user_id, profile_type, full_name, current_location, visa_status, 
                return_purpose, linkedin_profile, technical_strengths, personal_strengths, 
                total_experience, current_designation, location_preference, willing_relocate, 
                languages_spoken, country_code, phone_number, email, functions_desired, 
                current_salary, salary_currency, availability, citizenship_status, 
                additional_info, core_industry, category, subcategory, selected_skills,
                project_management_exp, project_management_skills, cross_industry_exp,
                us_experience, india_experience, status, submitted_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            
            try {
                $stmt->execute([
                    $user_id, $profile_type, $full_name, $current_location, $visa_status,
                    $return_purpose, $linkedin_profile, $technical_strengths,
                    $personal_strengths, $total_experience, $current_designation,
                    $location_preference, $willing_relocate, $languages_spoken,
                    $country_code, $phone_number, $email, $functions_desired,
                    $current_salary, $salary_currency, $availability,
                    $citizenship_status, $additional_info,
                    $core_industry, $category, $subcategory, $selected_skills,
                    $project_management_exp, $project_management_skills, $cross_industry_exp,
                    $us_experience, $india_experience
                ]);

                $portfolio_id = $pdo->lastInsertId();
                $messages[] = "New draft created successfully";
            } catch (PDOException $e) {
                // If duplicate key / integrity constraint error occurs, try to recover by using an existing portfolio
                if (strpos($e->getMessage(), 'Integrity constraint violation') !== false || $e->getCode() === '23000') {
                    // Try to find an existing portfolio with same email
                    $portfolio_id = null;
                    if (!empty($email)) {
                        $stmt2 = $pdo->prepare("SELECT id FROM portfolios WHERE email = ? ORDER BY updated_at DESC LIMIT 1");
                        $stmt2->execute([$email]);
                        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                        if ($row && isset($row['id'])) {
                            $portfolio_id = $row['id'];
                            $messages[] = "Duplicate email detected — using existing portfolio id {$portfolio_id}";
                        }
                    }
                    // If still no portfolio id, ensure/create a draft for the user
                    if (empty($portfolio_id)) {
                        $portfolio_id = ensureDraftPortfolioId($user_id);
                        $messages[] = "Duplicate key on insert — created/using draft id {$portfolio_id}";
                    }
                } else {
                    throw $e;
                }
            }
        }
        
        // Handle file uploads
        $fileMessages = handleFileUploads($portfolio_id);
        $messages = array_merge($messages, $fileMessages);
        
        // Handle education entries
        $educationMessages = handleEducationEntries($portfolio_id);
        $messages = array_merge($messages, $educationMessages);
        
        // Handle certificates
        $certificateMessages = handleCertificates($portfolio_id);
        $messages = array_merge($messages, $certificateMessages);

        // Check if portfolio is complete
        if (isPortfolioComplete($portfolio_id)) {
            $stmt = $pdo->prepare("UPDATE portfolios SET status = 'complete', updated_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$portfolio_id, $user_id]);
            $messages[] = "Portfolio status updated to complete";
        }
        
        sendJsonResponse([
            'success' => true, 
            'portfolio_id' => $portfolio_id,
            'messages' => $messages
        ]);
        
    } catch (Exception $e) {
        sendJsonResponse(['success' => false, 'error' => $e->getMessage()]);
    }
}

function submitPortfolio() {
    global $pdo;
    try {
        $user_id = $_SESSION['user_id'];

        // Combine first name and last name into full_name
        $first_name = $_POST['firstName'] ?? '';
        $last_name = $_POST['lastName'] ?? '';
        $full_name = trim($first_name . ' ' . $last_name);

        // Gather all fields (same as saveDraft)
        $profile_type = $_POST['profileType'] ?? '';
        $current_location = $_POST['currentLocation'] ?? '';
        $visa_status = $_POST['visaHeld'] ?? '';
        $return_purpose = $_POST['returnPurpose'] ?? '';
        $linkedin_profile = $_POST['linkedin'] ?? '';
        $technical_strengths = $_POST['technicalStrengths'] ?? '';
        $personal_strengths = $_POST['personalStrengths'] ?? '';
        $total_experience = $_POST['experience'] ?? '';
        $current_designation = $_POST['currentDesignation'] ?? '';
        $location_preference = isset($_POST['locationPreference']) ? json_encode($_POST['locationPreference']) : '[]';
        $willing_relocate = $_POST['relocate'] ?? '';
        $languages_spoken = $_POST['languages'] ?? '';
        $country_code = $_POST['countryCode'] ?? '';
        $phone_number = $_POST['phoneNumber'] ?? '';
        $email = $_POST['email'] ?? '';
        $functions_desired = $_POST['functionsDesired'] ?? '';
        $current_salary = $_POST['currentSalary'] ?? '';
        $salary_currency = $_POST['salaryCurrency'] ?? '';
        $availability = $_POST['availability'] ?? '';
        $citizenship_status = $_POST['citizenship'] ?? '';
        $additional_info = $_POST['additionalInfo'] ?? '';

        // Professional Experience fields
        $core_industry = $_POST['core_industry'] ?? '';
        $category = $_POST['category'] ?? '';
        $subcategory = $_POST['subcategory'] ?? '';
        $selected_skills = isset($_POST['selected_skills']) ? json_encode($_POST['selected_skills']) : '[]';
        $project_management_exp = $_POST['project_management_exp'] ?? '';
        $project_management_skills = $_POST['project_management_skills'] ?? '';
        $cross_industry_exp = $_POST['cross_industry_exp'] ?? '';
        $us_experience = isset($_POST['us_experience']) ? json_encode($_POST['us_experience']) : '[]';
        $india_experience = isset($_POST['india_experience']) ? json_encode($_POST['india_experience']) : '[]';

        // Handle "other" fields
        if ($current_location === 'other') {
            $current_location = $_POST['currentLocation_other'] ?? '';
        }
        if ($visa_status === 'others') {
            $visa_status = $_POST['visaHeld_other'] ?? '';
        }
        if ($return_purpose === 'others') {
            $return_purpose = $_POST['returnPurpose_other'] ?? '';
        }
        if ($functions_desired === 'other') {
            $functions_desired = $_POST['functionsDesired_other'] ?? '';
        }
        if ($citizenship_status === 'other') {
            $citizenship_status = $_POST['citizenship_other'] ?? '';
        }
        if ($country_code === 'other') {
            $country_code = $_POST['countryCode_other'] ?? '';
        }
        if ($core_industry === 'other') {
            $core_industry = $_POST['core_industry_other'] ?? '';
        }
        if ($category === 'other') {
            $category = $_POST['category_other'] ?? '';
        }
        if ($subcategory === 'other') {
            $subcategory = $_POST['subcategory_other'] ?? '';
        }

        $draft_id = $_POST['draft_id'] ?? '';
        $messages = [];

        if (!empty($draft_id)) {
            // Update existing draft with all fields
            $stmt = $pdo->prepare("UPDATE portfolios SET 
                profile_type = ?, full_name = ?, current_location = ?, visa_status = ?, 
                return_purpose = ?, linkedin_profile = ?, technical_strengths = ?, 
                personal_strengths = ?, total_experience = ?, current_designation = ?, 
                location_preference = ?, willing_relocate = ?, languages_spoken = ?, 
                country_code = ?, phone_number = ?, email = ?, functions_desired = ?, 
                current_salary = ?, salary_currency = ?, availability = ?, 
                citizenship_status = ?, additional_info = ?,
                core_industry = ?, category = ?, subcategory = ?, selected_skills = ?,
                project_management_exp = ?, project_management_skills = ?, cross_industry_exp = ?,
                us_experience = ?, india_experience = ?, status = 'submitted', submitted_at = NOW(), updated_at = NOW()
                WHERE id = ? AND user_id = ?");

            $stmt->execute([
                $profile_type, $full_name, $current_location, $visa_status,
                $return_purpose, $linkedin_profile, $technical_strengths,
                $personal_strengths, $total_experience, $current_designation,
                $location_preference, $willing_relocate, $languages_spoken,
                $country_code, $phone_number, $email, $functions_desired,
                $current_salary, $salary_currency, $availability,
                $citizenship_status, $additional_info,
                $core_industry, $category, $subcategory, $selected_skills,
                $project_management_exp, $project_management_skills, $cross_industry_exp,
                $us_experience, $india_experience, $draft_id, $user_id
            ]);

            $portfolio_id = $draft_id;
            $messages[] = "Portfolio submitted successfully";
        } else {
            // Insert new submitted portfolio with all fields
            $stmt = $pdo->prepare("INSERT INTO portfolios 
                (user_id, profile_type, full_name, current_location, visa_status, 
                return_purpose, linkedin_profile, technical_strengths, personal_strengths, 
                total_experience, current_designation, location_preference, willing_relocate, 
                languages_spoken, country_code, phone_number, email, functions_desired, 
                current_salary, salary_currency, availability, citizenship_status, 
                additional_info, core_industry, category, subcategory, selected_skills,
                project_management_exp, project_management_skills, cross_industry_exp,
                us_experience, india_experience, status, submitted_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted', NOW())");

            try {
                $stmt->execute([
                    $user_id, $profile_type, $full_name, $current_location, $visa_status,
                    $return_purpose, $linkedin_profile, $technical_strengths,
                    $personal_strengths, $total_experience, $current_designation,
                    $location_preference, $willing_relocate, $languages_spoken,
                    $country_code, $phone_number, $email, $functions_desired,
                    $current_salary, $salary_currency, $availability,
                    $citizenship_status, $additional_info,
                    $core_industry, $category, $subcategory, $selected_skills,
                    $project_management_exp, $project_management_skills, $cross_industry_exp,
                    $us_experience, $india_experience
                ]);

                $portfolio_id = $pdo->lastInsertId();
                $messages[] = "New portfolio submitted successfully";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Integrity constraint violation') !== false || $e->getCode() === '23000') {
                    // Try to find existing portfolio by email
                    $portfolio_id = null;
                    if (!empty($email)) {
                        $stmt2 = $pdo->prepare("SELECT id FROM portfolios WHERE email = ? ORDER BY updated_at DESC LIMIT 1");
                        $stmt2->execute([$email]);
                        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                        if ($row && isset($row['id'])) {
                            $portfolio_id = $row['id'];
                            // Mark it as submitted
                            $u = $pdo->prepare("UPDATE portfolios SET status = 'submitted', submitted_at = NOW(), updated_at = NOW() WHERE id = ?");
                            $u->execute([$portfolio_id]);
                            $messages[] = "Duplicate email detected — updated existing portfolio id {$portfolio_id} to submitted";
                        }
                    }
                    if (empty($portfolio_id)) {
                        // Fallback: create or use draft
                        $portfolio_id = ensureDraftPortfolioId($user_id);
                        $messages[] = "Duplicate key on insert — using draft id {$portfolio_id}";
                    }
                } else {
                    throw $e;
                }
            }
        }

        // Handle files, education, certificates
        $fileMessages = handleFileUploads($portfolio_id);
        $messages = array_merge($messages, $fileMessages);
        
        $educationMessages = handleEducationEntries($portfolio_id);
        $messages = array_merge($messages, $educationMessages);
        
        $certificateMessages = handleCertificates($portfolio_id);
        $messages = array_merge($messages, $certificateMessages);

        sendJsonResponse([
            'success' => true, 
            'portfolio_id' => $portfolio_id,
            'messages' => $messages
        ]);
    } catch (Exception $e) {
        sendJsonResponse(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleFileUploads($portfolio_id) {
    global $pdo;
    $messages = [];
    
    // Handle resume upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/resumes/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $file_name = time() . '_' . basename($_FILES['resume']['name']);
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path)) {
            $stmt = $pdo->prepare("UPDATE portfolios SET resume_path = ? WHERE id = ?");
            $stmt->execute([$file_path, $portfolio_id]);
            $messages[] = "Resume file uploaded";
        }
    }
    
    // Handle portfolio upload
    if (isset($_FILES['portfolio']) && $_FILES['portfolio']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/portfolios/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $file_name = time() . '_' . basename($_FILES['portfolio']['name']);
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['portfolio']['tmp_name'], $file_path)) {
            $stmt = $pdo->prepare("UPDATE portfolios SET portfolio_path = ? WHERE id = ?");
            $stmt->execute([$file_path, $portfolio_id]);
            $messages[] = "Portfolio file uploaded";
        }
    }
    
    return $messages;
}

function handleEducationEntries($portfolio_id) {
    global $pdo;
    $messages = [];
    
    // Delete existing education entries
    $stmt = $pdo->prepare("DELETE FROM portfolio_education WHERE portfolio_id = ?");
    $stmt->execute([$portfolio_id]);
    
    // Insert new education entries
    if (isset($_POST['degree']) && is_array($_POST['degree'])) {
        $degrees = $_POST['degree'];
        $gradYears = $_POST['gradYear'] ?? [];
        $universities = $_POST['university'] ?? [];
        $specializations = $_POST['specialization'] ?? [];
        $degree_others = $_POST['degree_other'] ?? [];
        
        $stmt = $pdo->prepare("INSERT INTO portfolio_education (portfolio_id, degree_type, graduation_year, university_name, specialization) VALUES (?, ?, ?, ?, ?)");
        
        $count = 0;
        for ($i = 0; $i < count($degrees); $i++) {
            if (!empty($degrees[$i]) && !empty($gradYears[$i]) && !empty($universities[$i])) {
                $degree_type = $degrees[$i];
                if ($degree_type === 'Others' && isset($degree_others[$i])) {
                    $degree_type = $degree_others[$i];
                }
                
                $stmt->execute([
                    $portfolio_id,
                    $degree_type,
                    $gradYears[$i],
                    $universities[$i],
                    $specializations[$i] ?? ''
                ]);
                $count++;
            }
        }
        if ($count > 0) {
            $messages[] = "Education entries updated";
        }
    }
    
    return $messages;
}

function handleCertificates($portfolio_id) {
    global $pdo;
    $messages = [];
    
    // Handle remove certificates
    if (isset($_POST['remove_certs']) && is_array($_POST['remove_certs'])) {
        $placeholders = str_repeat('?,', count($_POST['remove_certs']) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM portfolio_certificates WHERE id IN ($placeholders)");
        $stmt->execute($_POST['remove_certs']);
        $messages[] = "Certificates removed";
    }
    
    // Handle certificate file uploads
    if (isset($_FILES['certificates']) && is_array($_FILES['certificates']['name'])) {
        $upload_dir = 'uploads/certificates/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $stmt = $pdo->prepare("INSERT INTO portfolio_certificates (portfolio_id, certificate_name, certificate_path) VALUES (?, ?, ?)");
        
        $count = 0;
        foreach ($_FILES['certificates']['name'] as $key => $name) {
            if ($_FILES['certificates']['error'][$key] === UPLOAD_ERR_OK) {
                $file_name = time() . '_' . $key . '_' . basename($name);
                $file_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['certificates']['tmp_name'][$key], $file_path)) {
                    $stmt->execute([$portfolio_id, $name, $file_path]);
                    $count++;
                }
            }
        }
        if ($count > 0) {
            $messages[] = "Certificates uploaded";
        }
    }
    
    // Handle remove resume
    if (isset($_POST['remove_resume'])) {
        $stmt = $pdo->prepare("UPDATE portfolios SET resume_path = NULL WHERE id = ?");
        $stmt->execute([$portfolio_id]);
        $messages[] = "Resume removed";
    }
    
    // Handle remove portfolio
    if (isset($_POST['remove_portfolio'])) {
        $stmt = $pdo->prepare("UPDATE portfolios SET portfolio_path = NULL WHERE id = ?");
        $stmt->execute([$portfolio_id]);
        $messages[] = "Portfolio removed";
    }
    
    return $messages;
}
?>