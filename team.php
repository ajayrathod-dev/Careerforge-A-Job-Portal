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

// Sample team data (could be fetched from database)
$team_members = [
    [
        'name' => 'Sarah Johnson',
        'position' => 'CEO & Founder',
        'description' => '15+ years in international recruitment and talent management',
        'image' => 'assets/images/team-1.jpg'
    ],
    [
        'name' => 'Michael Chen',
        'position' => 'Head of Global Operations',
        'description' => 'Expert in cross-border career transitions and relocation',
        'image' => 'assets/images/team-2.jpg'
    ],
    [
        'name' => 'Priya Sharma',
        'position' => 'Director of Client Success',
        'description' => 'Specialized in tech and engineering talent placement',
        'image' => 'assets/images/team-3.jpg'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Meet the team behind Order Tech Careers, dedicated to connecting talent with opportunities.">
    <meta name="author" content="Order Tech Team">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">

    <title>ORDER TECH | OUR TEAM</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    
    <style>
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
        .team-member h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .team-member p {
            color: #4a5568;
        }
        .team-member .text-primary {
            color: #a4c639;
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
            <h1>Our Team</h1>
            <span>Meet the experts behind your career success</span>
          </div>
        </div>
      </div>
    </div>

    <div class="team-section" style="padding: 60px 0; background: #f8f9fa;">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center mb-5">
            <h2>Our Leadership Team</h2>
            <p class="lead">Dedicated professionals committed to connecting talent with opportunity</p>
          </div>
        </div>
        <div class="row">
          <?php foreach ($team_members as $member): ?>
            <div class="col-md-4">
              <div class="team-member">
                <img src="<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                <p class="text-primary"><?php echo htmlspecialchars($member['position']); ?></p>
                <p><?php echo htmlspecialchars($member['description']); ?></p>
              </div>
            </div>
          <?php endforeach; ?>
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