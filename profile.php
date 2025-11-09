<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = getUserId();
$error = '';
$success = '';

if ($user_id === null) {
    session_destroy();
    redirect('login.php');
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    redirect('login.php');
}

$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    if (empty($full_name) || empty($email)) {
        $error = "Name and email are required";
    } else {
        $update_sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);
        
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            $_SESSION['full_name'] = $full_name;
            $stmt2 = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();
            $user = $stmt2->get_result()->fetch_assoc();
            $stmt2->close();
        } else {
            $error = "Failed to update profile";
        }
        $stmt->close();
    }
}

function countRows($conn, $query, $id) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows > 0 ? $result->fetch_assoc()['count'] : 0;
    $stmt->close();
    return $count;
}

$products_count = countRows($conn, "SELECT COUNT(*) as count FROM products WHERE user_id = ?", $user_id);
$sales_count = countRows($conn, "SELECT COUNT(*) as count FROM orders WHERE seller_id = ?", $user_id);
$purchases_count = countRows($conn, "SELECT COUNT(*) as count FROM orders WHERE buyer_id = ?", $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ReMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2B2D42 0%, #8D99AE 100%);
            color: #2B2D42;
        }

        .navbar {
            background: white;
            border-radius: 20px;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin: 1.5rem auto 2rem;
            max-width: 1100px;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #EF233C;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links a {
            color: #2B2D42;
            text-decoration: none;
            margin-left: 1rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            transition: 0.3s;
        }

        .nav-links a:hover {
            background: #EDF2F4;
            color: #EF233C;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-avatar {
            width: 110px;
            height: 110px;
            background: #EF233C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1rem;
        }

        .profile-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2B2D42;
        }

        .profile-username {
            color: #8D99AE;
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: #F8F9FA;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            border: 1px solid #EDF2F4;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 2rem;
            color: #EF233C;
            font-weight: bold;
        }

        .stat-label {
            color: #8D99AE;
            font-weight: 500;
        }

        h2 {
            color: #2B2D42;
            margin-bottom: 1rem;
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

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2B2D42;
        }

        input, textarea {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #EDF2F4;
            border-radius: 10px;
            font-size: 1rem;
            transition: 0.3s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #EF233C;
            box-shadow: 0 0 0 3px rgba(239, 35, 60, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: #EF233C;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background: #d91e35;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .container { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="home.php" class="logo"><i class="fas fa-recycle"></i> ReMarket</a>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="sell_product.php">Sell Product</a>
            <a href="orders.php">My Orders</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="profile-header">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
            <div class="profile-username">@<?php echo htmlspecialchars($user['username']); ?></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $products_count; ?></div>
                <div class="stat-label">Products Listed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $sales_count; ?></div>
                <div class="stat-label">Items Sold</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $purchases_count; ?></div>
                <div class="stat-label">Purchases Made</div>
            </div>
        </div>

        <div class="profile-form">
            <h2>Edit Profile</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="submit-btn">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
