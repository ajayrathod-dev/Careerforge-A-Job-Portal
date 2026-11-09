<?php
include("connection.php");
include("admin_functions.php");

// Check if user is logged in, if not redirect to login
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get counts for dashboard
$user_count = mysqli_query($link, "SELECT COUNT(*) as count FROM job_seekers")->fetch_assoc()['count'];
$portfolio_count = mysqli_query($link, "SELECT COUNT(*) as count FROM portfolios")->fetch_assoc()['count'];
$pending_portfolios = mysqli_query($link, "SELECT COUNT(*) as count FROM portfolios WHERE status = 'pending'")->fetch_assoc()['count'];
$approved_portfolios = mysqli_query($link, "SELECT COUNT(*) as count FROM portfolios WHERE status = 'approved'")->fetch_assoc()['count'];

// Check if blogs table exists
$blog_count = 0;
$published_blogs = 0;
$draft_blogs = 0;
$check_blog_table = mysqli_query($link, "SHOW TABLES LIKE 'blogs'");
if (mysqli_num_rows($check_blog_table) > 0) {
    $blog_count = mysqli_query($link, "SELECT COUNT(*) as count FROM blogs")->fetch_assoc()['count'];
    $published_blogs = mysqli_query($link, "SELECT COUNT(*) as count FROM blogs WHERE status = 'published'")->fetch_assoc()['count'];
    $draft_blogs = mysqli_query($link, "SELECT COUNT(*) as count FROM blogs WHERE status = 'draft'")->fetch_assoc()['count'];
}

// Get recent activities
$recent_users = mysqli_query($link, "SELECT * FROM job_seekers ORDER BY created_at DESC LIMIT 5");
$recent_portfolios = mysqli_query($link, "SELECT * FROM portfolios ORDER BY submitted_at DESC LIMIT 5");
$recent_blogs = [];
if (mysqli_num_rows($check_blog_table) > 0) {
    $recent_blogs_result = mysqli_query($link, "SELECT * FROM blogs ORDER BY created_at DESC LIMIT 5");
    while ($row = mysqli_fetch_assoc($recent_blogs_result)) {
        $recent_blogs[] = $row;
    }
}

// Get admin details for welcome message
$admin = getAdminDetails($link);
$admin_name = $admin ? $admin['user_id'] : 'Admin';
?>

<?php include 'header.php'; ?>

<div class="page-content">
    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-deepblue">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <h4 class="mb-1 text-white">Welcome back, <?php echo $admin_name; ?>! 👋</h4>
                            <p class="mb-0 text-white">Here's what's happening with your job portal today.</p>
                        </div>
                        <div class="ms-auto">
                            <i class='bx bx-stats text-white' style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total Users</p>
                            <h4 class="my-1"><?php echo $user_count; ?></h4>
                            <p class="mb-0 font-13 text-success"><i class='bx bxs-up-arrow-alt'></i> Job Seekers Registered</p>
                        </div>
                        <div class="widgets-icons bg-light-success text-success ms-auto">
                            <i class='bx bx-user'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Portfolios</p>
                            <h4 class="my-1"><?php echo $portfolio_count; ?></h4>
                            <p class="mb-0 font-13">
                                <span class="text-success"><?php echo $approved_portfolios; ?> Approved</span>, 
                                <span class="text-warning"><?php echo $pending_portfolios; ?> Pending</span>
                            </p>
                        </div>
                        <div class="widgets-icons bg-light-warning text-warning ms-auto">
                            <i class='bx bx-briefcase'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Blog Posts</p>
                            <h4 class="my-1"><?php echo $blog_count; ?></h4>
                            <p class="mb-0 font-13">
                                <span class="text-success"><?php echo $published_blogs; ?> Published</span>, 
                                <span class="text-info"><?php echo $draft_blogs; ?> Draft</span>
                            </p>
                        </div>
                        <div class="widgets-icons bg-light-info text-info ms-auto">
                            <i class='bx bx-news'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Pending Review</p>
                            <h4 class="my-1"><?php echo $pending_portfolios; ?></h4>
                            <p class="mb-0 font-13 text-warning"><i class='bx bxs-time'></i> Need Attention</p>
                        </div>
                        <div class="widgets-icons bg-light-danger text-danger ms-auto">
                            <i class='bx bx-time'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-primary text-white rounded-circle mb-3">
                                        <i class='bx bx-news bx-sm'></i>
                                    </div>
                                    <h6 class="mb-2">Blog Management</h6>
                                    <div class="btn-group-vertical w-100">
                                        <a href="add_blog.php" class="btn btn-primary btn-sm mb-2">Add New Blog</a>
                                        <a href="blog_list.php" class="btn btn-outline-primary btn-sm">Manage Blogs</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-success text-white rounded-circle mb-3">
                                        <i class='bx bx-user bx-sm'></i>
                                    </div>
                                    <h6 class="mb-2">User Management</h6>
                                    <div class="btn-group-vertical w-100">
                                        <a href="user_list.php" class="btn btn-success btn-sm">View All Users</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-warning text-white rounded-circle mb-3">
                                        <i class='bx bx-briefcase bx-sm'></i>
                                    </div>
                                    <h6 class="mb-2">Portfolios</h6>
                                    <div class="btn-group-vertical w-100">
                                        <a href="portfolio_list.php" class="btn btn-warning btn-sm">Review Portfolios</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">System Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <p class="mb-0">Database Connection</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-success">Connected</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <p class="mb-0">Admin Authentication</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <p class="mb-0">Blog System</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-<?php echo mysqli_num_rows($check_blog_table) > 0 ? 'success' : 'warning'; ?>">
                                <?php echo mysqli_num_rows($check_blog_table) > 0 ? 'Ready' : 'Setup Required'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-0">Last Login</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="text-muted">Just now</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <!-- Recent Users -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">Recent Users</h5>
                        <a href="user_list.php" class="ms-auto">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($recent_users) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($user = mysqli_fetch_assoc($recent_users)): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light rounded">
                                        <i class='bx bx-user'></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($user['fullname']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <small class="text-muted"><?php echo date('M j', strtotime($user['created_at'])); ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class='bx bx-user-x text-muted' style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">No users registered yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Portfolios -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">Recent Portfolios</h5>
                        <a href="portfolio_list.php" class="ms-auto">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($recent_portfolios) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($portfolio = mysqli_fetch_assoc($recent_portfolios)): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light-<?php 
                                        switch($portfolio['status']) {
                                            case 'approved': echo 'success'; break;
                                            case 'pending': echo 'warning'; break;
                                            case 'rejected': echo 'danger'; break;
                                            default: echo 'info';
                                        }
                                    ?> rounded">
                                        <i class='bx bx-briefcase'></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($portfolio['full_name']); ?></h6>
                                        <small class="text-muted">
                                            <?php 
                                            switch($portfolio['profile_type']) {
                                                case '1': echo 'US to India'; break;
                                                case '2': echo 'US exp in India'; break;
                                                case '3': echo 'US student to India'; break;
                                                default: echo 'Unknown';
                                            }
                                            ?>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?php 
                                            switch($portfolio['status']) {
                                                case 'approved': echo 'success'; break;
                                                case 'pending': echo 'warning'; break;
                                                case 'rejected': echo 'danger'; break;
                                                default: echo 'info';
                                            }
                                        ?>">
                                            <?php echo ucfirst($portfolio['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class='bx bx-briefcase-alt text-muted' style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">No portfolio submissions yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Blogs -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">Recent Blogs</h5>
                        <a href="blog_list.php" class="ms-auto">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_blogs)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_blogs as $blog): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light-<?php echo $blog['status'] == 'published' ? 'success' : 'info'; ?> rounded">
                                        <i class='bx bx-news'></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($blog['title']); ?></h6>
                                        <small class="text-muted">By <?php echo htmlspecialchars($blog['author']); ?></small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?php echo $blog['status'] == 'published' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($blog['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class='bx bx-news text-muted' style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">No blog posts yet</p>
                            <a href="add_blog.php" class="btn btn-primary btn-sm mt-2">Create First Blog</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>