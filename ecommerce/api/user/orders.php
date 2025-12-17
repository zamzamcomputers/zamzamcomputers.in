<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

requireLogin();

$db = Database::getInstance()->getConnection();

// Get user's orders
$stmt = $db->prepare("
    SELECT 
        o.*,
        COUNT(oi.id) as items_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// Format orders
foreach ($orders as &$order) {
    $order['total_amount_formatted'] = formatCurrency($order['total_amount']);
    $order['final_amount_formatted'] = formatCurrency($order['final_amount']);
    $order['created_at_formatted'] = timeAgo($order['created_at']);
    
    // Get order items
    $stmt = $db->prepare("
        SELECT 
            oi.*,
            p.screenshots,
            d.download_token,
            d.download_count,
            d.max_downloads,
            d.expires_at
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        LEFT JOIN downloads d ON d.product_id = oi.product_id AND d.order_id = oi.order_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order['id']]);
    $order['items'] = $stmt->fetchAll();
    
    foreach ($order['items'] as &$item) {
        $item['screenshots'] = json_decode($item['screenshots'] ?? '[]');
        $item['price_formatted'] = formatCurrency($item['price']);
    }
}

jsonResponse(true, '', ['orders' => $orders]);
