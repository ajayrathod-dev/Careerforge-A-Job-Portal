<?php
// add_portfolio.php - Minimal PHP for session and data loading
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'connection.php';

// Helper functions to split full name
function getFirstName($fullName) {
    if (empty($fullName)) return '';
    $names = explode(' ', trim($fullName));
    return $names[0] ?? '';
}

function getLastName($fullName) {
    if (empty($fullName)) return '';
    $names = explode(' ', trim($fullName));
    array_shift($names); // Remove first name
    return implode(' ', $names);
}

// First try to load an existing pending draft for the user
$stmt = $pdo->prepare("SELECT * FROM portfolios WHERE user_id = ? AND status = 'pending' ORDER BY updated_at DESC, submitted_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$draft = $stmt->fetch(PDO::FETCH_ASSOC);

// If no pending draft found, attempt to load the most recent portfolio (any status)
if (!$draft) {
    $stmt = $pdo->prepare("SELECT * FROM portfolios WHERE user_id = ? ORDER BY updated_at DESC, submitted_at DESC LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        // Use the existing portfolio to pre-fill the form (user intent: edit/update)
        $draft = $existing;
        $isExistingPortfolio = true;
    } else {
        $isExistingPortfolio = false;
    }
} else {
    $isExistingPortfolio = false; // explicit: this is a draft not a submitted portfolio
}

// Initialize $draft as empty array if no portfolio or draft exists
if (!$draft) {
    $draft = [
        'id' => '',
        'profile_type' => '',
        'full_name' => '',
        'current_location' => '',
        'visa_status' => '',
        'return_purpose' => '',
        'linkedin_profile' => '',
        'technical_strengths' => '',
        'personal_strengths' => '',
        'resume_path' => '',
        'portfolio_path' => '',
        'total_experience' => '',
        'current_designation' => '',
        'location_preference' => [],
        'willing_relocate' => '',
        'languages_spoken' => '',
        'country_code' => '',
        'phone_number' => '',
        'email' => '',
        'functions_desired' => '',
        'current_salary' => '',
        'salary_currency' => '',
        'availability' => '',
        'citizenship_status' => '',
        'additional_info' => '',
        'education' => [],
        'certificates' => [],
        // Professional Experience fields
        'core_industry' => '',
        'category' => '',
        'subcategory' => '',
        'selected_skills' => [],
        'project_management_exp' => '',
        'project_management_skills' => '',
        'cross_industry_exp' => '',
        'us_experience' => [],
        'india_experience' => []
    ];
}

// If we have a portfolio/draft id, load education and certificates and decode JSON fields
if (!empty($draft['id'])) {
    $portfolio_id = $draft['id'];
    $stmt = $pdo->prepare("SELECT * FROM portfolio_education WHERE portfolio_id = ?");
    $stmt->execute([$portfolio_id]);
    $draft['education'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM portfolio_certificates WHERE portfolio_id = ?");
    $stmt->execute([$portfolio_id]);
    $draft['certificates'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Safe handling for JSON fields
    $jsonFields = [
        'location_preference' => [],
        'selected_skills' => [],
        'us_experience' => [],
        'india_experience' => []
    ];
    foreach ($jsonFields as $field => $default) {
        if (isset($draft[$field])) {
            if (is_string($draft[$field])) {
                $decoded = json_decode($draft[$field], true);
                $draft[$field] = is_array($decoded) ? $decoded : $default;
            } elseif (!is_array($draft[$field])) {
                $draft[$field] = $default;
            }
        } else {
            $draft[$field] = $default;
        }
    }
}

// If we loaded an existing (non-pending) portfolio, don't force a redirect.
// We'll show a visible banner above the form so the user can choose to edit the existing portfolio
// or continue with the pre-filled "add" form.

// Check for success or error parameters in URL
$submission_success = isset($_GET['success']) && $_GET['success'] == '1';
$portfolio_id = $_GET['portfolio_id'] ?? '';
$submission_error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Submit your professional portfolio to connect with top employers worldwide through Order Tech Careers">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">

    <title>Professional Portfolio Submission | ORDER TECH</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome CDN with multiple fallbacks -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-MXxJHkPKt9gXvN3oS8H7AXH5Ncc+bwCTZcG4Dh7Pf3F+HjPtfGdQmAA1eM7t2sV+jEXsWzTukBi4RzP9Y3vUZg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Alternative FontAwesome CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.0/css/all.css">
    
    <!-- Local fallback for FontAwesome -->
    <style>
    /* FontAwesome fallback icons using Unicode */
    .fa-fallback::before {
        font-family: Arial, sans-serif;
        font-weight: bold;
    }
    .fa-user::before { content: "👤"; }
    .fa-envelope::before { content: "✉️"; }
    .fa-phone::before { content: "📞"; }
    .fa-briefcase::before { content: "💼"; }
    .fa-user-tie::before { content: "👔"; }
    .fa-map-marker-alt::before { content: "📍"; }
    .fa-passport::before { content: "📘"; }
    .fa-plane-arrival::before { content: "🛬"; }
    .fa-globe::before { content: "🌎"; }
    .fa-industry::before { content: "🏭"; }
    .fa-cogs::before { content: "⚙️"; }
    .fa-tasks::before { content: "📋"; }
    .fa-exchange-alt::before { content: "🔄"; }
    .fa-flag-usa::before { content: "🇺🇸"; }
    .fa-flag::before { content: "🇮🇳"; }
    .fa-id-badge::before { content: "🪪"; }
    .fa-tools::before { content: "🛠️"; }
    .fa-user-check::before { content: "✅"; }
    .fa-dollar-sign::before { content: "💵"; }
    .fa-map::before { content: "🗺️"; }
    .fa-truck-moving::before { content: "🚚"; }
    .fa-calendar-check::before { content: "📅"; }
    .fa-language::before { content: "🔤"; }
    .fa-graduation-cap::before { content: "🎓"; }
    .fa-plus::before { content: "➕"; }
    .fa-times::before { content: "✖️"; }
    .fa-info-circle::before { content: "ℹ️"; }
    .fa-paper-plane::before { content: "📨"; }
    .fa-save::before { content: "💾"; }
    .fa-file-upload::before { content: "📤"; }
    .fa-file-pdf::before { content: "📄"; }
    .fa-folder-open::before { content: "📁"; }
    .fa-certificate::before { content: "🏅"; }
    .fa-edit::before { content: "✏️"; }
    .fa-home::before { content: "🏠"; }
    .fa-exclamation-triangle::before { content: "⚠️"; }
    .fa-linkedin::before { content: "💼"; }
    </style>

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    
    <!-- Portfolio Specific CSS -->
    <link rel="stylesheet" href="portfolio.css">
    
    <style>
    .other-input-container {
        margin-top: 8px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #2563eb;
        animation: fadeIn 0.3s ease;
    }

    .other-input-label {
        font-weight: 500;
        margin-bottom: 5px;
        color: #495057;
        font-size: 0.9rem;
        display: block;
    }

    .experience-input {
        transition: border-color 0.3s ease;
    }

    .experience-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .btn-circle {
        border-radius: 50%;
        width: 38px;
        height: 38px;
        font-size: 22px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes tickPop {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.3); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    .tick-anim {
        animation: tickPop 0.6s ease;
        color: #28a745;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        border-bottom: 2px solid #2563eb;
        padding-bottom: 12px;
    }
    
    .section-header .section-title {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .section-header .section-title::after {
        display: none;
    }

    .floating-draft-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 1000;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        background-color: #fff;
        color: #555;
        border: 2px solid #ddd;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .floating-draft-btn:hover {
        background-color: #f0f0f0;
        border-color: #2563eb;
        color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    /* Debug styles */
    .icon-test {
        padding: 10px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #f9f9f9;
    }
    </style>
</head>
<body>
   

    <!-- Header Include -->
    <?php include 'header.php'; ?>

    <!-- Light Blue Hero Section -->
    <div class="page-header">
        <div class="container">
            <h1>Professional Portfolio Submission</h1>
            <p>Showcase your skills and experience to connect with global opportunities</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if ($submission_success): ?>
                    <!-- Success Message -->
                    <div class="form-container">
                        <div class="thank-you-container">
                            <div>
                                <div class="success-icon">🎉</div>
                                <h2>Thank You For Your Submission!</h2>
                                <p class="lead">Your professional portfolio has been successfully submitted.</p>
                                <p>Your portfolio ID is: <strong class="text-primary"><?php echo htmlspecialchars($portfolio_id); ?></strong></p>
                                <p>Our global recruitment team will review your profile and contact you for matching opportunities.</p>
                                <div class="mt-4">
                                    <a href="index-2.php" class="btn btn-primary me-3">
                                        <i class="fas fa-home me-2"></i> Back to Home
                                    </a>
                                    <a href="jobs.php" class="btn btn-outline-primary">
                                        <i class="fas fa-briefcase me-2"></i> Browse Jobs
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Display errors if any -->
                    <?php if (!empty($submission_error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Submission Error!</strong> <?php echo htmlspecialchars($submission_error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Check for existing pending portfolio -->
                    <?php if ($draft && !empty($draft['id'])): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-info-circle me-2"></i>Pending Portfolio Found!</strong> You have an existing pending portfolio (ID: <?php echo htmlspecialchars($draft['id']); ?>). Please complete it below or contact support to delete it.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Draft Status Indicator -->
                    <div class="draft-status-indicator mb-4">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-save me-2"></i>
                            <div>
                                <strong>Auto-save enabled</strong>
                                <div class="small">Your progress is automatically saved as you type</div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Area Above Form -->
                    <div id="formNotificationArea"></div>

                    <?php if (!empty($isExistingPortfolio) && $isExistingPortfolio && !empty($draft['id'])): ?>
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Existing portfolio detected.</strong>
                                You already have a portfolio (ID: <?php echo htmlspecialchars($draft['id']); ?>).
                                You can edit that portfolio or continue editing this pre-filled form.
                            </div>
                            <div>
                                <a href="update_portfolio.php?portfolio_id=<?php echo urlencode($draft['id']); ?>" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-edit me-1"></i>Edit existing portfolio
                                </a>
                                <a href="#" class="btn btn-outline-secondary btn-sm" onclick="this.closest('.alert').style.display='none'; return false;">
                                    Continue here
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Premium Portfolio Form - Single Page Layout -->
                    <div class="form-container">
                        <form id="portfolioForm" method="POST" action="action.php" enctype="multipart/form-data" novalidate data-action="<?php echo !empty($draft['id']) ? 'update' : 'create'; ?>">
                            <input type="hidden" name="draft_id" value="<?php echo htmlspecialchars($draft['id'] ?? ''); ?>">
                            <input type="hidden" name="action" value="<?php echo !empty($draft['id']) ? 'update' : 'create'; ?>">
                            <input type="hidden" name="portfolio_id" value="<?php echo htmlspecialchars($draft['id'] ?? ''); ?>">

                            <!-- Profile Type Section -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-user-tie me-2"></i>Profile Type
                                </h4>
                                
                                <div class="option-card" onclick="selectProfileType('profileType1')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="profileType" id="profileType1" value="1" <?php if (isset($draft['profile_type']) && $draft['profile_type'] == '1') echo 'checked'; ?> required>
                                        <label class="form-check-label" for="profileType1">
                                            Candidates currently in the US and looking for jobs in India
                                        </label>
                                    </div>
                                </div>

                                <div class="option-card" onclick="selectProfileType('profileType2')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="profileType" id="profileType2" value="2" <?php if (isset($draft['profile_type']) && $draft['profile_type'] == '2') echo 'checked'; ?> required>
                                        <label class="form-check-label" for="profileType2">
                                            Candidates with US work experience currently in India and looking for jobs in India
                                        </label>
                                    </div>
                                </div>

                                <div class="option-card" onclick="selectProfileType('profileType3')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="profileType" id="profileType3" value="3" <?php if (isset($draft['profile_type']) && $draft['profile_type'] == '3') echo 'checked'; ?> required>
                                        <label class="form-check-label" for="profileType3">
                                            Candidates currently pursuing studies in the US and looking for jobs in India
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information Section -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="firstName" class="form-label"><i class="fas fa-user"></i> First Name *</label>
                                            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars(getFirstName($draft['full_name'] ?? '')); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="lastName" class="form-label"><i class="fas fa-user"></i> Last Name *</label>
                                            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars(getLastName($draft['full_name'] ?? '')); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email *</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($draft['email'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="countryCode" class="form-label"><i class="fas fa-phone"></i> Country Code *</label>
                                            <select class="form-select" id="countryCode" name="countryCode" required onchange="toggleOther('countryCode', 'countryCodeOtherContainer', 'other')">
                                                <option value="">Select Code</option>
                                                <option value="+1" <?php if (isset($draft['country_code']) && $draft['country_code'] == '+1') echo 'selected'; ?>>+1 (USA/Canada)</option>
                                                <option value="+91" <?php if (isset($draft['country_code']) && $draft['country_code'] == '+91') echo 'selected'; ?>>+91 (India)</option>
                                                <option value="+44" <?php if (isset($draft['country_code']) && $draft['country_code'] == '+44') echo 'selected'; ?>>+44 (UK)</option>
                                                <option value="+61" <?php if (isset($draft['country_code']) && $draft['country_code'] == '+61') echo 'selected'; ?>>+61 (Australia)</option>
                                                <option value="+49" <?php if (isset($draft['country_code']) && $draft['country_code'] == '+49') echo 'selected'; ?>>+49 (Germany)</option>
                                                <option value="other" <?php if (isset($draft['country_code']) && !in_array($draft['country_code'], ['+1', '+91', '+44', '+61', '+49', ''])) echo 'selected'; ?>>Other</option>
                                            </select>
                                            <div id="countryCodeOtherContainer" class="other-input-container" style="<?php if (isset($draft['country_code']) && !in_array($draft['country_code'], ['+1', '+91', '+44', '+61', '+49', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                                <label for="countryCode_other" class="other-input-label">Specify Country Code</label>
                                                <input type="text" class="form-control" id="countryCode_other" name="countryCode_other" value="<?php if (isset($draft['country_code']) && !in_array($draft['country_code'], ['+1', '+91', '+44', '+61', '+49', ''])) echo htmlspecialchars($draft['country_code']); ?>" placeholder="e.g., +33">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="phoneNumber" class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($draft['phone_number'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="linkedin" class="form-label"><i class="fab fa-linkedin"></i> LinkedIn Profile</label>
                                    <input type="text" class="form-control" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($draft['linkedin_profile'] ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Location & Visa Information -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-map-marker-alt me-2"></i>Location & Visa Information
                                </h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="currentLocation" class="form-label"><i class="fas fa-map-marker-alt"></i> Current Location *</label>
                                            <select class="form-select" id="currentLocation" name="currentLocation" required onchange="toggleOther('currentLocation', 'currentLocationOtherContainer', 'other')">
                                                <option value="">Select Location</option>
                                                <option value="US" <?php if (isset($draft['current_location']) && $draft['current_location'] == 'US') echo 'selected'; ?>>US</option>
                                                <option value="India" <?php if (isset($draft['current_location']) && $draft['current_location'] == 'India') echo 'selected'; ?>>India</option>
                                                <option value="other" <?php if (isset($draft['current_location']) && !in_array($draft['current_location'], ['US', 'India', ''])) echo 'selected'; ?>>Other (mention)</option>
                                            </select>
                                            <div id="currentLocationOtherContainer" class="other-input-container" style="<?php if (isset($draft['current_location']) && !in_array($draft['current_location'], ['US', 'India', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                                <label for="currentLocation_other" class="other-input-label">Mention</label>
                                                <input type="text" class="form-control" id="currentLocation_other" name="currentLocation_other" value="<?php if (isset($draft['current_location']) && !in_array($draft['current_location'], ['US', 'India', ''])) echo htmlspecialchars($draft['current_location']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="visaHeld" class="form-label"><i class="fas fa-passport"></i> Visa Held (If US)</label>
                                            <select class="form-select" id="visaHeld" name="visaHeld" onchange="toggleOther('visaHeld', 'visaHeldOtherContainer', 'others')">
                                                <option value="">Select Visa</option>
                                                <option value="F1" <?php if (isset($draft['visa_status']) && $draft['visa_status'] == 'F1') echo 'selected'; ?>>F1</option>
                                                <option value="H1" <?php if (isset($draft['visa_status']) && $draft['visa_status'] == 'H1') echo 'selected'; ?>>H1</option>
                                                <option value="J1" <?php if (isset($draft['visa_status']) && $draft['visa_status'] == 'J1') echo 'selected'; ?>>J1</option>
                                                <option value="Dependent" <?php if (isset($draft['visa_status']) && $draft['visa_status'] == 'Dependent') echo 'selected'; ?>>Dependent</option>
                                                <option value="others" <?php if (isset($draft['visa_status']) && !in_array($draft['visa_status'], ['F1', 'H1', 'J1', 'Dependent', ''])) echo 'selected'; ?>>Others</option>
                                            </select>
                                            <div id="visaHeldOtherContainer" class="other-input-container" style="<?php if (isset($draft['visa_status']) && !in_array($draft['visa_status'], ['F1', 'H1', 'J1', 'Dependent', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                                <label for="visaHeld_other" class="other-input-label">Specify Other Visa</label>
                                                <input type="text" class="form-control" id="visaHeld_other" name="visaHeld_other" value="<?php if (isset($draft['visa_status']) && !in_array($draft['visa_status'], ['F1', 'H1', 'J1', 'Dependent', ''])) echo htmlspecialchars($draft['visa_status']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="returnPurpose" class="form-label"><i class="fas fa-plane-arrival"></i> Purpose of Returning to India? *</label>
                                    <select class="form-select" id="returnPurpose" name="returnPurpose" required onchange="toggleOther('returnPurpose', 'returnPurposeOtherContainer', 'others')">
                                        <option value="">Select Purpose</option>
                                        <option value="Laid off" <?php if (isset($draft['return_purpose']) && $draft['return_purpose'] == 'Laid off') echo 'selected'; ?>>Laid off</option>
                                        <option value="Immigration issues related to visa" <?php if (isset($draft['return_purpose']) && $draft['return_purpose'] == 'Immigration issues related to visa') echo 'selected'; ?>>Immigration issues related to visa</option>
                                        <option value="Grace period over" <?php if (isset($draft['return_purpose']) && $draft['return_purpose'] == 'Grace period over') echo 'selected'; ?>>Grace period over</option>
                                        <option value="Personal reasons" <?php if (isset($draft['return_purpose']) && $draft['return_purpose'] == 'Personal reasons') echo 'selected'; ?>>Personal reasons</option>
                                        <option value="others" <?php if (isset($draft['return_purpose']) && !in_array($draft['return_purpose'], ['Laid off', 'Immigration issues related to visa', 'Grace period over', 'Personal reasons', ''])) echo 'selected'; ?>>Others (mention)</option>
                                    </select>
                                    <div id="returnPurposeOtherContainer" class="other-input-container" style="<?php if (isset($draft['return_purpose']) && !in_array($draft['return_purpose'], ['Laid off', 'Immigration issues related to visa', 'Grace period over', 'Personal reasons', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                        <label for="returnPurpose_other" class="other-input-label">Mention</label>
                                        <input type="text" class="form-control" id="returnPurpose_other" name="returnPurpose_other" value="<?php if (isset($draft['return_purpose']) && !in_array($draft['return_purpose'], ['Laid off', 'Immigration issues related to visa', 'Grace period over', 'Personal reasons', ''])) echo htmlspecialchars($draft['return_purpose']); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="citizenship" class="form-label"><i class="fas fa-globe"></i> Current Citizenship Status *</label>
                                    <select class="form-select" id="citizenship" name="citizenship" required onchange="toggleOther('citizenship', 'citizenshipOtherContainer', 'other')">
                                        <option value="">Select Status</option>
                                        <option value="NRI (Indian Citizen)" <?php if (isset($draft['citizenship_status']) && $draft['citizenship_status'] == 'NRI (Indian Citizen)') echo 'selected'; ?>>NRI (Indian Citizen)</option>
                                        <option value="GC Holder" <?php if (isset($draft['citizenship_status']) && $draft['citizenship_status'] == 'GC Holder') echo 'selected'; ?>>GC Holder</option>
                                        <option value="OCI" <?php if (isset($draft['citizenship_status']) && $draft['citizenship_status'] == 'OCI') echo 'selected'; ?>>OCI</option>
                                        <option value="PIO" <?php if (isset($draft['citizenship_status']) && $draft['citizenship_status'] == 'PIO') echo 'selected'; ?>>PIO</option>
                                        <option value="Will need authorization to work in India" <?php if (isset($draft['citizenship_status']) && $draft['citizenship_status'] == 'Will need authorization to work in India') echo 'selected'; ?>>Will need authorization to work in India</option>
                                        <option value="other" <?php if (isset($draft['citizenship_status']) && !in_array($draft['citizenship_status'], ['NRI (Indian Citizen)', 'GC Holder', 'OCI', 'PIO', 'Will need authorization to work in India', ''])) echo 'selected'; ?>>Other (mention)</option>
                                    </select>
                                    <div id="citizenshipOtherContainer" class="other-input-container" style="<?php if (isset($draft['citizenship_status']) && !in_array($draft['citizenship_status'], ['NRI (Indian Citizen)', 'GC Holder', 'OCI', 'PIO', 'Will need authorization to work in India', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                        <label for="citizenship_other" class="other-input-label">Mention</label>
                                        <input type="text" class="form-control" id="citizenship_other" name="citizenship_other" value="<?php if (isset($draft['citizenship_status']) && !in_array($draft['citizenship_status'], ['NRI (Indian Citizen)', 'GC Holder', 'OCI', 'PIO', 'Will need authorization to work in India', ''])) echo htmlspecialchars($draft['citizenship_status']); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Professional Experience Section -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-briefcase me-2"></i>Professional Experience
                                </h4>
                                
                                <!-- A. Core Industry Selection -->
                                <div class="mb-4">
                                    <label for="core_industry" class="form-label"><i class="fas fa-industry"></i> Core Industry *</label>
                                    <select name="core_industry" id="core_industry" class="form-select" required onchange="toggleOther('core_industry', 'coreIndustryOtherContainer', 'other')">
                                        <option value="">Select Industry</option>
                                        <option value="CS" <?php echo (($draft['core_industry'] ?? '') == 'CS') ? 'selected' : ''; ?>>Computer Science (CS)</option>
                                        <option value="IT" <?php echo (($draft['core_industry'] ?? '') == 'IT') ? 'selected' : ''; ?>>Information Technology (IT)</option>
                                        <option value="EXTC" <?php echo (($draft['core_industry'] ?? '') == 'EXTC') ? 'selected' : ''; ?>>Electronics and Telecommunication (EXTC)</option>
                                        <option value="Mechanical" <?php echo (($draft['core_industry'] ?? '') == 'Mechanical') ? 'selected' : ''; ?>>Mechanical Engineering (Mechanical)</option>
                                        <option value="MFG" <?php echo (($draft['core_industry'] ?? '') == 'MFG') ? 'selected' : ''; ?>>Manufacturing (MFG)</option>
                                        <option value="other" <?php echo (($draft['core_industry'] ?? '') && !in_array($draft['core_industry'], ['CS', 'IT', 'EXTC', 'Mechanical', 'MFG', ''])) ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <div id="coreIndustryOtherContainer" class="other-input-container" style="<?php if (($draft['core_industry'] ?? '') && !in_array($draft['core_industry'], ['CS', 'IT', 'EXTC', 'Mechanical', 'MFG', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                        <label for="core_industry_other" class="other-input-label">Specify Other Industry</label>
                                        <input type="text" class="form-control" id="core_industry_other" name="core_industry_other" value="<?php if (($draft['core_industry'] ?? '') && !in_array($draft['core_industry'], ['CS', 'IT', 'EXTC', 'Mechanical', 'MFG', ''])) echo htmlspecialchars($draft['core_industry']); ?>">
                                    </div>
                                </div>

                                <!-- B. Dynamic Category and Subcategory System -->
                                <div class="mb-4">
                                    <label class="form-label"><i class="fas fa-cogs"></i> Select Skill Sets</label>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <select name="category" id="category" class="form-select" onchange="toggleOther('category', 'categoryOtherContainer', 'other')">
                                                <option value="">Select Category</option>
                                                <!-- Options will be loaded dynamically -->
                                            </select>
                                            <div id="categoryOtherContainer" class="other-input-container" style="display: none;">
                                                <label for="category_other" class="other-input-label">Specify Other Category</label>
                                                <input type="text" class="form-control" id="category_other" name="category_other" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="subcategory" id="subcategory" class="form-select" onchange="toggleOther('subcategory', 'subcategoryOtherContainer', 'other')">
                                                <option value="">Select Subcategory</option>
                                                <!-- Options will be loaded dynamically -->
                                            </select>
                                            <div id="subcategoryOtherContainer" class="other-input-container" style="display: none;">
                                                <label for="subcategory_other" class="other-input-label">Specify Other Subcategory</label>
                                                <input type="text" class="form-control" id="subcategory_other" name="subcategory_other" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="skills-container" class="mb-3" style="display: none;">
                                        <h6 class="form-label">Available Skills:</h6>
                                        <div id="skills-list" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                    <div id="selected-skills-container" class="mb-3 <?php echo empty($draft['selected_skills']) ? 'd-none' : ''; ?>">
                                        <h6 class="form-label">Selected Skills:</h6>
                                        <div id="selected-skills-list" class="d-flex flex-wrap gap-2">
                                            <?php if (!empty($draft['selected_skills'])): ?>
                                                <?php foreach($draft['selected_skills'] as $skill): ?>
                                                    <div class="skill-chip selected" data-skill="<?php echo htmlspecialchars($skill); ?>">
                                                        <?php echo htmlspecialchars($skill); ?>
                                                        <input type="hidden" name="selected_skills[]" value="<?php echo htmlspecialchars($skill); ?>">
                                                        <span class="ms-2 cursor-pointer" onclick="removeSkill(this)">×</span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- C. Project Management Section -->
                                <div class="mb-4">
                                    <label class="form-label"><i class="fas fa-tasks"></i> Do you have Project Management experience? *</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input type="radio" name="project_management_exp" value="yes" class="form-check-input" <?php echo (($draft['project_management_exp'] ?? '') == 'yes') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="project_management_exp" value="no" class="form-check-input" <?php echo (($draft['project_management_exp'] ?? 'no') == 'no' || empty($draft['project_management_exp'])) ? 'checked' : ''; ?> required>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                    
                                    <div id="project-management-details" class="mt-3 <?php echo (($draft['project_management_exp'] ?? '') != 'yes') ? 'd-none' : ''; ?>">
                                        <div class="mb-3">
                                            <label for="project_management_skills" class="form-label">Add Project Management Skills</label>
                                            <textarea name="project_management_skills" rows="3" class="form-control" id="project_management_skills" placeholder="Describe your project management skills and experience"><?php echo htmlspecialchars($draft['project_management_skills'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- D. Cross Industry Experience -->
                                <div class="mb-4">
                                    <label class="form-label"><i class="fas fa-exchange-alt"></i> Are you eligible/willing for cross-industry experience? *</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input type="radio" name="cross_industry_exp" value="yes" class="form-check-input" <?php echo (($draft['cross_industry_exp'] ?? '') == 'yes') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="cross_industry_exp" value="no" class="form-check-input" <?php echo (($draft['cross_industry_exp'] ?? 'no') == 'no' || empty($draft['cross_industry_exp'])) ? 'checked' : ''; ?> required>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- E. US Experience (Repeatable Section) -->
                                <div class="mb-4">
                                    <div class="section-header">
                                        <h4 class="section-title mb-0"><i class="fas fa-flag-usa me-2"></i>US Experience</h4>
                                        <button type="button" id="add-us-experience" class="btn btn-outline-primary btn-circle">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div id="us-experience-container">
                                        <?php if (!empty($draft['us_experience'])): ?>
                                            <?php foreach($draft['us_experience'] as $index => $exp): ?>
                                                <div class="experience-block" data-index="<?php echo $index; ?>">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="form-label mb-0">Experience #<?php echo $index + 1; ?></h6>
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, 'us')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Name of Employer</label>
                                                            <input type="text" name="us_experience[<?php echo $index; ?>][employer]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['employer'] ?? ''); ?>" placeholder="Employer name">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                                                            <div class="d-flex gap-2">
                                                                <input type="text" name="us_experience[<?php echo $index; ?>][from]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['from'] ?? ''); ?>" placeholder="MM/YY" maxlength="5">
                                                                <span class="align-self-center">to</span>
                                                                <input type="text" name="us_experience[<?php echo $index; ?>][to]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['to'] ?? ''); ?>" placeholder="MM/YY" maxlength="5">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">State</label>
                                                            <input type="text" name="us_experience[<?php echo $index; ?>][state]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['state'] ?? ''); ?>" placeholder="State">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">City</label>
                                                            <input type="text" name="us_experience[<?php echo $index; ?>][city]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['city'] ?? ''); ?>" placeholder="City">
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="experience-block" data-index="0">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="form-label mb-0">Experience #1</h6>
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, 'us')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Name of Employer</label>
                                                        <input type="text" name="us_experience[0][employer]" class="form-control experience-input" placeholder="Employer name">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                                                        <div class="d-flex gap-2">
                                                            <input type="text" name="us_experience[0][from]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                                                            <span class="align-self-center">to</span>
                                                            <input type="text" name="us_experience[0][to]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">State</label>
                                                        <input type="text" name="us_experience[0][state]" class="form-control experience-input" placeholder="State">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">City</label>
                                                        <input type="text" name="us_experience[0][city]" class="form-control experience-input" placeholder="City">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- F. India Experience (Repeatable Section) -->
                                <div class="mb-4">
                                    <div class="section-header">
                                        <h4 class="section-title mb-0"><i class="fas fa-flag me-2"></i>India Experience (If any)</h4>
                                        <button type="button" id="add-india-experience" class="btn btn-outline-primary btn-circle">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div id="india-experience-container">
                                        <?php if (!empty($draft['india_experience'])): ?>
                                            <?php foreach($draft['india_experience'] as $index => $exp): ?>
                                                <div class="experience-block" data-index="<?php echo $index; ?>">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="form-label mb-0">Experience #<?php echo $index + 1; ?></h6>
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, 'india')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Name of Employer</label>
                                                            <input type="text" name="india_experience[<?php echo $index; ?>][employer]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['employer'] ?? ''); ?>" placeholder="Employer name">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                                                            <div class="d-flex gap-2">
                                                                <input type="text" name="india_experience[<?php echo $index; ?>][from]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['from'] ?? ''); ?>" placeholder="MM/YY" maxlength="5">
                                                                <span class="align-self-center">to</span>
                                                                <input type="text" name="india_experience[<?php echo $index; ?>][to]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['to'] ?? ''); ?>" placeholder="MM/YY" maxlength="5">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">State</label>
                                                            <input type="text" name="india_experience[<?php echo $index; ?>][state]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['state'] ?? ''); ?>" placeholder="State">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">City</label>
                                                            <input type="text" name="india_experience[<?php echo $index; ?>][city]" class="form-control experience-input" value="<?php echo htmlspecialchars($exp['city'] ?? ''); ?>" placeholder="City">
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="experience-block" data-index="0">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="form-label mb-0">Experience #1</h6>
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-experience" onclick="removeExperienceBlock(this, 'india')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Name of Employer</label>
                                                        <input type="text" name="india_experience[0][employer]" class="form-control experience-input" placeholder="Employer name">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Duration (From MM/YY to MM/YY)</label>
                                                        <div class="d-flex gap-2">
                                                            <input type="text" name="india_experience[0][from]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                                                            <span class="align-self-center">to</span>
                                                            <input type="text" name="india_experience[0][to]" class="form-control experience-input" placeholder="MM/YY" maxlength="5">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">State</label>
                                                        <input type="text" name="india_experience[0][state]" class="form-control experience-input" placeholder="State">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">City</label>
                                                        <input type="text" name="india_experience[0][city]" class="form-control experience-input" placeholder="City">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Professional Information -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-briefcase me-2"></i>Basic Professional Information
                                </h4>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="experience" class="form-label"><i class="fas fa-briefcase"></i> Total Full-time Work Experience (Years) *</label>
                                        <input type="number" class="form-control" id="experience" name="experience" min="0" step="0.1" value="<?php echo htmlspecialchars($draft['total_experience'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="currentDesignation" class="form-label"><i class="fas fa-id-badge"></i> Current / Latest Designation *</label>
                                        <input type="text" class="form-control" id="currentDesignation" name="currentDesignation" value="<?php echo htmlspecialchars($draft['current_designation'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="technicalStrengths" class="form-label"><i class="fas fa-tools"></i> Technical Strengths *</label>
                                    <textarea class="form-control" id="technicalStrengths" name="technicalStrengths" rows="3" required><?php echo htmlspecialchars($draft['technical_strengths'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="personalStrengths" class="form-label"><i class="fas fa-user-check"></i> Personal Strengths *</label>
                                    <textarea class="form-control" id="personalStrengths" name="personalStrengths" rows="3" required><?php echo htmlspecialchars($draft['personal_strengths'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="functionsDesired" class="form-label"><i class="fas fa-tasks"></i> Functions Desired *</label>
                                    <select class="form-select" id="functionsDesired" name="functionsDesired" required onchange="toggleOther('functionsDesired', 'functionsDesiredOtherContainer', 'other')">
                                        <option value="">Select Function</option>
                                        <option value="Individual contributor" <?php if (isset($draft['functions_desired']) && $draft['functions_desired'] == 'Individual contributor') echo 'selected'; ?>>Individual contributor</option>
                                        <option value="Team member" <?php if (isset($draft['functions_desired']) && $draft['functions_desired'] == 'Team member') echo 'selected'; ?>>Team member</option>
                                        <option value="Project manager" <?php if (isset($draft['functions_desired']) && $draft['functions_desired'] == 'Project manager') echo 'selected'; ?>>Project manager</option>
                                        <option value="Leadership role" <?php if (isset($draft['functions_desired']) && $draft['functions_desired'] == 'Leadership role') echo 'selected'; ?>>Leadership role</option>
                                        <option value="other" <?php if (isset($draft['functions_desired']) && !in_array($draft['functions_desired'], ['Individual contributor', 'Team member', 'Project manager', 'Leadership role', ''])) echo 'selected'; ?>>Other (mention)</option>
                                    </select>
                                    <div id="functionsDesiredOtherContainer" class="other-input-container" style="<?php if (isset($draft['functions_desired']) && !in_array($draft['functions_desired'], ['Individual contributor', 'Team member', 'Project manager', 'Leadership role', ''])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                        <label for="functionsDesired_other" class="other-input-label">Mention</label>
                                        <input type="text" class="form-control" id="functionsDesired_other" name="functionsDesired_other" value="<?php if (isset($draft['functions_desired']) && !in_array($draft['functions_desired'], ['Individual contributor', 'Team member', 'Project manager', 'Leadership role', ''])) echo htmlspecialchars($draft['functions_desired']); ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="currentSalary" class="form-label"><i class="fas fa-dollar-sign"></i> Current / Latest Annual Salary *</label>
                                        <input type="number" class="form-control" id="currentSalary" name="currentSalary" value="<?php echo htmlspecialchars($draft['current_salary'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="salaryCurrency" class="form-label">Currency *</label>
                                        <select class="form-select" id="salaryCurrency" name="salaryCurrency" required>
                                            <option value="">Select Currency</option>
                                            <option value="USD" <?php if (isset($draft['salary_currency']) && $draft['salary_currency'] == 'USD') echo 'selected'; ?>>USD</option>
                                            <option value="INR" <?php if (isset($draft['salary_currency']) && $draft['salary_currency'] == 'INR') echo 'selected'; ?>>INR</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Job Preferences -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-map me-2"></i>Job Preferences
                                </h4>

                                <div class="mb-3">
                                    <label for="locationPreference" class="form-label"><i class="fas fa-map"></i> Location Preference in India *</label>
                                    <select class="form-select" id="locationPreference" name="locationPreference[]" multiple required>
                                        <option value="No preference" <?php if (in_array('No preference', $draft['location_preference'])) echo 'selected'; ?>>No preference</option>
                                        <option value="Andhra Pradesh" <?php if (in_array('Andhra Pradesh', $draft['location_preference'])) echo 'selected'; ?>>Andhra Pradesh</option>
                                        <option value="Karnataka" <?php if (in_array('Karnataka', $draft['location_preference'])) echo 'selected'; ?>>Karnataka</option>
                                        <option value="Tamil Nadu" <?php if (in_array('Tamil Nadu', $draft['location_preference'])) echo 'selected'; ?>>Tamil Nadu</option>
                                        <option value="Telangana" <?php if (in_array('Telangana', $draft['location_preference'])) echo 'selected'; ?>>Telangana</option>
                                        <option value="Maharashtra" <?php if (in_array('Maharashtra', $draft['location_preference'])) echo 'selected'; ?>>Maharashtra</option>
                                    </select>
                                    <div class="form-text">Hold Ctrl/Cmd to select multiple locations</div>
                                </div>

                                <div class="mb-3">
                                    <label for="relocate" class="form-label"><i class="fas fa-truck-moving"></i> Willing to Relocate within India? *</label>
                                    <select class="form-select" id="relocate" name="relocate" required>
                                        <option value="">Select</option>
                                        <option value="Yes" <?php if (isset($draft['willing_relocate']) && $draft['willing_relocate'] == 'Yes') echo 'selected'; ?>>Yes</option>
                                        <option value="No" <?php if (isset($draft['willing_relocate']) && $draft['willing_relocate'] == 'No') echo 'selected'; ?>>No</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="availability" class="form-label"><i class="fas fa-calendar-check"></i> Availability to Join in India *</label>
                                    <select class="form-select" id="availability" name="availability" required>
                                        <option value="">Select</option>
                                        <option value="Immediate" <?php if (isset($draft['availability']) && $draft['availability'] == 'Immediate') echo 'selected'; ?>>Immediate</option>
                                        <option value="Within 2 weeks" <?php if (isset($draft['availability']) && $draft['availability'] == 'Within 2 weeks') echo 'selected'; ?>>Within 2 weeks</option>
                                        <option value="Within 1 month" <?php if (isset($draft['availability']) && $draft['availability'] == 'Within 1 month') echo 'selected'; ?>>Within 1 month</option>
                                        <option value="Within 2 months" <?php if (isset($draft['availability']) && $draft['availability'] == 'Within 2 months') echo 'selected'; ?>>Within 2 months</option>
                                        <option value="Within 3 months" <?php if (isset($draft['availability']) && $draft['availability'] == 'Within 3 months') echo 'selected'; ?>>Within 3 months</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="languages" class="form-label"><i class="fas fa-language"></i> Languages Spoken *</label>
                                    <input type="text" class="form-control" id="languages" name="languages" value="<?php echo htmlspecialchars($draft['languages_spoken'] ?? ''); ?>" required>
                                    <div class="form-text">Include Indian regional languages</div>
                                </div>
                            </div>

                            <!-- Education Background -->
                            <div class="form-section">
                                <div class="section-header">
                                    <h4 class="section-title mb-0"><i class="fas fa-graduation-cap me-2"></i>Educational Background</h4>
                                    <button type="button" id="addEducation" class="btn btn-outline-primary btn-circle">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>

                                <div id="educationEntries">
                                    <?php if (!empty($draft['education'])): ?>
                                        <?php foreach ($draft['education'] as $index => $edu): ?>
                                            <div class="education-entry">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Degree</label>
                                                        <select class="form-select degree-select" name="degree[]" onchange="toggleDegreeOther(this)">
                                                            <option value="">Select Degree</option>
                                                            <option value="Bachelor's" <?php if ($edu['degree_type'] == "Bachelor's") echo 'selected'; ?>>Bachelor's</option>
                                                            <option value="Master's" <?php if ($edu['degree_type'] == "Master's") echo 'selected'; ?>>Master's</option>
                                                            <option value="PhD" <?php if ($edu['degree_type'] == 'PhD') echo 'selected'; ?>>PhD</option>
                                                            <option value="MBA" <?php if ($edu['degree_type'] == 'MBA') echo 'selected'; ?>>MBA</option>
                                                            <option value="Others" <?php if (!in_array($edu['degree_type'], ["Bachelor's", "Master's", 'PhD', 'MBA'])) echo 'selected'; ?>>Others</option>
                                                        </select>
                                                        <div class="degree-other-container other-input-container" style="<?php if (!in_array($edu['degree_type'], ["Bachelor's", "Master's", 'PhD', 'MBA'])) echo 'display: block;'; else echo 'display: none;'; ?>">
                                                            <label class="other-input-label">Specify Other Degree</label>
                                                            <input type="text" class="form-control" name="degree_other[]" value="<?php if (!in_array($edu['degree_type'], ["Bachelor's", "Master's", 'PhD', 'MBA'])) echo htmlspecialchars($edu['degree_type']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Year of Graduation</label>
                                                        <input type="number" class="form-control" name="gradYear[]" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($edu['graduation_year']); ?>">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">University Name</label>
                                                        <input type="text" class="form-control" name="university[]" value="<?php echo htmlspecialchars($edu['university_name']); ?>">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Specialization</label>
                                                        <input type="text" class="form-control" name="specialization[]" value="<?php echo htmlspecialchars($edu['specialization']); ?>">
                                                    </div>
                                                </div>
                                                <?php if ($index > 0): ?>
                                                    <button type="button" class="btn btn-outline-danger remove-education mt-2">Remove Entry</button>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="education-entry">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">Degree</label>
                                                    <select class="form-select degree-select" name="degree[]" onchange="toggleDegreeOther(this)">
                                                        <option value="">Select Degree</option>
                                                        <option value="Bachelor's">Bachelor's</option>
                                                        <option value="Master's">Master's</option>
                                                        <option value="PhD">PhD</option>
                                                        <option value="MBA">MBA</option>
                                                        <option value="Others">Others</option>
                                                    </select>
                                                    <div class="degree-other-container other-input-container" style="display: none;">
                                                        <label class="other-input-label">Specify Other Degree</label>
                                                        <input type="text" class="form-control" name="degree_other[]">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Year of Graduation</label>
                                                    <input type="number" class="form-control" name="gradYear[]" min="1900" max="<?php echo date('Y'); ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">University Name</label>
                                                    <input type="text" class="form-control" name="university[]">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Specialization</label>
                                                    <input type="text" class="form-control" name="specialization[]">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Documents Upload -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-file-upload me-2"></i>Documents Upload
                                </h4>

                                <div class="mb-3">
                                    <label for="resume" class="form-label"><i class="fas fa-file-pdf"></i> Resume *</label>
                                    <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" <?php echo empty($draft['resume_path']) ? 'required' : ''; ?>>
                                    <?php if ($draft && !empty($draft['resume_path'])): ?>
                                        <p class="mt-2">Existing Resume: <?php echo htmlspecialchars(basename($draft['resume_path'])); ?>
                                            <label class="ms-2"><input type="checkbox" name="remove_resume"> Remove</label>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="portfolio" class="form-label"><i class="fas fa-folder-open"></i> Portfolio, GitHub, Publications</label>
                                    <input type="file" class="form-control" id="portfolio" name="portfolio" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <?php if ($draft && !empty($draft['portfolio_path'])): ?>
                                        <p class="mt-2">Existing Portfolio: <?php echo htmlspecialchars(basename($draft['portfolio_path'])); ?>
                                            <label class="ms-2"><input type="checkbox" name="remove_portfolio"> Remove</label>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="certificates" class="form-label"><i class="fas fa-certificate"></i> Certificates</label>
                                    <input type="file" class="form-control" id="certificates" name="certificates[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    <?php if ($draft && !empty($draft['certificates'])): ?>
                                        <h6 class="mt-2">Existing Certificates:</h6>
                                        <ul>
                                            <?php foreach ($draft['certificates'] as $cert): ?>
                                                <li><?php echo htmlspecialchars($cert['certificate_name']); ?>
                                                    <label class="ms-2"><input type="checkbox" name="remove_certs[]" value="<?php echo $cert['id']; ?>"> Remove</label>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-info-circle me-2"></i>Additional Information
                                </h4>

                                <div class="mb-3">
                                    <label for="additionalInfo" class="form-label"><i class="fas fa-info-circle"></i> Any Other Additional Information</label>
                                    <textarea class="form-control" id="additionalInfo" name="additionalInfo" rows="3"><?php echo htmlspecialchars($draft['additional_info'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="consent" name="consent" required>
                                        <label class="form-check-label" for="consent">
                                            I consent to sharing my information with potential employers. *
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Portfolio
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Floating Draft Button -->
    <button type="button" class="save-draft floating-draft-btn">
        <i class="fas fa-save" style="font-size: 1.5rem;"></i>
    </button>

    <!-- Footer Include -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Portfolio Specific JavaScript -->
    <script src="portfolio.js"></script>
    
    <script>
    // Initialize other fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all other field containers
        const otherFields = [
            'countryCode', 'currentLocation', 'visaHeld', 'returnPurpose', 
            'citizenship', 'functionsDesired', 'core_industry'
        ];
        
        otherFields.forEach(field => {
            const select = document.getElementById(field);
            if (select) {
                const otherValue = (field === 'visaHeld' || field === 'returnPurpose') ? 'others' : 'other';
                if (select.value === otherValue || select.value === 'Others') {
                    const container = document.getElementById(field + 'OtherContainer');
                    if (container) container.style.display = 'block';
                }
            }
        });
        
        // Initialize education other fields
        document.querySelectorAll('.degree-select').forEach(select => {
            if (select.value === 'Others') {
                const container = select.closest('.education-entry').querySelector('.degree-other-container');
                if (container) container.style.display = 'block';
            }
        });

        // Add tick animation to draft button
        document.querySelector('.save-draft').addEventListener('click', function() {
            const icon = this.querySelector('i');
            const originalClass = icon.className;
            icon.className = 'fas fa-check tick-anim';
            setTimeout(() => icon.className = originalClass, 1500);
            
            // Trigger the save draft functionality
            saveEntireDraft(this);
        });

        // Test if FontAwesome is loading
        setTimeout(function() {
            const testIcon = document.querySelector('.icon-test i');
            if (testIcon && getComputedStyle(testIcon).fontFamily.includes('Font Awesome')) {
                console.log('FontAwesome is loading correctly');
            } else {
                console.log('FontAwesome not detected, using fallback icons');
                // Add fallback class to all icons
                document.querySelectorAll('i[class*="fa-"]').forEach(icon => {
                    icon.classList.add('fa-fallback');
                });
            }
        }, 1000);
    });

    // Global function to toggle other inputs
    function toggleOther(selectId, containerId, otherValue = 'other') {
        const select = document.getElementById(selectId);
        const container = document.getElementById(containerId);
        if (!select || !container) return;
        
        const input = container.querySelector('input');
        if (select.value === otherValue) {
            container.style.display = 'block';
            if (input) input.required = true;
        } else {
            container.style.display = 'none';
            if (input) {
                input.value = '';
                input.required = false;
            }
        }
    }

    // Degree other field toggle
    function toggleDegreeOther(select) {
        const container = select.closest('.education-entry').querySelector('.degree-other-container');
        if (!container) return;
        
        const input = container.querySelector('input');
        if (select.value === 'Others') {
            container.style.display = 'block';
            if (input) input.required = true;
        } else {
            container.style.display = 'none';
            if (input) {
                input.value = '';
                input.required = false;
            }
        }
    }

    // Profile type selection helper
    function selectProfileType(profileTypeId) {
        const profileTypeInput = document.getElementById(profileTypeId);
        if (profileTypeInput) {
            profileTypeInput.checked = true;
            
            // Update visual selection
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('selected');
            });
            profileTypeInput.closest('.option-card').classList.add('selected');
        }
    }
    </script>
</body>
</html>