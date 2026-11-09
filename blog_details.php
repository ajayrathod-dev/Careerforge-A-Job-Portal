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

// Get blog ID from URL
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Fetch specific blog from database
    $sql = "SELECT * FROM blogs WHERE id = ? AND status = 'published'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$blog_id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    // If blog not found, redirect to blog list
    if (!$blog) {
        header("Location: blog.php");
        exit();
    }

    // Fetch recent posts for sidebar
    $recent_sql = "SELECT * FROM blogs WHERE status = 'published' AND id != ? ORDER BY created_at DESC LIMIT 3";
    $recent_stmt = $pdo->prepare($recent_sql);
    $recent_stmt->execute([$blog_id]);
    $recent_blogs = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching blog: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo htmlspecialchars($blog['meta_description'] ?? $blog['title']); ?>">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">

    <title><?php echo htmlspecialchars($blog['meta_title'] ?? $blog['title']); ?> | ORDER TECH</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    
    <style>
        .blog-content {
            line-height: 1.8;
            font-size: 16px;
            color: #333;
        }
        .blog-content p {
            margin-bottom: 1.5rem;
        }
        .blog-meta {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #a4c639;
        }
        .share-buttons .social-icons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .share-buttons .social-share {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #a4c639;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .share-buttons .social-share:hover {
            background: #8fb12d;
        }
        .comment-section {
            margin-top: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .comment-form {
            margin-top: 30px;
        }
        .comment-form .form-control {
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .recent-posts li {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .recent-posts li:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <!-- Header Include -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <div class="page-heading header-text">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <h1><?php echo htmlspecialchars($blog['title']); ?></h1>
            <span>Published on <?php echo date('d.m.Y', strtotime($blog['created_at'])); ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="single-services">
      <div class="container">
        <div class="row">
          <div class="col-md-8">
            <section class='tabs-content'>
              <article>
                <?php if (!empty($blog['image_path'])): ?>
                  <img src="<?php echo htmlspecialchars($blog['image_path']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="img-fluid mb-4">
                <?php else: ?>
                  <img src="assets/images/blog-image-1-940x460.jpg" alt="Default Blog Image" class="img-fluid mb-4">
                <?php endif; ?>
                
                <div class="blog-meta">
                  <span>
                    <i class="fa fa-user"></i> <?php echo htmlspecialchars($blog['author']); ?> 
                    &nbsp;|&nbsp; 
                    <i class="fa fa-calendar"></i> <?php echo date('d.m.Y H:i', strtotime($blog['created_at'])); ?> 
                    &nbsp;|&nbsp; 
                    <i class="fa fa-comments"></i> 0 comments
                  </span>
                </div>
                
                <div class="blog-content">
                  <?php echo $blog['content']; ?>
                </div>
                
                <div class="share-buttons">
                  <h6>Share this post:</h6>
                  <ul class="social-icons">
                    <li><a href="#" class="social-share"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#" class="social-share"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#" class="social-share"><i class="fa fa-linkedin"></i></a></li>
                  </ul>
                </div>
                
                <div class="comment-section" id="comments">
                  <h4>Leave a Comment</h4>
                  <form class="comment-form" action="#" method="post">
                    <div class="row">
                      <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Your Name" required>
                      </div>
                      <div class="col-md-6">
                        <input type="email" class="form-control" placeholder="Your Email" required>
                      </div>
                      <div class="col-md-12">
                        <textarea class="form-control" rows="5" placeholder="Your Comment" required></textarea>
                      </div>
                      <div class="col-md-12">
                        <button type="submit" class="filled-button">Submit Comment</button>
                      </div>
                    </div>
                  </form>
                </div>
              </article>
            </section>
          </div>

          <div class="col-md-4">
            <!-- Search Widget -->
            <div class="sidebar-widget mb-5">
              <h4 class="h4">Search Blogs</h4>
              <form id="search_form" name="gs" method="GET" action="#">
                <div class="input-group">
                  <input type="text" name="q" class="form-control" placeholder="Type to search..." autocomplete="on">
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">
                      <i class="fa fa-search"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <!-- Recent Posts Widget -->
            <div class="sidebar-widget">
              <h4 class="h4">Recent Posts</h4>
              <ul class="recent-posts">
                <?php if (count($recent_blogs) > 0): ?>
                  <?php foreach ($recent_blogs as $recent_blog): ?>
                    <li>
                      <h6 style="margin-bottom:8px;">
                        <a href="blog_details.php?id=<?php echo $recent_blog['id']; ?>">
                          <?php echo htmlspecialchars($recent_blog['title']); ?>
                        </a>
                      </h6>
                      <small class="text-muted">
                        <i class="fa fa-user"></i> <?php echo htmlspecialchars($recent_blog['author']); ?> 
                        &nbsp;|&nbsp; 
                        <i class="fa fa-calendar"></i> <?php echo date('d.m.Y', strtotime($recent_blog['created_at'])); ?>
                      </small>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li class="text-muted">No recent posts available.</li>
                <?php endif; ?>
              </ul>
            </div>

            <!-- Categories Widget -->
            <div class="sidebar-widget mt-5">
              <h4 class="h4">Blog Categories</h4>
              <ul class="list-unstyled">
                <li><a href="#" class="text-decoration-none">Career Tips</a></li>
                <li><a href="#" class="text-decoration-none">Job Search</a></li>
                <li><a href="#" class="text-decoration-none">Interview Advice</a></li>
                <li><a href="#" class="text-decoration-none">Industry News</a></li>
                <li><a href="#" class="text-decoration-none">Success Stories</a></li>
              </ul>
            </div>

            <!-- Newsletter Widget -->
            <div class="sidebar-widget mt-5">
              <h4 class="h4">Stay Updated</h4>
              <p>Subscribe to get the latest blog posts and career tips.</p>
              <form action="#" method="post">
                <div class="form-group">
                  <input type="email" class="form-control" placeholder="Your Email" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Subscribe</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Include -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/accordions.js"></script>

    <script language="text/Javascript"> 
      cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
      function clearField(t){                   //declaring the array outside of the
      if(! cleared[t.id]){                      // function makes it static and global
          cleared[t.id] = 1;  // you could use true and false, but that's more typing
          t.value='';         // with more chance of typos
          t.style.color='#fff';
          }
      }
    </script>
</body>
</html>