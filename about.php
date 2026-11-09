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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Learn about Order Tech Careers - Connecting talented professionals with top employers worldwide">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">

    <title>About Us | ORDER TECH CAREERS</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    
    <style>
        .about-mission {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 60px 0;
            margin: 40px 0;
        }
        .count-area-content {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .count-area-content:hover {
            transform: translateY(-5px);
        }
        .count-digit {
            font-size: 2.5rem;
            font-weight: bold;
            color: #a4c639;
            margin-bottom: 10px;
        }
        .count-title {
            font-size: 1.1rem;
            color: #4a5568;
            font-weight: 500;
        }
        .feature-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
            border-left: 4px solid #a4c639;
        }
        .feature-box:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #a4c639;
            margin-bottom: 20px;
        }
        .team-member {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .team-member:hover {
            transform: translateY(-5px);
        }
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #a4c639;
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
            <h1>About Order Tech Careers</h1>
            <span>Connecting Global Talent with Premier Opportunities</span>
          </div>
        </div>
      </div>
    </div>

    <div class="more-info about-info">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="more-info-content">
              <div class="row">
                <div class="col-md-6 align-self-center">
                  <div class="right-content">
                    <span>Our Vision</span>
                    <h2>Bridging Talent with <em>Global Opportunities</em></h2>
                    <p>At Order Tech Careers, we believe that talent knows no boundaries. Our mission is to connect exceptional professionals from around the world with life-changing career opportunities that match their skills, ambitions, and lifestyle preferences.</p>
                    <p>With a focus on international talent placement, we specialize in helping professionals navigate career transitions across borders, whether they're returning to their home country or exploring new international markets.</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="left-image">
                    <img src="assets/images/about-1-570x350.jpg" alt="Order Tech Careers Team" class="img-fluid rounded">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="about-mission">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
              <h2>Our <em>Mission</em> & Impact</h2>
              <span>Empowering careers worldwide</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="count-area-content">
              <div class="count-digit">5000+</div>
              <div class="count-title">Successful Placements</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="count-area-content">
              <div class="count-digit">25+</div>
              <div class="count-title">Countries Served</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="count-area-content">
              <div class="count-digit">98%</div>
              <div class="count-title">Client Satisfaction</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="count-area-content">
              <div class="count-digit">15+</div>
              <div class="count-title">Years Experience</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="more-info">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="more-info-content">
              <div class="row">
                <div class="col-md-6">
                  <div class="left-image">
                    <img src="assets/images/about-2-570x350.jpg" alt="Our Process" class="img-fluid rounded">
                  </div>
                </div>
                <div class="col-md-6 align-self-center">
                  <div class="right-content">
                    <span>Our Process</span>
                    <h2>How We <em>Connect Talent</em> with Opportunity</h2>
                    <p>Our proven process ensures that every candidate finds the right opportunity and every employer finds the perfect talent:</p>
                    <ul class="numbered-list">
                      <li><strong>Comprehensive Profiling:</strong> Detailed assessment of skills, experience, and career goals</li>
                      <li><strong>Strategic Matching:</strong> AI-powered matching with suitable opportunities</li>
                      <li><strong>Interview Coordination:</strong> End-to-end interview scheduling and preparation</li>
                      <li><strong>Offer Negotiation:</strong> Expert guidance through the offer and negotiation process</li>
                      <li><strong>Relocation Support:</strong> Assistance with visas, housing, and cultural integration</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="team-section" style="padding: 60px 0; background: #f8f9fa;">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center mb-5">
            <h2>Meet Our Leadership Team</h2>
            <p class="lead">Experienced professionals dedicated to your career success</p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="team-member">
              <img src="assets/images/team-1.jpg" alt="Sarah Johnson">
              <h4>Sarah Johnson</h4>
              <p class="text-primary">CEO & Founder</p>
              <p>15+ years in international recruitment and talent management</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="team-member">
              <img src="assets/images/team-2.jpg" alt="Michael Chen">
              <h4>Michael Chen</h4>
              <p class="text-primary">Head of Global Operations</p>
              <p>Expert in cross-border career transitions and relocation</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="team-member">
              <img src="assets/images/team-3.jpg" alt="Priya Sharma">
              <h4>Priya Sharma</h4>
              <p class="text-primary">Director of Client Success</p>
              <p>Specialized in tech and engineering talent placement</p>
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