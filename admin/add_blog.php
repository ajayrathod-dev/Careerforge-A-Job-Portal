<?php
include("connection.php");
include("admin_functions.php");

// Check if user is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $excerpt = trim($_POST['excerpt']);
    $author = trim($_POST['author']);
    $status = $_POST['status'];
    $meta_title = trim($_POST['meta_title']);
    $meta_description = trim($_POST['meta_description']);
    
    // Generate slug from title
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . time();
    
    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = 'uploads/blogs/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }
    
    if (empty($error)) {
        try {
            // Use mysqli instead of PDO
            $stmt = mysqli_prepare($link, "INSERT INTO blogs (title, content, excerpt, image_path, author, status, slug, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssssss", $title, $content, $excerpt, $image_path, $author, $status, $slug, $meta_title, $meta_description);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Blog added successfully!";
                    // Clear form
                    $title = $content = $excerpt = $author = $meta_title = $meta_description = '';
                } else {
                    $error = "Error adding blog: " . mysqli_error($link);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Database preparation error: " . mysqli_error($link);
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Add New Blog</h4>
                <a href="blog_list.php" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to Blogs
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Blog Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Content *</label>
                                    <textarea class="form-control" id="content" name="content" rows="12" required><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Excerpt</label>
                                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo isset($excerpt) ? htmlspecialchars($excerpt) : ''; ?></textarea>
                                    <div class="form-text">Brief summary of the blog (optional)</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Featured Image</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Upload Image</label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                            <div class="form-text">Recommended: 1200x630 pixels</div>
                                        </div>
                                        <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                                            <img id="previewImage" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Blog Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="author" class="form-label">Author *</label>
                                            <input type="text" class="form-control" id="author" name="author" value="<?php echo isset($author) ? htmlspecialchars($author) : ''; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status *</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="draft">Draft</option>
                                                <option value="published" selected>Published</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">SEO Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?php echo isset($meta_title) ? htmlspecialchars($meta_title) : ''; ?>">
                                            <div class="form-text">SEO meta title (optional)</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?php echo isset($meta_description) ? htmlspecialchars($meta_description) : ''; ?></textarea>
                                            <div class="form-text">SEO meta description (optional)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class='bx bx-plus-circle'></i> Add Blog
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-generate excerpt from content
    document.getElementById('content').addEventListener('input', function() {
        const content = this.value;
        const excerptField = document.getElementById('excerpt');
        
        if (!excerptField.value) {
            // Generate excerpt from first 150 characters
            const excerpt = content.substring(0, 150).trim();
            if (excerpt.length > 0) {
                excerptField.value = excerpt + (content.length > 150 ? '...' : '');
            }
        }
    });

    // Auto-generate meta title from blog title
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const metaTitleField = document.getElementById('meta_title');
        
        if (!metaTitleField.value && title) {
            metaTitleField.value = title;
        }
    });

    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });

    // Character counters
    function setupCharacterCounter(textareaId, counterId, maxLength) {
        const textarea = document.getElementById(textareaId);
        const counter = document.getElementById(counterId);
        
        if (textarea && counter) {
            textarea.addEventListener('input', function() {
                const currentLength = this.value.length;
                counter.textContent = `${currentLength}/${maxLength} characters`;
                
                if (currentLength > maxLength) {
                    counter.classList.add('text-danger');
                } else {
                    counter.classList.remove('text-danger');
                }
            });
            
            // Trigger initial count
            textarea.dispatchEvent(new Event('input'));
        }
    }

    // Initialize character counters
    document.addEventListener('DOMContentLoaded', function() {
        setupCharacterCounter('excerpt', 'excerptCounter', 300);
        setupCharacterCounter('meta_description', 'metaDescCounter', 160);
        
        const metaTitle = document.getElementById('meta_title');
        const metaTitleCounter = document.getElementById('metaTitleCounter');
        
        if (metaTitle && metaTitleCounter) {
            metaTitle.addEventListener('input', function() {
                const currentLength = this.value.length;
                metaTitleCounter.textContent = `${currentLength}/60 characters`;
                
                if (currentLength > 60) {
                    metaTitleCounter.classList.add('text-danger');
                } else {
                    metaTitleCounter.classList.remove('text-danger');
                }
            });
            
            // Trigger initial count
            metaTitle.dispatchEvent(new Event('input'));
        }
    });
</script>

<?php include 'footer.php'; ?>