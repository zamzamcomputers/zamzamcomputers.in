<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();

// Get query parameters
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'latest';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$where = ["p.status = 'active'"];
$params = [];

if ($category > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
}

if (!empty($search)) {
    $where[] = "(p.title LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = implode(' AND ', $where);

// Determine sort order
$orderBy = match($sort) {
    'price_low' => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'popular' => 'p.downloads_count DESC',
    default => 'p.created_at DESC'
};

// Get total count
$countSql = "SELECT COUNT(*) as total FROM products p WHERE $whereClause";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetch()['total'];

// Get products
$sql = "
    SELECT 
        p.*,
        c.name as category_name,
        c.slug as category_slug
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT $limit OFFSET $offset
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Format products
foreach ($products as &$product) {
    $product['screenshots'] = json_decode($product['screenshots'] ?? '[]');
    $product['price_formatted'] = formatCurrency($product['price']);
}

jsonResponse(true, '', [
    'products' => $products,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => ceil($total / $limit),
        'total_items' => $total,
        'per_page' => $limit
    ]
]);
