<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('home.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        $sql = "SELECT id, username, password, full_name, email FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = intval($user['id']);
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                redirect('home.php');
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
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
    <title>Login - ReMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            position: relative;
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
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
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

        .error-message {
            background: #fee;
            color: #c33;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
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

        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #8D99AE;
        }

        .signup-link a {
            color: #EF233C;
            font-weight: 600;
            text-decoration: none;
        }

        .signup-link a:hover {
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
        <!-- Left Branding Section -->
        <div class="auth-branding">
            <i class="fas fa-recycle"></i>
            <h1>ReMarket</h1>
            <p>Give your products a second life! Buy and sell with ease.</p>
        </div>

        <!-- Right Login Form -->
        <div class="auth-form">
            <h2>Welcome Back!</h2>
            <p>Login to continue your shopping journey</p>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" placeholder="your@email.com" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <div class="signup-link">
                Don’t have an account? <a href="signup.php">Sign Up</a>
            </div>

            <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</body>
</html>
