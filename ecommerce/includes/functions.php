<?php
// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generate slug from string
function generateSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    return $slug;
}

// Format currency
function formatCurrency($amount) {
    $settings = getSettings();
    $symbol = $settings['currency_symbol'] ?? '₹';
    return $symbol . number_format($amount, 2);
}

// Get settings from database
function getSettings() {
    static $settings = null;
    
    if ($settings === null) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings;
}

// Update setting
function updateSetting($key, $value) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

// Generate order number
function generateOrderNumber() {
    return 'ORD-' . strtoupper(uniqid());
}

// Generate download token
function generateDownloadToken() {
    return bin2hex(random_bytes(32));
}

// Check if user owns product
function userOwnsProduct($userId, $productId) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'completed'
    ");
    $stmt->execute([$userId, $productId]);
    $result = $stmt->fetch();
    return $result['count'] > 0;
}

// Send JSON response
function jsonResponse($success, $message = '', $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

// Get base URL
function baseUrl($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

// Get asset URL
function asset($path) {
    return baseUrl('assets/' . ltrim($path, '/'));
}

// Time ago function
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return date('M d, Y', $timestamp);
}

// File size format
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Validate coupon
function validateCoupon($code, $totalAmount) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT * FROM coupons 
        WHERE code = ? 
        AND status = 'active' 
        AND (expires_at IS NULL OR expires_at > NOW())
        AND (usage_limit = 0 OR used_count < usage_limit)
        AND min_purchase <= ?
    ");
    $stmt->execute([$code, $totalAmount]);
    return $stmt->fetch();
}

// Calculate discount
function calculateDiscount($coupon, $amount) {
    if (!$coupon) return 0;
    
    if ($coupon['type'] === 'flat') {
        return min($coupon['value'], $amount);
    } else {
        $discount = ($amount * $coupon['value']) / 100;
        if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
            $discount = $coupon['max_discount'];
        }
        return $discount;
    }
}

// Require login
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect(baseUrl('pages/login.php'));
        } else {
            jsonResponse(false, 'Please login to continue', null, 401);
        }
    }
}

// Require admin
function requireAdmin() {
    requireLogin();
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect(baseUrl('index.php'));
        } else {
            jsonResponse(false, 'Access denied', null, 403);
        }
    }
}
