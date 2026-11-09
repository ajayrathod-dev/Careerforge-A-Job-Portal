<?php
include("connection.php");
include("admin_functions.php");

// Check if user is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get all job seekers
$result = mysqli_query($link, "SELECT * FROM job_seekers ORDER BY created_at DESC");
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Check if user exists
    $check_user = mysqli_query($link, "SELECT * FROM job_seekers WHERE id = '$delete_id'");
    if (mysqli_num_rows($check_user) > 0) {
        $delete_sql = "DELETE FROM job_seekers WHERE id = '$delete_id'";
        if (mysqli_query($link, $delete_sql)) {
            header('Location: user_list.php?success=User deleted successfully');
            exit();
        } else {
            header('Location: user_list.php?error=Error deleting user');
            exit();
        }
    } else {
        header('Location: user_list.php?error=User not found');
        exit();
    }
}
?>

<?php include 'header.php'; ?>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>User Management</h4>
                <div>
                    <span class="badge bg-primary">Total Users: <?php echo count($users); ?></span>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class='bx bx-user-x text-muted' style="font-size: 3rem;"></i>
                                    <p class="text-muted mb-0">No users found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-light-primary rounded me-2">
                                            <i class='bx bx-user'></i>
                                        </div>
                                        <div>
                                            <?php echo htmlspecialchars($user['fullname']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($user['username']); ?></span>
                                </td>
                                <td>
                                    <small><?php echo date('M j, Y', strtotime($user['created_at'])); ?></small>
                                    <br>
                                    <small class="text-muted"><?php echo date('h:i A', strtotime($user['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewUserModal<?php echo $user['id']; ?>">
                                            <i class='bx bx-show'></i> View
                                        </button>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class='bx bx-edit'></i> Edit
                                        </a>
                                        <a href="user_list.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete user: <?php echo htmlspecialchars($user['fullname']); ?>?')">
                                            <i class='bx bx-trash'></i> Delete
                                        </a>
                                    </div>

                                    <!-- View User Modal -->
                                    <div class="modal fade" id="viewUserModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">User Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-4 text-center mb-3">
                                                            <div class="avatar avatar-xl bg-primary text-white rounded-circle mx-auto mb-2">
                                                                <i class='bx bx-user'></i>
                                                            </div>
                                                            <h6><?php echo htmlspecialchars($user['fullname']); ?></h6>
                                                            <small class="text-muted">User ID: <?php echo $user['id']; ?></small>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <td><strong>Email:</strong></td>
                                                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Username:</strong></td>
                                                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Registered:</strong></td>
                                                                    <td><?php echo date('F j, Y \a\t h:i A', strtotime($user['created_at'])); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Status:</strong></td>
                                                                    <td><span class="badge bg-success">Active</span></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit User</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>