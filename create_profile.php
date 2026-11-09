<?php
session_start();
// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current page for active class
$current_page = basename($_SERVER['PHP_SELF']);
$about_pages = ['about.php', 'team.php', 'blog.php', 'categories.php', 'testimonials.php', 'terms.php'];
$profile_pages = ['profile.php', 'resume.php'];

// Database connection
require_once 'connection.php';

// Handle form submission
$submission_success = false;
$submission_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $full_name = trim($first_name . ' ' . $last_name);

        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? '';
        $phone_country_code = $_POST['phone_country_code'] ?? '';
        $phone = $_POST['phone'] ?? '';
        if ($phone_country_code === 'other') {
            $phone_country_code = $_POST['phone_country_code_other'] ?? '';
        }
        $phone_combined = trim(($phone_country_code ? $phone_country_code . ' ' : '') . $phone);

        $bio = $_POST['bio'] ?? '';
        $profile_picture = '';
        
        if (empty($first_name) || empty($last_name) || empty($email) || empty($username)) {
            throw new Exception("First name, last name, email, and username are required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address.");
        }

        // Handle profile picture upload
        if (!empty($_FILES['profile_picture']['name'])) {
            $allowed_types = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_types)) {
                throw new Exception("Invalid profile picture format. Allowed: JPG, PNG.");
            }
            if ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) { // 5MB
                throw new Exception("Profile picture exceeds 5MB limit.");
            }

            $profile_picture = 'uploads/profiles/' . uniqid('profile_') . '.' . $ext;
            if (!is_dir('uploads/profiles')) {
                mkdir('uploads/profiles', 0755, true);
            }
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture)) {
                throw new Exception("Failed to upload profile picture.");
            }
        }

        // Update basic profile (map to existing columns)
        // Preserve existing profile_picture when no new file uploaded
        $sql = "UPDATE job_seekers SET 
                fullname = ?, email = ?, username = ?, 
                phone = ?, bio = ?, profile_picture = COALESCE(NULLIF(?, ''), profile_picture)
                WHERE id = ?";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $full_name, $email, $username,
            $phone_combined, $bio, $profile_picture,
            $_SESSION['user_id']
        ]);

        if ($stmt->rowCount() > 0) {
            $submission_success = true;
            header("Location: index-2.php");
            exit();
        } else {
            throw new Exception("No profile found to update. Please ensure you are logged in correctly.");
        }
    } catch (Exception $e) {
        $submission_error = $e->getMessage();
    }
}

// Fetch existing profile data
try {
    $sql = "SELECT * FROM job_seekers WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $submission_error = "Error fetching profile: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Create or update your professional profile on Order Tech Careers.">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>ORDER TECH | CREATE PROFILE</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
</head>

<body>
    <!-- Header Include -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <div class="page-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Create Your Profile</h1>
                    <p>Build your basic professional presence</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Profile Information</h4>
                        
                        <?php if ($submission_success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i> Profile updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($submission_error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i> Error: <?php echo htmlspecialchars($submission_error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($profile['username'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="phone_country_code" class="form-label">Country Code</label>
                                    <select class="form-select" id="phone_country_code" name="phone_country_code">
                                        <option value="">Select Code</option>
                                        <option value="+1" <?php if (($profile['phone_country_code'] ?? '') == '+1') echo 'selected'; ?>>+1 (USA/Canada)</option>
                                        <option value="+91" <?php if (($profile['phone_country_code'] ?? '') == '+91') echo 'selected'; ?>>+91 (India)</option>
                                        <option value="+44" <?php if (($profile['phone_country_code'] ?? '') == '+44') echo 'selected'; ?>>+44 (UK)</option>
                                        <option value="+61" <?php if (($profile['phone_country_code'] ?? '') == '+61') echo 'selected'; ?>>+61 (Australia)</option>
                                        <option value="+49" <?php if (($profile['phone_country_code'] ?? '') == '+49') echo 'selected'; ?>>+49 (Germany)</option>
                                        <option value="other" <?php if (($profile['phone_country_code'] ?? '') && !in_array($profile['phone_country_code'], ['+1', '+91', '+44', '+61', '+49'])) echo 'selected'; ?>>Other</option>
                                    </select>
                                    <div id="phoneCountryCodeOtherContainer" class="other-input-container" style="<?php if (($profile['phone_country_code'] ?? '') && !in_array($profile['phone_country_code'], ['+1', '+91', '+44', '+61', '+49'])) echo 'display: block;'; ?>">
                                        <label for="phone_country_code_other" class="other-input-label">Specify Country Code</label>
                                        <input type="text" class="form-control" id="phone_country_code_other" name="phone_country_code_other" value="<?php if (($profile['phone_country_code'] ?? '') && !in_array($profile['phone_country_code'], ['+1', '+91', '+44', '+61', '+49'])) echo htmlspecialchars($profile['phone_country_code']); ?>" placeholder="e.g., +33">
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept=".jpg,.jpeg,.png">
                                <div class="form-text">Accepted formats: JPG, PNG (Max 5MB)</div>
                                <?php if (!empty($profile['profile_picture'])): ?>
                                    <div class="mt-2">
                                        <img src="<?php echo htmlspecialchars($profile['profile_picture']); ?>" alt="Current Profile Picture" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i> Save Profile
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <p class="text-muted">
                                For detailed professional experience and portfolio submission, 
                                <a href="add_portfolio.php" class="text-decoration-none">click here to complete your portfolio</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Include -->
    <?php include 'footer.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle other country code input
        document.getElementById('phone_country_code').addEventListener('change', function() {
            const container = document.getElementById('phoneCountryCodeOtherContainer');
            if (this.value === 'other') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                document.getElementById('phone_country_code_other').value = '';
            }
        });
    </script>
</body>
</html>