<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username or email already exists";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $phone);
            
            if ($stmt->execute()) {
                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ReMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2B2D42 0%, #8D99AE 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Left Side - Branding */
        .auth-branding {
            background: linear-gradient(135deg, #2B2D42 0%, #8D99AE 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .auth-branding i {
            font-size: 3rem;
            color: #EF233C;
            margin-bottom: 1rem;
        }

        .auth-branding h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .auth-branding p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 300px;
        }

        /* Right Side - Form */
        .auth-form {
            padding: 3rem;
        }

        .auth-form h2 {
            font-size: 2rem;
            color: #2B2D42;
            margin-bottom: 0.5rem;
        }

        .auth-form p {
            color: #8D99AE;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            margin-bottom: 0.4rem;
            color: #2B2D42;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #EDF2F4;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #EF233C;
            box-shadow: 0 0 0 3px rgba(239, 35, 60, 0.1);
        }

        .error-message, .success-message {
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
        }

        .error-message {
            background: #fee;
            color: #b91c1c;
        }

        .success-message {
            background: #ecfdf5;
            color: #059669;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #EF233C;
            color: white;
        }

        .btn-primary:hover {
            background: #d91e35;
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #8D99AE;
        }

        .login-link a {
            color: #EF233C;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            opacity: 0.8;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #8D99AE;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-home:hover {
            color: #EF233C;
        }

        @media (max-width: 768px) {
            .auth-container {
                grid-template-columns: 1fr;
            }
            .auth-branding {
                display: none;
            }
            .auth-form {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Left Side: Brand Section -->
        <div class="auth-branding">
            <i class="fas fa-recycle"></i>
            <h1>Join ReMarket</h1>
            <p>Buy, sell, and give your items a second life!</p>
        </div>

        <!-- Right Side: Signup Form -->
        <div class="auth-form">
            <h2>Create Account</h2>
            <p>Sign up to get started on ReMarket</p>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone (optional)</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign Up</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Login</a>
            </div>

            <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</body>
</html>
