<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get all available products
$sql = "SELECT p.*, u.username, u.full_name 
        FROM products p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.status = 'available' 
        ORDER BY p.created_at DESC";
$products = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - ReMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #2B2D42 0%, #8D99AE 100%);
            min-height: 100vh;
            color: #2B2D42;
            padding: 2rem;
        }

        /* Navbar */
        .navbar {
            background: white;
            border-radius: 20px;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
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
            transition: all 0.3s;
        }

        .nav-links a:hover {
            background: #EDF2F4;
            color: #EF233C;
        }

        .sell-btn {
            background: #EF233C;
            color: white !important;
        }

        .sell-btn:hover {
            background: #d91e35;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .welcome-banner {
            text-align: center;
            background: linear-gradient(135deg, #2B2D42 0%, #8D99AE 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .welcome-banner h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .products-header h2 {
            color: #2B2D42;
        }

        /* Product Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background: #fff;
            border: 2px solid #EDF2F4;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-color: #EF233C;
        }

        .product-image {
            width: 100%;
            height: 200px;
            background: #EDF2F4;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 1rem 1.5rem;
        }

        .badge {
            display: inline-block;
            background: #EDF2F4;
            color: #EF233C;
            font-size: 0.8rem;
            padding: 0.3rem 0.7rem;
            border-radius: 15px;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .product-title {
            font-weight: 700;
            color: #2B2D42;
            margin-bottom: 0.5rem;
        }

        .product-description {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 1rem;
            height: 40px;
            overflow: hidden;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #EDF2F4;
            padding-top: 0.8rem;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #EF233C;
        }

        .product-seller {
            font-size: 0.85rem;
            color: #8D99AE;
        }

        .buy-btn {
            background: #EF233C;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .buy-btn:hover {
            background: #d91e35;
            transform: translateY(-2px);
        }

        .my-product-badge {
            background: #EDF2F4;
            color: #8D99AE;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .no-products {
            text-align: center;
            padding: 3rem;
            color: #8D99AE;
        }

        footer {
            text-align: center;
            color: white;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="home.php" class="logo"><i class="fas fa-recycle"></i> ReMarket</a>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="sell_product.php" class="sell-btn">+ Sell Product</a>
            <a href="orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-banner">
            <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>! 👋</h1>
            <p>Discover amazing pre-loved items or list something you want to sell.</p>
        </div>

        <div class="products-header">
            <h2>Available Products</h2>
        </div>

        <div class="products-grid">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?php echo UPLOAD_URL . htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <?php else: ?>
                                <i class="fas fa-box-open fa-3x" style="color:#8D99AE;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <span class="badge"><?php echo ucfirst($product['condition_type']); ?></span>
                            <div class="product-title"><?php echo htmlspecialchars($product['title']); ?></div>
                            <div class="product-description"><?php echo htmlspecialchars($product['description']); ?></div>
                            <div class="product-footer">
                                <div>
                                    <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                                    <div class="product-seller">by <?php echo htmlspecialchars($product['username']); ?></div>
                                </div>
                                <?php if ($product['user_id'] != getUserId()): ?>
                                    <form action="buy_product.php" method="POST" style="margin: 0;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="buy-btn">Buy Now</button>
                                    </form>
                                <?php else: ?>
                                    <span class="my-product-badge">Your Product</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-products">
                    <h3>No products available yet</h3>
                    <p>Be the first to list a product!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> ReMarket. All rights reserved.
    </footer>
</body>
</html>
