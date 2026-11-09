<?php
include("connection.php");
include("admin_functions.php");

// Check if user is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM portfolios WHERE id = '$id'";
$result = mysqli_query($link, $query);
$portfolio = mysqli_fetch_assoc($result);

if (!$portfolio) {
    die('Portfolio not found');
}

// Fetch education details
$education_query = "SELECT * FROM portfolio_education WHERE portfolio_id = '$id'";
$education_result = mysqli_query($link, $education_query);
$education = [];
while ($edu_row = mysqli_fetch_assoc($education_result)) {
    $education[] = $edu_row;
}

// Fetch certificates
$certificates_query = "SELECT * FROM portfolio_certificates WHERE portfolio_id = '$id'";
$certificates_result = mysqli_query($link, $certificates_query);
$certificates = [];
while ($cert_row = mysqli_fetch_assoc($certificates_result)) {
    $certificates[] = $cert_row;
}

// Parse JSON fields
$selected_skills = $portfolio['selected_skills'] ? json_decode($portfolio['selected_skills'], true) : [];
$us_experience = $portfolio['us_experience'] ? json_decode($portfolio['us_experience'], true) : [];
$india_experience = $portfolio['india_experience'] ? json_decode($portfolio['india_experience'], true) : [];
$location_preference = $portfolio['location_preference'] ? json_decode($portfolio['location_preference'], true) : [];

// Handle download requests - only PDF now
if (isset($_GET['download'])) {
    $format = $_GET['format'];
    
    if ($format == 'pdf') {
        downloadAsPDF($portfolio, $education, $certificates);
    }
}

// Function to download as PDF - modified to include print button
function downloadAsPDF($portfolio, $education, $certificates) {
    $filename = "portfolio_" . $portfolio['id'] . "_" . str_replace(' ', '_', $portfolio['full_name']) . ".pdf";
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $us_experience = $portfolio['us_experience'] ? json_decode($portfolio['us_experience'], true) : [];
    $india_experience = $portfolio['india_experience'] ? json_decode($portfolio['india_experience'], true) : [];
    $selected_skills = $portfolio['selected_skills'] ? json_decode($portfolio['selected_skills'], true) : [];
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Portfolio - ' . htmlspecialchars($portfolio['full_name']) . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.4; }
            h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; text-align: center; }
            h2 { color: #34495e; margin-top: 25px; border-left: 4px solid #3498db; padding-left: 10px; }
            table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            th { background-color: #f8f9fa; text-align: left; padding: 10px; border: 1px solid #dee2e6; font-weight: bold; }
            td { padding: 10px; border: 1px solid #dee2e6; }
            .section { margin-bottom: 25px; }
            .highlight { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #3498db; }
            .contact-info { background: #e8f4fd; padding: 12px; border-radius: 5px; margin: 8px 0; }
            .skill-badge { background-color: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 3px; font-size: 11px; margin: 2px; display: inline-block; }
            @media print {
                body { margin: 0; }
                .no-print { display: none !important; }
            }
        </style>
    </head>
    <body>
        <div class="no-print" style="text-align: center; margin-bottom: 20px; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Save as PDF</button>
            <p style="margin: 10px 0 0 0; font-size: 14px; color: #666;">Click the button above to save as PDF</p>
        </div>
        
        <h1>PORTFOLIO - ' . htmlspecialchars($portfolio['full_name']) . '</h1>
        <div style="text-align: right; color: #666; font-size: 12px; margin-bottom: 20px;">Generated on: ' . date('F j, Y \a\t g:i A') . '</div>
        
        <div class="section">
            <h2>Personal Information</h2>
            <div class="contact-info">
                <table>
                    <tr><th width="30%">Full Name:</th><td>' . htmlspecialchars($portfolio['full_name']) . '</td></tr>
                    <tr><th>Email:</th><td>' . htmlspecialchars($portfolio['email']) . '</td></tr>
                    <tr><th>Phone:</th><td>+' . htmlspecialchars($portfolio['country_code']) . ' ' . htmlspecialchars($portfolio['phone_number']) . '</td></tr>
                    <tr><th>Current Location:</th><td>' . htmlspecialchars($portfolio['current_location']) . '</td></tr>
                    <tr><th>Visa Status:</th><td>' . htmlspecialchars($portfolio['visa_status']) . '</td></tr>
                    <tr><th>LinkedIn Profile:</th><td>' . htmlspecialchars($portfolio['linkedin_profile']) . '</td></tr>
                    <tr><th>Languages Spoken:</th><td>' . htmlspecialchars($portfolio['languages_spoken']) . '</td></tr>
                    <tr><th>Citizenship Status:</th><td>' . htmlspecialchars($portfolio['citizenship_status']) . '</td></tr>
                </table>
            </div>
        </div>

        <div class="section">
            <h2>Professional Information</h2>
            <table>
                <tr><th width="30%">Profile Type:</th><td>' . getProfileTypeText($portfolio['profile_type']) . '</td></tr>
                <tr><th>Total Experience:</th><td>' . $portfolio['total_experience'] . ' years</td></tr>
                <tr><th>Current Designation:</th><td>' . htmlspecialchars($portfolio['current_designation']) . '</td></tr>
                <tr><th>Functions Desired:</th><td>' . htmlspecialchars($portfolio['functions_desired']) . '</td></tr>
                <tr><th>Current Salary:</th><td>' . $portfolio['current_salary'] . ' ' . $portfolio['salary_currency'] . '</td></tr>
                <tr><th>Location Preference:</th><td>' . htmlspecialchars($portfolio['location_preference']) . '</td></tr>
                <tr><th>Willing to Relocate:</th><td>' . htmlspecialchars($portfolio['willing_relocate']) . '</td></tr>
                <tr><th>Availability:</th><td>' . htmlspecialchars($portfolio['availability']) . '</td></tr>
            </table>
        </div>

        <div class="section">
            <h2>Skills & Industry</h2>
            <table>
                <tr><th width="30%">Core Industry:</th><td>' . htmlspecialchars($portfolio['core_industry']) . '</td></tr>
                <tr><th>Category:</th><td>' . htmlspecialchars($portfolio['category']) . '</td></tr>
                <tr><th>Subcategory:</th><td>' . htmlspecialchars($portfolio['subcategory']) . '</td></tr>
                <tr><th>Selected Skills:</th><td>';
    
    if (!empty($selected_skills)) {
        foreach ($selected_skills as $skill) {
            $html .= '<span class="skill-badge">' . htmlspecialchars($skill) . '</span> ';
        }
    } else {
        $html .= 'No skills specified';
    }
    
    $html .= '</td></tr>
                <tr><th>Project Management Experience:</th><td>' . $portfolio['project_management_exp'] . '</td></tr>
                <tr><th>Cross Industry Experience:</th><td>' . $portfolio['cross_industry_exp'] . '</td></tr>
            </table>
        </div>';

    if (!empty($portfolio['project_management_skills'])) {
        $html .= '
        <div class="section">
            <h2>Project Management Skills</h2>
            <div class="highlight">' . nl2br(htmlspecialchars($portfolio['project_management_skills'])) . '</div>
        </div>';
    }

    $html .= '
        <div class="section">
            <h2>Technical Strengths</h2>
            <div class="highlight">' . nl2br(htmlspecialchars($portfolio['technical_strengths'])) . '</div>
        </div>

        <div class="section">
            <h2>Personal Strengths</h2>
            <div class="highlight">' . nl2br(htmlspecialchars($portfolio['personal_strengths'])) . '</div>
        </div>';

    // US Experience
    if (!empty($us_experience)) {
        $html .= '
        <div class="section">
            <h2>US Experience</h2>
            <table>
                <tr><th>Employer</th><th>Duration</th><th>Location</th></tr>';
        foreach ($us_experience as $exp) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($exp['employer'] ?? 'Not specified') . '</td>
                    <td>' . htmlspecialchars(($exp['from'] ?? '') . ' - ' . ($exp['to'] ?? '')) . '</td>
                    <td>' . htmlspecialchars(($exp['city'] ?? '') . ', ' . ($exp['state'] ?? '')) . '</td>
                </tr>';
        }
        $html .= '
            </table>
        </div>';
    }

    // India Experience
    if (!empty($india_experience)) {
        $html .= '
        <div class="section">
            <h2>India Experience</h2>
            <table>
                <tr><th>Employer</th><th>Duration</th><th>Location</th></tr>';
        foreach ($india_experience as $exp) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($exp['employer'] ?? 'Not specified') . '</td>
                    <td>' . htmlspecialchars(($exp['from'] ?? '') . ' - ' . ($exp['to'] ?? '')) . '</td>
                    <td>' . htmlspecialchars(($exp['city'] ?? '') . ', ' . ($exp['state'] ?? '')) . '</td>
                </tr>';
        }
        $html .= '
            </table>
        </div>';
    }

    // Education
    if (!empty($education)) {
        $html .= '
        <div class="section">
            <h2>Education</h2>
            <table>
                <tr><th>Degree</th><th>Specialization</th><th>University</th><th>Graduation Year</th></tr>';
        foreach ($education as $edu) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($edu['degree_type']) . '</td>
                    <td>' . htmlspecialchars($edu['specialization'] ?? 'Not specified') . '</td>
                    <td>' . htmlspecialchars($edu['university_name']) . '</td>
                    <td>' . htmlspecialchars($edu['graduation_year']) . '</td>
                </tr>';
        }
        $html .= '
            </table>
        </div>';
    }

    $html .= '
        <div class="section">
            <h2>Career Objective</h2>
            <div class="highlight">' . htmlspecialchars($portfolio['return_purpose']) . '</div>
        </div>

        <div class="section">
            <h2>Additional Information</h2>
            <div class="highlight">' . nl2br(htmlspecialchars($portfolio['additional_info'])) . '</div>
        </div>

        <div class="section">
            <p style="text-align: center; color: #666; font-style: italic; margin-top: 30px;">
                Document generated automatically by Job Portal System
            </p>
        </div>
        
        <script>
            // Auto-trigger print dialog when page loads
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        </script>
    </body>
    </html>';

    echo $html;
    exit();
}

// Helper function to get profile type text
function getProfileTypeText($type) {
    $types = [
        '1' => 'International Professional',
        '2' => 'Global Professional',
        '3' => 'International Student'
    ];
    return $types[$type] ?? 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Portfolio - <?php echo htmlspecialchars($portfolio['full_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .card {
            margin-bottom: 20px;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .skill-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            margin: 0.125rem;
            display: inline-block;
        }
        .experience-item {
            border-left: 3px solid #3498db;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .print-btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Portfolio Details - <?php echo htmlspecialchars($portfolio['full_name']); ?></h4>
                    <div class="btn-group">
                        <!-- Single print button instead of dropdown -->
                        <button type="button" class="btn btn-outline-primary" onclick="printPortfolio()">
                            <i class='bx bx-printer'></i> Print/Save as PDF
                        </button>
                        <a href="portfolio_list.php" class="btn btn-secondary"><i class='bx bx-arrow-back'></i> Back to List</a>
                    </div>
                </div>

                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class='bx bx-user me-2'></i>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($portfolio['full_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($portfolio['email']); ?></p>
                                <p><strong>Phone:</strong> +<?php echo htmlspecialchars($portfolio['country_code']); ?> <?php echo htmlspecialchars($portfolio['phone_number']); ?></p>
                                <p><strong>Current Location:</strong> <?php echo htmlspecialchars($portfolio['current_location']); ?></p>
                                <p><strong>Visa Status:</strong> <?php echo htmlspecialchars($portfolio['visa_status']); ?></p>
                                <p><strong>LinkedIn:</strong> 
                                    <?php if ($portfolio['linkedin_profile']): ?>
                                        <a href="<?php echo htmlspecialchars($portfolio['linkedin_profile']); ?>" target="_blank">View Profile</a>
                                    <?php else: ?>
                                        Not provided
                                    <?php endif; ?>
                                </p>
                                <p><strong>Languages Spoken:</strong> <?php echo htmlspecialchars($portfolio['languages_spoken']); ?></p>
                                <p><strong>Citizenship Status:</strong> <?php echo htmlspecialchars($portfolio['citizenship_status']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class='bx bx-briefcase me-2'></i>Professional Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Profile Type:</strong> <?php echo getProfileTypeText($portfolio['profile_type']); ?></p>
                                <p><strong>Total Experience:</strong> <?php echo $portfolio['total_experience']; ?> years</p>
                                <p><strong>Current Designation:</strong> <?php echo htmlspecialchars($portfolio['current_designation']); ?></p>
                                <p><strong>Functions Desired:</strong> <?php echo htmlspecialchars($portfolio['functions_desired']); ?></p>
                                <p><strong>Current Salary:</strong> <?php echo $portfolio['current_salary']; ?> <?php echo $portfolio['salary_currency']; ?></p>
                                <p><strong>Location Preference:</strong> 
                                    <?php 
                                    if (is_array($location_preference)) {
                                        echo htmlspecialchars(implode(', ', $location_preference));
                                    } else {
                                        echo htmlspecialchars($portfolio['location_preference']);
                                    }
                                    ?>
                                </p>
                                <p><strong>Willing to Relocate:</strong> <?php echo htmlspecialchars($portfolio['willing_relocate']); ?></p>
                                <p><strong>Availability:</strong> <?php echo htmlspecialchars($portfolio['availability']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Industry & Skills -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class='bx bx-category me-2'></i>Industry & Skills</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Core Industry:</strong> <?php echo htmlspecialchars($portfolio['core_industry'] ?: 'Not specified'); ?></p>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($portfolio['category'] ?: 'Not specified'); ?></p>
                                <p><strong>Subcategory:</strong> <?php echo htmlspecialchars($portfolio['subcategory'] ?: 'Not specified'); ?></p>
                                <p><strong>Project Management Experience:</strong> <?php echo $portfolio['project_management_exp'] === 'yes' ? 'Yes' : 'No'; ?></p>
                                <p><strong>Cross Industry Experience:</strong> <?php echo $portfolio['cross_industry_exp'] === 'yes' ? 'Yes' : 'No'; ?></p>
                                
                                <?php if ($portfolio['project_management_skills']): ?>
                                    <p><strong>Project Management Skills:</strong> <?php echo htmlspecialchars($portfolio['project_management_skills']); ?></p>
                                <?php endif; ?>
                                
                                <p><strong>Selected Skills:</strong></p>
                                <?php if (!empty($selected_skills)): ?>
                                    <div>
                                        <?php foreach ($selected_skills as $skill): ?>
                                            <span class="skill-badge"><?php echo htmlspecialchars($skill); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No skills specified</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class='bx bx-code-block me-2'></i>Technical Strengths</h5>
                            </div>
                            <div class="card-body">
                                <p><?php echo nl2br(htmlspecialchars($portfolio['technical_strengths'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class='bx bx-user-voice me-2'></i>Personal Strengths</h5>
                            </div>
                            <div class="card-body">
                                <p><?php echo nl2br(htmlspecialchars($portfolio['personal_strengths'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Experience -->
                <div class="row">
                    <?php if (!empty($us_experience)): ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class='bx bx-buildings me-2'></i>US Experience</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($us_experience as $exp): ?>
                                    <div class="experience-item">
                                        <p><strong>Employer:</strong> <?php echo htmlspecialchars($exp['employer'] ?: 'Not specified'); ?></p>
                                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($exp['from'] ?: ''); ?> - <?php echo htmlspecialchars($exp['to'] ?: ''); ?></p>
                                        <p><strong>Location:</strong> <?php echo htmlspecialchars($exp['city'] ?: ''); ?>, <?php echo htmlspecialchars($exp['state'] ?: ''); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($india_experience)): ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class='bx bx-buildings me-2'></i>India Experience</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($india_experience as $exp): ?>
                                    <div class="experience-item">
                                        <p><strong>Employer:</strong> <?php echo htmlspecialchars($exp['employer'] ?: 'Not specified'); ?></p>
                                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($exp['from'] ?: ''); ?> - <?php echo htmlspecialchars($exp['to'] ?: ''); ?></p>
                                        <p><strong>Location:</strong> <?php echo htmlspecialchars($exp['city'] ?: ''); ?>, <?php echo htmlspecialchars($exp['state'] ?: ''); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Additional Information -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class='bx bx-target-lock me-2'></i>Career Objective & Additional Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Return Purpose:</strong> <?php echo htmlspecialchars($portfolio['return_purpose']); ?></p>
                                <p><strong>Additional Info:</strong> <?php echo nl2br(htmlspecialchars($portfolio['additional_info'] ?: 'No additional information provided')); ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php 
                                        switch($portfolio['status']) {
                                            case 'pending': echo 'warning'; break;
                                            case 'submitted': echo 'primary'; break;
                                            case 'approved': echo 'success'; break;
                                            case 'rejected': echo 'danger'; break;
                                            case 'under_review': echo 'info'; break;
                                            default: echo 'secondary';
                                        }
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $portfolio['status'])); ?>
                                    </span>
                                </p>
                                <p><strong>Submitted At:</strong> <?php echo date('M j, Y h:i A', strtotime($portfolio['submitted_at'])); ?></p>
                                <?php if ($portfolio['updated_at'] != $portfolio['submitted_at']): ?>
                                    <p><strong>Last Updated:</strong> <?php echo date('M j, Y h:i A', strtotime($portfolio['updated_at'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Education -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class='bx bx-graduation me-2'></i>Education</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($education)): ?>
                            <?php foreach ($education as $edu): ?>
                                <div class="border-bottom pb-2 mb-2">
                                    <p><strong>Degree:</strong> <?php echo htmlspecialchars($edu['degree_type']); ?></p>
                                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($edu['specialization'] ?: 'Not specified'); ?></p>
                                    <p><strong>University:</strong> <?php echo htmlspecialchars($edu['university_name']); ?></p>
                                    <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($edu['graduation_year']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No education information provided</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Certificates -->
                <?php if (!empty($certificates)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class='bx bx-certification me-2'></i>Certificates</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($certificates as $cert): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <p><strong>Certificate Name:</strong> <?php echo htmlspecialchars($cert['certificate_name']); ?></p>
                                <p><strong>File:</strong> 
                                    <a href="<?php echo htmlspecialchars($cert['certificate_path']); ?>" target="_blank">View Certificate</a>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- File Attachments -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class='bx bx-file me-2'></i>File Attachments</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($portfolio['resume_path']): ?>
                            <p><strong>Resume:</strong> 
                                <a href="<?php echo htmlspecialchars($portfolio['resume_path']); ?>" target="_blank">View Resume</a>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($portfolio['portfolio_path']): ?>
                            <p><strong>Portfolio:</strong> 
                                <a href="<?php echo htmlspecialchars($portfolio['portfolio_path']); ?>" target="_blank">View Portfolio</a>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!$portfolio['resume_path'] && !$portfolio['portfolio_path']): ?>
                            <p class="text-muted">No files uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <div class="btn-group">
                            <a href="portfolio_list.php?id=<?php echo $portfolio['id']; ?>&update_status=approved" class="btn btn-success">Approve Portfolio</a>
                            <a href="portfolio_list.php?id=<?php echo $portfolio['id']; ?>&update_status=rejected" class="btn btn-danger">Reject Portfolio</a>
                            <a href="portfolio_list.php?id=<?php echo $portfolio['id']; ?>&update_status=under_review" class="btn btn-info">Mark Under Review</a>
                            <button type="button" class="btn btn-primary print-btn" onclick="printPortfolio()">
                                <i class='bx bx-printer'></i> Print/Save as PDF
                            </button>
                            <a href="portfolio_list.php" class="btn btn-secondary">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function printPortfolio() {
        // Open portfolio in new window for printing
        const printWindow = window.open('view_portfolio.php?id=<?php echo $portfolio['id']; ?>&download=pdf&format=pdf', '_blank');
        
        // Focus on the new window
        if (printWindow) {
            printWindow.focus();
        }
    }
    </script>
</body>
</html>