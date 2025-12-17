<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_db');

// Application Configuration
define('APP_NAME', 'Digital Marketplace');
define('APP_URL', 'http://localhost/ecommerce');
define('BASE_PATH', dirname(__DIR__));

// Upload Directories
define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('PRODUCTS_DIR', UPLOAD_DIR . '/products');
define('SCREENSHOTS_DIR', UPLOAD_DIR . '/screenshots');

// Session Configuration
define('SESSION_LIFETIME', 86400); // 24 hours

// Security
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 10);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configure session cookies
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_path', '/');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
