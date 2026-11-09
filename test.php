<?php
session_start();
include 'connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username or email and password!';
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM job_seekers WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                
                header("Location: index-2.php");
                exit();
            } else {
                $error = 'Invalid username or email or password!';
            }
        } catch (PDOException $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Job Agency</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        header {
            background-color: #1e293b;
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #3b82f6;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            font-size: 32px;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 25px;
            position: relative;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            padding: 5px 0;
            display: flex;
            align-items: center;
        }
        
        nav ul li a i {
            margin-right: 8px;
        }
        
        nav ul li a:hover {
            color: #3b82f6;
        }
        
        nav ul li a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #3b82f6;
            transition: width 0.3s ease;
        }
        
        nav ul li a:hover::after {
            width: 100%;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        
        .form-container {
            width: 100%;
            max-width: 450px;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            animation: slideInUp 0.8s ease;
            transform: translateY(20px);
            opacity: 0;
            animation-fill-mode: forwards;
            border: 1px solid #e2e8f0;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-header h2 {
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 700;
        }
        
        .form-header p {
            color: #64748b;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #3b82f6;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #f8fafc;
        }
        
        .form-group input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            background-color: white;
        }
        
        .btn {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            width: 100%;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .form-footer p {
            margin-bottom: 10px;
            color: #64748b;
        }
        
        .form-footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
        }
        
        .form-footer a i {
            margin-right: 5px;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
            color: #2563eb;
        }
        
        footer {
            background-color: #1e293b;
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-top: auto;
        }
        
        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #3b82f6;
        }
        
        .footer-links {
            display: flex;
            margin-bottom: 20px;
        }
        
        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            margin: 0 15px;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: #3b82f6;
        }
        
        .copyright {
            color: #94a3b8;
            font-size: 14px;
        }
        
        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: shake 0.5s ease;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 15px;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            nav ul li {
                margin: 5px 10px;
            }
            
            .form-container {
                padding: 25px;
                margin: 20px;
            }
            
            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .footer-links a {
                margin: 5px 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-briefcase"></i>
                JOB AGENCY
            </div>
            <nav>
                <ul>
                    <li><a href="index.html"><i class="fas fa-home"></i>Home</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i>Login</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i>Register</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i>Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>Welcome Back</h2>
                    <p>Sign in to access your account</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i>Username or Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="username" name="username" placeholder="Enter your username or email" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i>Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn">
                        <i class="fas fa-sign-in-alt"></i>Login
                    </button>
                </form>
                <div class="form-footer">
                    <p>Don't have an account? <a href="register.php"><i class="fas fa-user-plus"></i>Register here</a></p>
                    <p><a href="f_password.php"><i class="fas fa-key"></i>Forgot your password?</a></p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">JOB AGENCY</div>
                <div class="footer-links">
                    <a href="index.html">Home</a>
                    <a href="about.html">About</a>
                    <a href="services.html">Services</a>
                    <a href="contact.php">Contact</a>
                    <a href="privacy.html">Privacy Policy</a>
                </div>
                <div class="copyright">
                    &copy; 2025 Job Agency. All rights reserved.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>