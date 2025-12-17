<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    jsonResponse(false, 'Product ID is required', null, 400);
}

$db = Database::getInstance()->getConnection();

// Get product details
$stmt = $db->prepare("
    SELECT 
        p.*,
        c.name as category_name,
        c.slug as category_slug
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.status = 'active'
");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    jsonResponse(false, 'Product not found', null, 404);
}

// Format product
$product['screenshots'] = json_decode($product['screenshots'] ?? '[]');
$product['price_formatted'] = formatCurrency($product['price']);

// Get related products
$stmt = $db->prepare("
    SELECT id, title, slug, price, short_description, screenshots
    FROM products
    WHERE category_id = ? AND id != ? AND status = 'active'
    LIMIT 4
");
$stmt->execute([$product['category_id'], $id]);
$related = $stmt->fetchAll();

foreach ($related as &$item) {
    $item['screenshots'] = json_decode($item['screenshots'] ?? '[]');
    $item['price_formatted'] = formatCurrency($item['price']);
}

jsonResponse(true, '', [
    'product' => $product,
    'related_products' => $related
]);
