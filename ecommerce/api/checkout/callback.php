<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method', null, 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = intval($data['order_id'] ?? 0);
$transactionId = sanitize($data['transaction_id'] ?? '');
$paymentStatus = sanitize($data['payment_status'] ?? 'completed');

if ($orderId === 0 || empty($transactionId)) {
    jsonResponse(false, 'Order ID and transaction ID are required');
}

$db = Database::getInstance()->getConnection();

try {
    $db->beginTransaction();
    
    // Update order status
    $stmt = $db->prepare("
        UPDATE orders 
        SET payment_status = ?, transaction_id = ?, order_status = 'confirmed'
        WHERE id = ?
    ");
    $stmt->execute([$paymentStatus, $transactionId, $orderId]);
    
    if ($paymentStatus === 'completed') {
        // Get order items
        $stmt = $db->prepare("SELECT product_id FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();
        
        // Get order details
        $stmt = $db->prepare("SELECT user_id FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        // Create download entries
        $settings = getSettings();
        $maxDownloads = intval($settings['max_downloads_per_product'] ?? 5);
        $expiryDays = intval($settings['download_expiry_days'] ?? 365);
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$expiryDays days"));
        
        $stmt = $db->prepare("
            INSERT INTO downloads (user_id, product_id, order_id, download_token, max_downloads, expires_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($items as $item) {
            $token = generateDownloadToken();
            $stmt->execute([
                $order['user_id'],
                $item['product_id'],
                $orderId,
                $token,
                $maxDownloads,
                $expiresAt
            ]);
            
            // Update product download count
            $updateStmt = $db->prepare("UPDATE products SET downloads_count = downloads_count + 1 WHERE id = ?");
            $updateStmt->execute([$item['product_id']]);
        }
    }
    
    $db->commit();
    jsonResponse(true, 'Payment processed successfully');
    
} catch (Exception $e) {
    $db->rollBack();
    jsonResponse(false, 'Failed to process payment: ' . $e->getMessage());
}
