<?php
// Database configuration
$host = 'localhost';
$dbname = 'e-com'; // Updated to match database.sql
$username = 'root';
$password = ''; // Change this to your database password if needed

// Create connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Don't expose error details to users
    error_log("Database connection error: " . $e->getMessage());
    die("Error: Could not connect to database. Please contact the administrator.");
}

// Start session with secure settings
session_start([
    'cookie_httponly' => true,     // Prevent JavaScript access to session cookie
    'cookie_secure' => isset($_SERVER['HTTPS']), // Use secure cookies if HTTPS
    'cookie_samesite' => 'Strict'  // CSRF protection
]);

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to verify CSRF token
function verify_csrf_token() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error: Invalid form submission. Please try again.");
    }
}

// Function to safely output text
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Store name constant
define('STORE_NAME', 'Simple E-commerce Store');