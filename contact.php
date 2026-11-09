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

// Handle form submission
$submission_success = false;
$submission_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'connection.php';
    
    try {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';
        
        if (empty($name) || empty($email) || empty($message)) {
            throw new Exception("All fields are required.");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address.");
        }
        
        // Insert contact message into database
        $sql = "INSERT INTO contact_messages (user_id, name, email, message, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $name, $email, $message]);
        
        $submission_success = true;
    } catch (Exception $e) {
        $submission_error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Contact Order Tech Careers for support or inquiries about our job portal services.">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">

    <title>ORDER TECH | CONTACT US</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    
    <style>
        .contact-form {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .contact-form .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 10px;
        }
        .contact-form .form-control:focus {
            border-color: #a4c639;
            box-shadow: 0 0 0 3px rgba(164, 198, 57, 0.1);
        }
        .contact-info {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            border-left: 4px solid #a4c639;
        }
        .contact-info i {
            color: #a4c639;
            margin-right: 10px;
        }
        .alert {
            border-radius: 8px;
            padding: 15px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
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
            <h1>Contact Us</h1>
            <span>We're here to assist you with your career journey</span>
          </div>
        </div>
      </div>
    </div>

    <div class="contact-us">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="contact-form">
              <h4>Send Us a Message</h4>
              <?php if ($submission_success): ?>
                <div class="alert alert-success">
                  <i class="fa fa-check-circle"></i> Your message has been sent successfully!
                </div>
              <?php elseif ($submission_error): ?>
                <div class="alert alert-danger">
                  <i class="fa fa-exclamation-circle"></i> Error: <?php echo htmlspecialchars($submission_error); ?>
                </div>
              <?php endif; ?>
              <form id="contact" action="" method="post">
                <div class="row">
                  <div class="col-md-12">
                    <fieldset>
                      <input name="name" type="text" class="form-control" id="name" placeholder="Your Name" required>
                    </fieldset>
                  </div>
                  <div class="col-md-12">
                    <fieldset>
                      <input name="email" type="email" class="form-control" id="email" placeholder="Your Email" required>
                    </fieldset>
                  </div>
                  <div class="col-md-12">
                    <fieldset>
                      <textarea name="message" rows="6" class="form-control" id="message" placeholder="Your Message" required></textarea>
                    </fieldset>
                  </div>
                  <div class="col-md-12">
                    <fieldset>
                      <button type="submit" id="form-submit" class="filled-button">Send Message</button>
                    </fieldset>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="col-md-6">
            <div class="contact-info">
              <h4>Contact Information</h4>
              <ul class="list-unstyled">
                <li><i class="fa fa-phone"></i>(800) 555-1234</li>
                <li><i class="fa fa-envelope"></i>info@ordertech.com</li>
                <li><i class="fa fa-map-marker"></i>1234 Career Avenue, Tech City, TC 12345</li>
              </ul>
              <h4 class="mt-4">Follow Us</h4>
              <ul class="social-icons">
                <li><a href="#" class="social-share"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#" class="social-share"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#" class="social-share"><i class="fa fa-linkedin"></i></a></li>
              </ul>
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