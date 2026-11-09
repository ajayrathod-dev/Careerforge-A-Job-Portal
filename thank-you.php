<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: add_portfolio.php?error=' . urlencode('Authentication required'));
    exit;
}

// Database connection
require_once 'connection.php';

// Get portfolio_id from query parameter
$portfolio_id = $_GET['portfolio_id'] ?? '';

if (empty($portfolio_id)) {
    header('Location: add_portfolio.php?error=' . urlencode('Invalid portfolio ID'));
    exit;
}

// Verify portfolio belongs to the logged-in user
$stmt = $pdo->prepare("SELECT id FROM portfolios WHERE id = ? AND user_id = ? AND status = 'completed'");
$stmt->execute([$portfolio_id, $_SESSION['user_id']]);
$portfolio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$portfolio) {
    header('Location: add_portfolio.php?error=' . urlencode('Invalid or unauthorized portfolio ID'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congrats! Your Portfolio is Submitted! - Order Tech Careers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        :root {
            --primary-color: #a4c639;
            --secondary-color: #2a2a2a;
            --accent-color: #ff6f61;
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        body {
            background: var(--bg-gradient);
            font-family: 'Arial', sans-serif;
        }
        .thank-you-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            animation: fadeIn 1s ease-in;
        }
        .success-icon {
            font-size: 5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            animation: bounce 1s infinite;
        }
        h1 {
            color: var(--secondary-color);
            font-weight: bold;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .lead {
            color: var(--secondary-color);
            font-size: 1.25rem;
            font-weight: 500;
        }
        .portfolio-id {
            background: #e9f7ef;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            display: inline-block;
            margin: 1rem 0;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            background-color: #8cb230;
        }
        .btn-outline-primary {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }
        .btn-outline-primary:hover {
            background-color: var(--accent-color);
            color: white;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body onload="launchConfetti()">
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="thank-you-container">
                    <div class="success-card">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h1>Congratulations!</h1>
                        <p class="lead">Woohoo! Your portfolio is officially submitted!</p>
                        <p class="portfolio-id">Portfolio ID: <strong><?php echo htmlspecialchars($portfolio_id); ?></strong></p>
                        <p>Our awesome team is already excited to review your profile and will reach out soon with next steps!</p>
                        <div class="mt-4">
                            <a href="index-2.php" class="btn btn-primary me-2">
                                <i class="fas fa-home"></i> Back to Home
                            </a>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script>
        function launchConfetti() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#a4c639', '#ff6f61', '#2a2a2a']
            });
        }
    </script>
</body>
</html>