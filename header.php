<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check - redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current page for active class
$current_page = basename($_SERVER['PHP_SELF']);
$about_pages = ['about.php', 'team.php', 'blog.php', 'categories.php', 'testimonials.php', 'terms.php'];
$profile_pages = ['profile.php', 'resume.php'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Find your dream job with Order Tech's job portal, connecting job seekers with top employers.">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">

    <title>ORDER TECH | JOB SEEKER</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome 6.5.0 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-MXxJHkPKt9gXvN3oS8H7AXH5Ncc+bwCTZcG4Dh7Pf3F+HjPtfGdQmAA1eM7t2sV+jEXsWzTukBi4RzP9Y3vUZg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    
    <style>
        :root {
            --primary: #bddd2eff;
            --primary-dark: #72d6a1ff;
            --secondary: #484ac9ff;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e5dedeff;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Header Styles */
        .sub-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 8px 0;
            color: white;
        }
        
        .sub-header .left-info {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 20px;
        }
        
        .sub-header .left-info li {
            margin: 0;
        }
        
        .sub-header .left-info li a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .sub-header .left-info li a:hover {
            color: white;
            transform: translateY(-1px);
        }
        
        .sub-header .right-icons {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        
        .sub-header .right-icons li {
            margin: 0;
        }
        
        .sub-header .right-icons li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sub-header .right-icons li a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Navbar Styles - Transparent without blur */
        .navbar {
            background: transparent !important;
            border: none !important;
            padding: 20px 0;
            transition: all 0.3s ease;
            position: absolute;
            top: 50px;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            padding: 12px 0;
        }
        
        .navbar-brand h2 {
            color: white;
            font-weight: 700;
            margin: 0;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled .navbar-brand h2 {
            color: var(--dark);
        }
        
        .navbar-brand h2 em {
            color: #c7da8cff;
            font-style: normal;
            font-weight: 600;
        }
        
        .navbar.scrolled .navbar-brand h2 em {
            color: var(--primary);
        }
        
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 8px 20px !important;
            margin: 0 4px;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .navbar.scrolled .navbar-nav .nav-link {
            color: var(--dark) !important;
        }
        
        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .navbar.scrolled .navbar-nav .nav-link:hover {
            color: var(--primary) !important;
            background: rgba(16, 185, 129, 0.1);
        }
        
        .navbar-nav .nav-item.active .nav-link {
            color: white !important;
            background: rgba(255, 255, 255, 0.25);
        }
        
        .navbar.scrolled .navbar-nav .nav-item.active .nav-link {
            color: var(--primary) !important;
            background: rgba(16, 185, 129, 0.15);
        }
        
        /* Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: 12px;
            padding: 8px;
            background: white;
            border: 1px solid var(--border);
            min-width: 200px;
        }
        
        .dropdown-item {
            padding: 10px 16px;
            border-radius: 8px;
            color: var(--dark);
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 2px 0;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            transform: translateX(5px);
        }
        
        .dropdown-item.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .dropdown-divider {
            margin: 8px 0;
            opacity: 0.3;
        }
        
        /* Navbar Toggler */
        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled .navbar-toggler {
            border-color: rgba(16, 185, 129, 0.2);
        }
        
        .navbar-toggler:hover {
            border-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.05);
        }
        
        .navbar.scrolled .navbar-toggler:hover {
            border-color: var(--primary);
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }
        
        .navbar.scrolled .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            width: 1.2em;
            height: 1.2em;
        }
        
        .navbar.scrolled .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2816, 185, 129, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* Profile dropdown specific */
        .navbar-nav .nav-item.dropdown .nav-link {
            background: linear-gradient(135deg, #a5d835ff, #42dab7ff);
            color: white !important;
            border-radius: 25px;
            padding: 8px 20px !important;
            margin-left: 10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .navbar-nav .nav-item.dropdown .nav-link:hover {
            background: linear-gradient(135deg, #29a757ff, #7d9c27);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(164, 198, 57, 0.3);
        }
        
        /* Mobile Styles */
        @media (max-width: 991px) {
            .navbar {
                background: rgba(255, 255, 255, 0.95) !important;
                position: relative;
                top: 0;
            }
            
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 12px;
                padding: 20px;
                margin-top: 10px;
                box-shadow: var(--shadow-lg);
                border: 1px solid var(--border);
            }
            
            .navbar-nav .nav-link {
                color: var(--dark) !important;
                margin: 4px 0;
                text-align: center;
            }
            
            .navbar-nav .nav-item.dropdown .nav-link {
                margin: 8px 0;
                justify-content: center;
            }
            
            .dropdown-menu {
                border: none;
                box-shadow: none;
                background: rgba(248, 250, 252, 0.8);
            }
            
            .navbar-brand h2 {
                color: var(--dark);
            }
        }
        
        /* Animation for navbar */
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .navbar {
            animation: slideInDown 0.5s ease-out;
        }
        
        /* Preloader Styles */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .jumper {
            display: flex;
            gap: 8px;
        }
        
        .jumper div {
            width: 12px;
            height: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            animation: jump 1s infinite ease-in-out;
        }
        
        .jumper div:nth-child(1) { animation-delay: 0s; }
        .jumper div:nth-child(2) { animation-delay: 0.2s; }
        .jumper div:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes jump {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        /* Smooth transitions */
        * {
            transition: color 0.3s ease, background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }
    </style>
</head>

<body>
    <!-- ***** Preloader Start ***** -->
    <div id="preloader">
        <div class="jumper">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>  
    <!-- ***** Preloader End ***** -->

    <!-- Header -->
    <div class="sub-header">
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-xs-12">
            <ul class="left-info">
              <li><a href="#"><i class="fas fa-envelope"></i>info@ordertech.com</a></li>
              <li><a href="#"><i class="fas fa-phone"></i>(800) 555-1234</a></li>
            </ul>
          </div>
          <div class="col-md-4">
            <ul class="right-icons">
              <li><a href="#"><i class="fab fa-facebook"></i></a></li>
              <li><a href="#"><i class="fab fa-twitter"></i></a></li>
              <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <header class="">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <a class="navbar-brand" href="index-2.php"><h2>Order Tech <em>Careers</em></h2></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item <?php echo ($current_page == 'index-2.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="index-2.php">Home
                  <span class="sr-only">(current)</span>
                </a>
              </li>
              <li class="nav-item dropdown <?php echo (in_array($current_page, $about_pages)) ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  About
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a></li>
                    <li><a class="dropdown-item <?php echo ($current_page == 'team.php') ? 'active' : ''; ?>" href="team.php">Team</a></li>
                    <li><a class="dropdown-item <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>" href="blog.php">Blog</a></li>
                    <li><a class="dropdown-item <?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>" href="categories.php">Job Categories</a></li>
                    <li><a class="dropdown-item <?php echo ($current_page == 'testimonials.php') ? 'active' : ''; ?>" href="testimonials.php">Testimonials</a></li>
                    <li><a class="dropdown-item <?php echo ($current_page == 'terms.php') ? 'active' : ''; ?>" href="terms.php">Terms</a></li>
                </ul>
              </li>
              <li class="nav-item <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="contact.php">Contact Us</a>
              </li>
              <li class="nav-item dropdown <?php echo (in_array($current_page, $profile_pages)) ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']); ?>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>" href="profile.php"><i class="fas fa-user-circle me-2"></i>View Profile</a></li>
                    <li><a class="dropdown-item <?php echo ($current_page == 'resume.php') ? 'active' : ''; ?>" href="add_portfolio.php"><i class="fas fa-file-alt me-2"></i>Update Portfolio</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <!-- Bootstrap 5 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery for legacy compatibility -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Initialize Bootstrap components and add custom animations
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });

            // Navbar scroll effect
            function handleScroll() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 100) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }

            window.addEventListener('scroll', handleScroll);
            handleScroll(); // Initialize on load

            // Remove preloader when page loads
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const preloader = document.getElementById('preloader');
                    if (preloader) {
                        preloader.style.opacity = '0';
                        preloader.style.visibility = 'hidden';
                        setTimeout(() => preloader.remove(), 500);
                    }
                }, 1000);
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add hover effects to navbar links
            document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Add animation to dropdown items
            document.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(8px)';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
</body>
</html>