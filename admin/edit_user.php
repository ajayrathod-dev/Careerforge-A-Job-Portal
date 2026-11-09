<?php
include("connection.php");
include("admin_functions.php");

// Check if user is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get user ID from URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user data
$user = null;
if ($user_id > 0) {
    $result = mysqli_query($link, "SELECT * FROM job_seekers WHERE id = '$user_id'");
    $user = mysqli_fetch_assoc($result);
}

// If user not found, redirect back
if (!$user) {
    header('Location: user_list.php?error=User not found');
    exit();
}

$success = "";
$error = "";

// Handle form submission
if ($_POST) {
    $fullname = mysqli_real_escape_string($link, $_POST['fullname']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $username = mysqli_real_escape_string($link, $_POST['username']);
    
    // Check if email already exists (excluding current user)
    $email_check = mysqli_query($link, "SELECT id FROM job_seekers WHERE email = '$email' AND id != '$user_id'");
    if (mysqli_num_rows($email_check) > 0) {
        $error = "Email already exists!";
    } else {
        // Check if username already exists (excluding current user)
        $username_check = mysqli_query($link, "SELECT id FROM job_seekers WHERE username = '$username' AND id != '$user_id'");
        if (mysqli_num_rows($username_check) > 0) {
            $error = "Username already exists!";
        } else {
            // Update user
            $update_sql = "UPDATE job_seekers SET fullname = '$fullname', email = '$email', username = '$username' WHERE id = '$user_id'";
            if (mysqli_query($link, $update_sql)) {
                $success = "User updated successfully!";
                // Refresh user data
                $result = mysqli_query($link, "SELECT * FROM job_seekers WHERE id = '$user_id'");
                $user = mysqli_fetch_assoc($result);
            } else {
                $error = "Error updating user: " . mysqli_error($link);
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Edit User</h4>
                <a href="user_list.php" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to Users
                </a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Registration Date</label>
                            <input type="text" class="form-control" value="<?php echo date('F j, Y \a\t h:i A', strtotime($user['created_at'])); ?>" readonly>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Update User
                            </button>
                            <a href="user_list.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-4">
                    <div class="card border">
                        <div class="card-body text-center">
                            <div class="avatar avatar-xl bg-primary text-white rounded-circle mx-auto mb-3">
                                <i class='bx bx-user'></i>
                            </div>
                            <h5><?php echo htmlspecialchars($user['fullname']); ?></h5>
                            <p class="text-muted">User ID: <?php echo $user['id']; ?></p>
                            <div class="d-grid gap-2">
                                <a href="user_list.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class='bx bx-trash'></i> Delete User
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>