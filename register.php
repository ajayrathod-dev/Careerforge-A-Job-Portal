<?php
session_start();
include 'connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT * FROM job_seekers WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = 'Username or email already exists!';
            } else {
                // Register user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO job_seekers (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$username, $email, $hashed_password]);
                
                // Get the newly created user ID
                $user_id = $pdo->lastInsertId();
                
                // Set session variables to log in the user
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                
                // Redirect to create_profile.php
                header("Location: create_profile.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Job Agency</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #d8dde1;
            color: #4e4747;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        header {
            background-color: #b8d030;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(177, 225, 233, 0.1);
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
            color: #f4f5f2;
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
        }

        nav ul li a:hover {
            color: #a4c639;
        }

        nav ul li a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #a4c639;
            transition: width 0.3s ease;
        }

        nav ul li a:hover::after {
            width: 100%;
        }

        .hero {
            background-color: #e9e1e1;
            background-image: linear-gradient(rgba(229, 239, 158, 0.8), rgba(35, 35, 35, 0.8)), url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="%23a4c639" opacity="0.1"/></svg>');
            color: white;
            padding: 70px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 42px;
            margin-bottom: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #a4c639;
            animation: fadeInDown 1s ease;
        }

        .hero p {
            font-size: 20px;
            max-width: 800px;
            margin: 0 auto;
            color: #0d0809;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .form-container {
            max-width: 450px;
            margin: 0 auto 50px;
            background-color: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(149, 177, 187, 0.1);
            animation: slideInUp 0.8s ease;
            transform: translateY(20px);
            opacity: 0;
            animation-fill-mode: forwards;
        }

        .form-container h2 {
            color: #232323;
            margin-bottom: 25px;
            text-align: center;
            font-size: 26px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            border-color: #a4c639;
            outline: none;
            box-shadow: 0 0 0 3px rgba(164, 198, 57, 0.2);
            transform: translateY(-2px);
        }

        .btn {
            display: inline-block;
            background-color: #b0df17;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            background-color: #8fb031;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn:active {
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .form-footer a {
            color: #398bc6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .form-footer a:hover {
            text-decoration: underline;
            color: #8fb031;
        }

        footer {
            background-color: #232323;
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-top: 60px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: shake 0.5s ease;
        }

        .alert-error {
            background-color: #ffeaea;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
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
            }

            nav ul li {
                margin: 0 10px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 18px;
            }

            .form-container {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container header-content">
            <div class="logo">JOB AGENCY</div>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="form-container">
                <h2>Create Your Account</h2>

                <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter Username" 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter Email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter Password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" class="btn">Register</button>
                </form>
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Job Agency. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>