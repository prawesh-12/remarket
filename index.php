<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReMarket - Buy & Sell Pre-loved Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2B2D42;
            background: linear-gradient(135deg, #2B2D42 0%, #8D99AE 100%);
            line-height: 1.6;
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
            margin: 1.5rem auto;
            max-width: 1200px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #EF233C;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-buttons a {
            text-decoration: none;
            color: #2B2D42;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            margin-left: 0.8rem;
            border-radius: 10px;
            border: 2px solid #EF233C;
            transition: all 0.3s;
        }

        .nav-buttons a:hover {
            background: #EF233C;
            color: white;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 5% 80px;
            background: white;
            border-radius: 30px;
            max-width: 1200px;
            margin: 2rem auto;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .hero h1 {
            font-size: 3rem;
            color: #2B2D42;
            margin-bottom: 1rem;
        }

        .hero p {
            color: #8D99AE;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .cta-button {
            background: #EF233C;
            color: white;
            padding: 1rem 2.5rem;
            text-decoration: none;
            font-weight: 700;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(239,35,60,0.3);
            transition: all 0.3s;
        }

        .cta-button:hover {
            background: #d91e35;
            transform: translateY(-3px);
        }

        /* Features Section */
        .features {
            max-width: 1200px;
            margin: 3rem auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            text-align: center;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 3rem;
            color: #EF233C;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: #2B2D42;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            color: #555;
            font-size: 0.95rem;
        }

        /* Stats Section */
        .stats {
            background: linear-gradient(135deg, #2B2D42 0%, #8D99AE 100%);
            color: white;
            padding: 60px 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item h2 {
            font-size: 2.5rem;
            color: #EF233C;
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            font-size: 1rem;
            opacity: 0.9;
        }

        footer {
            text-align: center;
            color: white;
            padding: 2rem 0;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            .cta-button {
                padding: 0.8rem 2rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.php" class="logo"><i class="fas fa-recycle"></i> ReMarket</a>
        <div class="nav-buttons">
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Give Your Products a Second Life!</h1>
        <p>Buy and sell pre-loved items easily, safely, and sustainably.</p>
        <a href="signup.php" class="cta-button">Get Started Today</a>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-store"></i></div>
            <h3>Buy & Sell Easily</h3>
            <p>List your items or find great deals with just a few clicks.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-leaf"></i></div>
            <h3>Eco-Friendly</h3>
            <p>Join a sustainable community that gives items a second chance.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-lock"></i></div>
            <h3>Safe Transactions</h3>
            <p>Your security is our priority with trusted user verification.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-box"></i></div>
            <h3>Track Orders</h3>
            <p>Stay informed with real-time updates on your orders and sales.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-users"></i></div>
            <h3>Community Support</h3>
            <p>Connect with thousands of active users sharing a common goal.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <h3>Save & Earn</h3>
            <p>Sell unused items and make extra cash while shopping smart.</p>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stat-item">
            <h2>10K+</h2>
            <p>Active Users</p>
        </div>
        <div class="stat-item">
            <h2>50K+</h2>
            <p>Items Sold</p>
        </div>
        <div class="stat-item">
            <h2>95%</h2>
            <p>Satisfaction Rate</p>
        </div>
        <div class="stat-item">
            <h2>24/7</h2>
            <p>Support Available</p>
        </div>
    </section>

    <footer>
        &copy; <?php echo date('Y'); ?> ReMarket. All rights reserved.  
        <br>Making the world more sustainable, one item at a time.
    </footer>

</body>
</html>
