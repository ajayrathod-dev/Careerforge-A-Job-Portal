<?php
session_start();
// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Read testimonials from job seekers and employers who have succeeded with Order Tech Careers.">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">

    <title>ORDER TECH | TESTIMONIALS</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
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

    <!-- Header Include -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <div class="page-heading header-text">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <h1>Testimonials</h1>
            <span>What our clients say about us</span>
          </div>
        </div>
      </div>
    </div>

    <div class="testimonials" style="margin: 0">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="owl-testimonials owl-carousel">
              
              <div class="testimonial-item">
                <div class="inner-content">
                  <h4>Emily Carter</h4>
                  <span>Senior Software Developer</span>
                  <p>"Order Tech Careers helped me land my dream job at a top tech firm. Their personalized guidance and job matching were exceptional. Highly recommended!"</p>
                </div>
                <img src="http://placehold.it/60x60" alt="">
              </div>
              
              <div class="testimonial-item">
                <div class="inner-content">
                  <h4>Michael Rodriguez</h4>
                  <span>Marketing Director</span>
                  <p>"The platform's user-friendly interface and vast job listings made my job search efficient. I secured a great position within weeks!"</p>
                </div>
                <img src="http://placehold.it/60x60" alt="">
              </div>
              
              <div class="testimonial-item">
                <div class="inner-content">
                  <h4>Sophia Chen</h4>
                  <span>UX Designer</span>
                  <p>"Outstanding support from the team. They provided valuable resume tips and interview coaching that boosted my confidence."</p>
                </div>
                <img src="http://placehold.it/60x60" alt="">
              </div>
              
              <div class="testimonial-item">
                <div class="inner-content">
                  <h4>David Patel</h4>
                  <span>Financial Analyst</span>
                  <p>"Impressive network of employers. Order Tech connected me with opportunities I wouldn't have found elsewhere. Truly grateful!"</p>
                </div>
                <img src="http://placehold.it/60x60" alt="">
              </div>
              
              <div class="testimonial-item">
                <div class="inner-content">
                  <h4>Robert Kim</h4>
                  <span>HR Manager</span>
                  <p>"As an employer, Order Tech streamlined our hiring. We found qualified candidates quickly, saving time and resources. Great service for businesses too!"</p>
                </div>
                <img src="http://placehold.it/60x60" alt="">
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Starts Here -->
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-md-3 footer-item">
            <h4>Order Tech Careers</h4>
            <p>We connect job seekers with top employers, offering tools and resources to help you succeed in your career journey.</p>
            <ul class="social-icons">
              <li><a rel="nofollow" href="#" target="_blank"><i class="fa fa-facebook"></i></a></li>
              <li><a href="#"><i class="fa fa-twitter"></i></a></li>
              <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
            </ul>
          </div>
          <div class="col-md-3 footer-item">
            <h4>Useful Links</h4>
            <ul class="menu-list">
              <li><a href="#">Career Advice</a></li>
              <li><a href="#">Resume Tips</a></li>
              <li><a href="#">Interview Preparation</a></li>
              <li><a href="#">Job Search Strategies</a></li>
              <li><a href="#">Networking Guide</a></li>
            </ul>
          </div>
          <div class="col-md-3 footer-item">
            <h4>Additional Pages</h4>
            <ul class="menu-list">
              <li><a href="#">About Us</a></li>
              <li><a href="#">Blog</a></li>
              <li><a href="#">Testimonials</a></li>
              <li><a href="#">Contact Us</a></li>
              <li><a href="#">Terms</a></li>
            </ul>
          </div>
          <div class="col-md-3 footer-item last-item">
            <h4>Contact Us</h4>
            <div class="contact-form">
              <form id="contact footer-contact" action="#" method="post">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <fieldset>
                      <input name="name" type="text" class="form-control" id="name" placeholder="Full Name" required="">
                    </fieldset>
                  </div>
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <fieldset>
                      <input name="email" type="text" class="form-control" id="email" pattern="[^ @]*@[^ @]*" placeholder="Email Address" required="">
                    </fieldset>
                  </div>
                  <div class="col-lg-12">
                    <fieldset>
                      <textarea name="message" rows="6" class="form-control" id="message" placeholder="Your Message" required=""></textarea>
                    </fieldset>
                  </div>
                  <div class="col-lg-12">
                    <fieldset>
                      <button type="submit" id="form-submit" class="filled-button">Send Message</button>
                    </fieldset>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </footer>
    
    <div class="sub-footer">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <p>
                Copyright © 2025 Order Tech Careers
                - Template by: <a href="https://www.phpjabbers.com/">PHPJabbers.com</a>
            </p>
          </div>
        </div>
      </div>
    </div>

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