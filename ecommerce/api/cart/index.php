<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$auth = new Auth();
$db = Database::getInstance()->getConnection();

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get cart items
        requireLogin();
        
        $stmt = $db->prepare("
            SELECT 
                c.*,
                p.title,
                p.slug,
                p.price,
                p.screenshots
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ? AND p.status = 'active'
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $items = $stmt->fetchAll();
        
        $total = 0;
        foreach ($items as &$item) {
            $item['screenshots'] = json_decode($item['screenshots'] ?? '[]');
            $item['price_formatted'] = formatCurrency($item['price']);
            $total += $item['price'];
        }
        
        jsonResponse(true, '', [
            'items' => $items,
            'total' => $total,
            'total_formatted' => formatCurrency($total),
            'count' => count($items)
        ]);
        break;
        
    case 'POST':
        // Add to cart
        requireLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = intval($data['product_id'] ?? 0);
        
        if ($productId === 0) {
            jsonResponse(false, 'Product ID is required');
        }
        
        // Check if product exists and is active
        $stmt = $db->prepare("SELECT id FROM products WHERE id = ? AND status = 'active'");
        $stmt->execute([$productId]);
        
        if (!$stmt->fetch()) {
            jsonResponse(false, 'Product not found');
        }
        
        // Check if already in cart
        $stmt = $db->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $productId]);
        
        if ($stmt->fetch()) {
            jsonResponse(false, 'Product already in cart');
        }
        
        // Check if user already owns this product
        if (userOwnsProduct($_SESSION['user_id'], $productId)) {
            jsonResponse(false, 'You already own this product');
        }
        
        // Add to cart
        $stmt = $db->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $productId])) {
            jsonResponse(true, 'Added to cart successfully');
        } else {
            jsonResponse(false, 'Failed to add to cart');
        }
        break;
        
    case 'DELETE':
        // Remove from cart
        requireLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = intval($data['product_id'] ?? 0);
        
        if ($productId === 0) {
            jsonResponse(false, 'Product ID is required');
        }
        
        $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        if ($stmt->execute([$_SESSION['user_id'], $productId])) {
            jsonResponse(true, 'Removed from cart');
        } else {
            jsonResponse(false, 'Failed to remove from cart');
        }
        break;
        
    default:
        jsonResponse(false, 'Method not allowed', null, 405);
}
