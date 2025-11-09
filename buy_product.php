<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';
$product = null;

if (isset($_POST['product_id']) || isset($_GET['product_id'])) {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : intval($_GET['product_id']);
    
    $sql = "SELECT p.*, u.username, u.full_name 
            FROM products p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = ? AND p.status = 'available'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
        if ($product['user_id'] == getUserId()) {
            $error = "You cannot buy your own product";
            $product = null;
        }
    } else {
        $error = "Product not found or no longer available";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_purchase']) && $product) {
    $shipping_address = sanitize($_POST['shipping_address']);
    $buyer_id = getUserId();
    $seller_id = $product['user_id'];
    $product_id = $product['id'];
    $total_amount = $product['price'];
    
    if (empty($shipping_address)) {
        $error = "Shipping address is required";
    } else {
        $conn->begin_transaction();
        try {
            $order_sql = "INSERT INTO orders (buyer_id, product_id, seller_id, total_amount, shipping_address, status) 
                          VALUES (?, ?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($order_sql);
            $stmt->bind_param("iiids", $buyer_id, $product_id, $seller_id, $total_amount, $shipping_address);
            $stmt->execute();
            
            $update_sql = "UPDATE products SET status = 'sold' WHERE id = ?";
            $stmt2 = $conn->prepare($update_sql);
            $stmt2->bind_param("i", $product_id);
            $stmt2->execute();
            
            $conn->commit();
            $success = "Order placed successfully! Redirecting...";
            header("refresh:2;url=orders.php");
            
            $stmt->close();
            $stmt2->close();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to place order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Product - ReMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2B2D42 0%, #8D99AE 100%);
            color: #2B2D42;
            line-height: 1.6;
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
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #2B2D42;
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

        .product-summary {
            background: #F8F9FA;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            border: 1px solid #EDF2F4;
        }

        .badge {
            display: inline-block;
            padding: 0.4rem 0.9rem;
            background: rgba(239, 35, 60, 0.1);
            color: #EF233C;
            border-radius: 15px;
            font-size: 0.85rem;
            margin-bottom: 0.8rem;
            border: 1px solid rgba(239, 35, 60, 0.2);
        }

        .product-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #2B2D42;
        }

        .product-seller {
            color: #8D99AE;
            margin: 0.5rem 0;
        }

        .product-price {
            font-size: 1.8rem;
            color: #EF233C;
            font-weight: bold;
            margin-top: 1rem;
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

        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #EDF2F4;
            border-radius: 10px;
            font-size: 1rem;
            resize: vertical;
            transition: 0.3s;
        }

        textarea:focus {
            outline: none;
            border-color: #EF233C;
            box-shadow: 0 0 0 3px rgba(239, 35, 60, 0.1);
        }

        .order-summary {
            background: #F8F9FA;
            border: 1px solid #EDF2F4;
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }

        .summary-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: #EF233C;
            border-top: 2px solid #EDF2F4;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-size: 1rem;
        }

        .btn-primary {
            background: #EF233C;
            color: white;
        }

        .btn-primary:hover {
            background: #d91e35;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: #EDF2F4;
            color: #2B2D42;
        }

        .btn-cancel:hover {
            background: #E2E8F0;
        }

        @media (max-width: 768px) {
            .container { padding: 1.5rem; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="home.php" class="logo"><i class="fas fa-recycle"></i> ReMarket</a>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1>Complete Your Purchase</h1>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
            <a href="home.php" class="btn btn-cancel">← Back to Home</a>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($product && !$success): ?>
            <div class="product-summary">
                <span class="badge"><?php echo ucfirst($product['condition_type']); ?></span>
                <div class="product-title"><?php echo htmlspecialchars($product['title']); ?></div>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="product-seller">Seller: <?php echo htmlspecialchars($product['full_name']); ?></div>
                <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="form-group">
                    <label for="shipping_address">Shipping Address *</label>
                    <textarea id="shipping_address" name="shipping_address" placeholder="Enter your complete shipping address..." required></textarea>
                </div>

                <div class="order-summary">
                    <div class="summary-row"><span>Product Price:</span><span>₹<?php echo number_format($product['price'], 2); ?></span></div>
                    <div class="summary-row"><span>Shipping:</span><span>Free</span></div>
                    <div class="summary-row summary-total"><span>Total:</span><span>₹<?php echo number_format($product['price'], 2); ?></span></div>
                </div>

                <div class="btn-group">
                    <a href="home.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" name="confirm_purchase" class="btn btn-primary">Confirm Purchase</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
