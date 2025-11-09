<?php
// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        die('.env file is missing. Please copy .env.example to .env and configure your settings.');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

loadEnv(__DIR__ . '/.env');

// Database Configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'remarket');

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL configuration
define('BASE_URL', 'https://remarket-sales.infinityfree.me/');

// Upload directory
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// Create upload directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getUserId() {
    if (!isLoggedIn()) {
        return null;
    }
    return intval($_SESSION['user_id']);
}

function redirect($page) {
    header("Location: " . BASE_URL . $page);
    exit();
}

function sanitize($data) {
    global $conn;
    if ($data === null) {
        return '';
    }
    return mysqli_real_escape_string($conn, trim($data));
}

// Function to handle file uploads
function uploadImage($file) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed with error code: ' . $file['error']];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    
    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF allowed'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
    
    return ['success' => true, 'filename' => $filename, 'url' => UPLOAD_URL . $filename];
}
?>