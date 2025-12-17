<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method', null, 405);
}

requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$couponCode = sanitize($data['coupon_code'] ?? '');
$paymentMethod = sanitize($data['payment_method'] ?? 'razorpay');

$db = Database::getInstance()->getConnection();

try {
    $db->beginTransaction();
    
    // Get cart items
    $stmt = $db->prepare("
        SELECT c.*, p.title, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ? AND p.status = 'active'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll();
    
    if (empty($cartItems)) {
        $db->rollBack();
        jsonResponse(false, 'Cart is empty');
    }
    
    // Calculate total
    $totalAmount = 0;
    foreach ($cartItems as $item) {
        $totalAmount += $item['price'];
    }
    
    // Apply coupon if provided
    $discountAmount = 0;
    $coupon = null;
    
    if (!empty($couponCode)) {
        $coupon = validateCoupon($couponCode, $totalAmount);
        
        if (!$coupon) {
            $db->rollBack();
            jsonResponse(false, 'Invalid or expired coupon code');
        }
        
        $discountAmount = calculateDiscount($coupon, $totalAmount);
    }
    
    $finalAmount = $totalAmount - $discountAmount;
    
    // Create order
    $orderNumber = generateOrderNumber();
    
    $stmt = $db->prepare("
        INSERT INTO orders (order_number, user_id, total_amount, discount_amount, final_amount, coupon_code, payment_method, payment_status, order_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 'pending')
    ");
    $stmt->execute([
        $orderNumber,
        $_SESSION['user_id'],
        $totalAmount,
        $discountAmount,
        $finalAmount,
        $couponCode,
        $paymentMethod
    ]);
    
    $orderId = $db->lastInsertId();
    
    // Add order items
    $stmt = $db->prepare("
        INSERT INTO order_items (order_id, product_id, product_title, price)
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($cartItems as $item) {
        $stmt->execute([
            $orderId,
            $item['product_id'],
            $item['title'],
            $item['price']
        ]);
    }
    
    // Update coupon usage
    if ($coupon) {
        $stmt = $db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
        $stmt->execute([$coupon['id']]);
    }
    
    // Clear cart
    $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    $db->commit();
    
    // Get payment gateway settings
    $settings = getSettings();
    $gateway = $settings['payment_gateway'] ?? 'razorpay';
    
    // Return order details for payment
    jsonResponse(true, 'Order created successfully', [
        'order_id' => $orderId,
        'order_number' => $orderNumber,
        'amount' => $finalAmount,
        'currency' => $settings['currency'] ?? 'INR',
        'payment_gateway' => $gateway,
        'gateway_key' => $settings[$gateway . '_key_id'] ?? '',
    ]);
    
} catch (Exception $e) {
    $db->rollBack();
    jsonResponse(false, 'Failed to create order: ' . $e->getMessage());
}
