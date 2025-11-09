<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = getUserId();

// Get orders where user is buyer
$buyer_sql = "SELECT o.*, p.title, p.image_url, u.username as seller_username, u.full_name as seller_name 
              FROM orders o 
              JOIN products p ON o.product_id = p.id 
              JOIN users u ON o.seller_id = u.id 
              WHERE o.buyer_id = ? 
              ORDER BY o.created_at DESC";
$buyer_stmt = $conn->prepare($buyer_sql);
$buyer_stmt->bind_param("i", $user_id);
$buyer_stmt->execute();
$buyer_orders = $buyer_stmt->get_result();

// Get orders where user is seller
$seller_sql = "SELECT o.*, p.title, p.image_url, u.username as buyer_username, u.full_name as buyer_name 
               FROM orders o 
               JOIN products p ON o.product_id = p.id 
               JOIN users u ON o.buyer_id = u.id 
               WHERE o.seller_id = ? 
               ORDER BY o.created_at DESC";
$seller_stmt = $conn->prepare($seller_sql);
$seller_stmt->bind_param("i", $user_id);
$seller_stmt->execute();
$seller_orders = $seller_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - ReMarket</title>
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
            color: #2B2D42;
            line-height: 1.6;
            padding-bottom: 3rem;
        }

        /* Navbar */
        .navbar {
            background: white;
            border-radius: 20px;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin: 1.5rem auto 2rem;
            max-width: 1200px;
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
            transition: all 0.3s;
        }

        .nav-links a:hover {
            background: #EDF2F4;
            color: #EF233C;
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

        h1 {
            color: #2B2D42;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 1rem;
            background: #EDF2F4;
            padding: 0.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            background: transparent;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            color: #8D99AE;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tab.active {
            background: #EF233C;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239,35,60,0.3);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Orders */
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .order-card {
            background: white;
            border: 2px solid #EDF2F4;
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-color: #EF233C;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid #EDF2F4;
        }

        .order-id {
            font-weight: bold;
        }

        .order-date {
            color: #8D99AE;
            font-size: 0.9rem;
        }

        .order-body {
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 1.5rem;
            align-items: center;
        }

        .order-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            background: #EDF2F4;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #EF233C;
            font-size: 2rem;
            overflow: hidden;
        }

        .order-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #2B2D42;
        }

        .order-meta {
            color: #8D99AE;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .order-price {
            font-weight: bold;
            color: #EF233C;
            font-size: 1.3rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pending { background: #fff5e6; color: #d97706; }
        .status-delivered { background: #ecfdf5; color: #059669; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .shipping-address {
            background: #F8F9FA;
            padding: 0.8rem;
            border-radius: 10px;
            margin-top: 0.8rem;
            font-size: 0.9rem;
            color: #555;
            border: 1px solid #EDF2F4;
        }

        .action-buttons {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .no-orders {
            text-align: center;
            padding: 3rem 2rem;
            background: #F8F9FA;
            border-radius: 15px;
            color: #8D99AE;
        }

        @media (max-width: 768px) {
            .order-body {
                grid-template-columns: 1fr;
            }
            .order-price {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="home.php" class="logo"><i class="fas fa-recycle"></i> ReMarket</a>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="sell_product.php">Sell</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1>My Orders</h1>

        <div class="tabs">
            <button class="tab active" onclick="switchTab('purchases', event)">My Purchases</button>
            <button class="tab" onclick="switchTab('sales', event)">My Sales</button>
        </div>

        <!-- Purchases -->
        <div id="purchases" class="tab-content active">
            <div class="orders-list">
                <?php if ($buyer_orders->num_rows > 0): ?>
                    <?php while ($order = $buyer_orders->fetch_assoc()): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <div class="order-id">Order #<?php echo $order['id']; ?></div>
                                    <div class="order-date"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></div>
                                </div>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                            <div class="order-body">
                                <div class="order-image">
                                    <?php if ($order['image_url']): ?>
                                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($order['image_url']); ?>" alt="">
                                    <?php else: ?>
                                        <i class="fas fa-box"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="product-title"><?php echo htmlspecialchars($order['title']); ?></div>
                                    <div class="order-meta">Seller: <?php echo htmlspecialchars($order['seller_name']); ?></div>
                                    <div class="shipping-address">
                                        <strong>Shipping to:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                                    </div>
                                </div>
                                <div class="order-price">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-orders">
                        <h3>No purchases yet</h3>
                        <p>Start shopping to see your orders here!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sales -->
        <div id="sales" class="tab-content">
            <div class="orders-list">
                <?php if ($seller_orders->num_rows > 0): ?>
                    <?php while ($order = $seller_orders->fetch_assoc()): ?>
                        <div class="order-card" id="order-<?php echo $order['id']; ?>">
                            <div class="order-header">
                                <div>
                                    <div class="order-id">Order #<?php echo $order['id']; ?></div>
                                    <div class="order-date"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></div>
                                </div>
                                <span class="status-badge status-<?php echo $order['status']; ?>" id="status-<?php echo $order['id']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                            <div class="order-body">
                                <div class="order-image">
                                    <?php if ($order['image_url']): ?>
                                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($order['image_url']); ?>" alt="">
                                    <?php else: ?>
                                        <i class="fas fa-box"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="product-title"><?php echo htmlspecialchars($order['title']); ?></div>
                                    <div class="order-meta">Buyer: <?php echo htmlspecialchars($order['buyer_name']); ?></div>
                                    <div class="shipping-address">
                                        <strong>Ship to:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                                    </div>
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <div class="action-buttons">
                                            <button class="btn btn-success" onclick="confirmDelivery(<?php echo $order['id']; ?>)">✓ Mark Delivered</button>
                                            <button class="btn btn-danger" onclick="cancelOrder(<?php echo $order['id']; ?>)">✗ Cancel</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="order-price">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-orders">
                        <h3>No sales yet</h3>
                        <p>List products to start selling!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName, event) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function confirmDelivery(orderId) {
            if (!confirm('Confirm delivery for this order?')) return;
            fetch('confirm_delivery.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'ajax=1&order_id=' + orderId + '&action=delivered'
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    const badge = document.getElementById('status-' + orderId);
                    badge.textContent = 'Delivered';
                    badge.className = 'status-badge status-delivered';
                    document.querySelector('#order-' + orderId + ' .action-buttons')?.remove();
                }
            });
        }

        function cancelOrder(orderId) {
            if (!confirm('Cancel this order?')) return;
            fetch('confirm_delivery.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'ajax=1&order_id=' + orderId + '&action=cancel'
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    const badge = document.getElementById('status-' + orderId);
                    badge.textContent = 'Cancelled';
                    badge.className = 'status-badge status-cancelled';
                    document.querySelector('#order-' + orderId + ' .action-buttons')?.remove();
                }
            });
        }
    </script>
</body>
</html>
<?php
$buyer_stmt->close();
$seller_stmt->close();
?>
