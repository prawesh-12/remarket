<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $category = sanitize($_POST['category']);
    $condition = sanitize($_POST['condition']);
    $user_id = getUserId();
    
    if ($user_id === null) {
        $error = "Session error. Please login again.";
    } elseif (empty($title) || empty($description) || $price <= 0 || empty($category) || empty($condition)) {
        $error = "All fields are required and price must be greater than 0";
    } else {
        $image_url = null;
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = uploadImage($_FILES['product_image']);
            if ($upload_result['success']) {
                $image_url = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        if (empty($error)) {
            $sql = "INSERT INTO products (user_id, title, description, price, category, condition_type, image_url, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'available')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issdsss", $user_id, $title, $description, $price, $category, $condition, $image_url);
            
            if ($stmt->execute()) {
                $success = "Product listed successfully! Redirecting...";
                header("refresh:2;url=home.php");
            } else {
                $error = "Failed to list product: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Product - ReMarket</title>
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
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 0.5rem;
            color: #2B2D42;
        }

        .subtitle {
            text-align: center;
            color: #8D99AE;
            margin-bottom: 2rem;
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

        input, textarea, select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #EDF2F4;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: 0.3s;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #EF233C;
            box-shadow: 0 0 0 3px rgba(239, 35, 60, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .file-input-wrapper {
            position: relative;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            border: 2px dashed #EF233C;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            color: #EF233C;
            background: rgba(239, 35, 60, 0.05);
        }

        .file-input-label:hover {
            background: rgba(239, 35, 60, 0.1);
        }

        .file-name {
            margin-top: 0.5rem;
            color: #EF233C;
            font-size: 0.9rem;
        }

        .image-preview {
            margin-top: 1rem;
            max-width: 300px;
            display: none;
        }

        .image-preview img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #8D99AE;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-link:hover {
            color: #EF233C;
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
            <a href="orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <h1>List Your Product</h1>
        <p class="subtitle">Fill in the details to list your item for sale</p>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Product Title *</label>
                <input type="text" id="title" name="title" required placeholder="e.g., iPhone 12 Pro 128GB">
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required placeholder="Describe your product..."></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price (₹) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required placeholder="0.00">
            </div>

            <div class="form-group">
  <label for="category">Category *</label>
  <select id="category" name="category" required>
    <option value="" selected hidden>Select a category</option>
    <option value="electronics">Electronics</option>
    <option value="furniture">Furniture</option>
    <option value="clothing">Clothing</option>
    <option value="books">Books</option>
    <option value="sports">Sports & Outdoors</option>
    <option value="toys">Toys & Games</option>
    <option value="home">Home & Garden</option>
    <option value="other">Other</option>
  </select>
</div>

<div class="form-group">
  <label for="condition">Condition *</label>
  <select id="condition" name="condition" required>
    <option value="" selected hidden>Select condition</option>
    <option value="new">New</option>
    <option value="like-new">Like New</option>
    <option value="good">Good</option>
    <option value="fair">Fair</option>
  </select>
</div>


            <div class="form-group">
                <label>Product Image (Optional)</label>
                <div class="file-input-wrapper">
                    <label for="product_image" class="file-input-label">
                        <i class="fas fa-camera"></i>&nbsp; Click to upload product image
                    </label>
                    <input type="file" id="product_image" name="product_image" accept="image/*" onchange="previewImage(this)">
                </div>
                <div class="file-name" id="fileName"></div>
                <div class="image-preview" id="imagePreview">
                    <img id="previewImg" src="" alt="Preview">
                </div>
            </div>

            <button type="submit" class="submit-btn">List Product</button>
        </form>

        <a href="home.php" class="back-link">← Back to Home</a>
    </div>

    <script>
        function previewImage(input) {
            const fileName = document.getElementById('fileName');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                fileName.textContent = '📎 ' + input.files[0].name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                fileName.textContent = '';
                imagePreview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
