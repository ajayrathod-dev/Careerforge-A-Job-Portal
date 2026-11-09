<?php
include("connection.php");
include("admin_functions.php");

// Check if user is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Handle blog deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    try {
        // Get image path before deleting
        $stmt = mysqli_prepare($link, "SELECT image_path FROM blogs WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $blog = mysqli_fetch_assoc($result);
        
        // Delete from database
        $stmt = mysqli_prepare($link, "DELETE FROM blogs WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Delete image file if exists
            if ($blog['image_path'] && file_exists($blog['image_path'])) {
                unlink($blog['image_path']);
            }
            $_SESSION['success'] = "Blog deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting blog: " . mysqli_error($link);
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: blog_list.php");
    exit();
}

// Handle status update
if (isset($_GET['toggle_status'])) {
    $blog_id = $_GET['toggle_status'];
    
    try {
        $stmt = mysqli_prepare($link, "UPDATE blogs SET status = IF(status='published', 'draft', 'published') WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $blog_id);
        mysqli_stmt_execute($stmt);
        
        $_SESSION['success'] = "Blog status updated successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating status: " . $e->getMessage();
    }
    
    header("Location: blog_list.php");
    exit();
}

// Fetch all blogs
$blogs = [];
try {
    $result = mysqli_query($link, "SELECT * FROM blogs ORDER BY created_at DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $blogs[] = $row;
    }
} catch (Exception $e) {
    $error = "Error fetching blogs: " . $e->getMessage();
}
?>

<?php include 'header.php'; ?>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Blog Management</h4>
                <a href="add_blog.php" class="btn btn-primary">
                    <i class='bx bx-plus'></i> Add New Blog
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($blogs)): ?>
                        <div class="text-center py-5">
                            <i class='bx bx-news text-muted' style="font-size: 3rem;"></i>
                            <h5 class="mt-3">No blogs found</h5>
                            <p class="text-muted">Get started by creating your first blog post.</p>
                            <a href="add_blog.php" class="btn btn-primary">Add Your First Blog</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($blogs as $blog): ?>
                                        <tr>
                                            <td>
                                                <?php if ($blog['image_path']): ?>
                                                    <img src="<?php echo htmlspecialchars($blog['image_path']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="blog-image" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                <?php else: ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 80px; height: 60px;">
                                                        <i class='bx bx-image text-muted'></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($blog['title']); ?></strong>
                                                <?php if ($blog['excerpt']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($blog['excerpt'], 0, 100)) . (strlen($blog['excerpt']) > 100 ? '...' : ''); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($blog['author']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $blog['status'] == 'published' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($blog['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo date('M j, Y', strtotime($blog['created_at'])); ?></small>
                                                <br>
                                                <small class="text-muted"><?php echo date('h:i A', strtotime($blog['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <small><?php echo date('M j, Y', strtotime($blog['updated_at'])); ?></small>
                                                <br>
                                                <small class="text-muted"><?php echo date('h:i A', strtotime($blog['updated_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                                                        <i class='bx bx-edit'></i>
                                                    </a>
                                                    <a href="?toggle_status=<?php echo $blog['id']; ?>" class="btn btn-outline-<?php echo $blog['status'] == 'published' ? 'warning' : 'success'; ?> btn-sm" title="<?php echo $blog['status'] == 'published' ? 'Unpublish' : 'Publish'; ?>">
                                                        <i class='bx bx-<?php echo $blog['status'] == 'published' ? 'hide' : 'show'; ?>'></i>
                                                    </a>
                                                    <a href="../blog.php?slug=<?php echo $blog['slug']; ?>" target="_blank" class="btn btn-outline-info btn-sm" title="View">
                                                        <i class='bx bx-link-external'></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" title="Delete" onclick="confirmDelete(<?php echo $blog['id']; ?>, '<?php echo htmlspecialchars(addslashes($blog['title'])); ?>')">
                                                        <i class='bx bx-trash'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, title) {
        if (confirm(`Are you sure you want to delete the blog "${title}"? This action cannot be undone.`)) {
            window.location.href = `?delete_id=${id}`;
        }
    }
</script>

<?php include 'footer.php'; ?>