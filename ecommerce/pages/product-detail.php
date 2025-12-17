<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireLogin();

$db = Database::getInstance()->getConnection();
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId === 0) {
    redirect(baseUrl('pages/products.php'));
}

$stmt = $db->prepare("
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.status = 'active'
");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    redirect(baseUrl('pages/products.php'));
}

$screenshots = json_decode($product['screenshots'] ?? '[]');
$userOwns = userOwnsProduct($_SESSION['user_id'], $productId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?> - Digital Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
</head>
<body>
    <nav class="desktop-navbar">
        <div class="container">
            <a href="<?= baseUrl() ?>" class="logo">
                <i class="fas fa-store"></i> Digital Marketplace
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="grid grid-2" style="gap: 2rem;">
            <!-- Product Images -->
            <div>
                <?php if (!empty($screenshots)): ?>
                    <img src="<?= baseUrl($screenshots[0]) ?>" alt="<?= htmlspecialchars($product['title']) ?>" 
                         style="width: 100%; border-radius: 1rem; margin-bottom: 1rem;">
                    
                    <?php if (count($screenshots) > 1): ?>
                        <div style="display: flex; gap: 0.5rem; overflow-x: auto;">
                            <?php foreach (array_slice($screenshots, 1, 4) as $screenshot): ?>
                                <img src="<?= baseUrl($screenshot) ?>" alt="Screenshot" 
                                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.5rem;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div>
                <h1><?= htmlspecialchars($product['title']) ?></h1>
                <p class="text-secondary"><?= htmlspecialchars($product['category_name']) ?></p>
                
                <div class="price mt-3 mb-3" style="font-size: 2rem;">
                    <?= formatCurrency($product['price']) ?>
                </div>
                
                <div class="mb-4">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
                
                <?php if ($product['requirements']): ?>
                    <div class="mb-4">
                        <h3>Requirements</h3>
                        <p><?= nl2br(htmlspecialchars($product['requirements'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($product['file_size']): ?>
                    <p><strong>File Size:</strong> <?= htmlspecialchars($product['file_size']) ?></p>
                <?php endif; ?>
                
                <?php if ($product['version']): ?>
                    <p><strong>Version:</strong> <?= htmlspecialchars($product['version']) ?></p>
                <?php endif; ?>
                
                <div class="mt-4" style="display: flex; gap: 1rem;">
                    <?php if ($userOwns): ?>
                        <a href="<?= baseUrl('pages/orders.php') ?>" class="btn btn-primary" style="flex: 1;">
                            <i class="fas fa-download"></i> View in Orders
                        </a>
                    <?php else: ?>
                        <button onclick="addToCart(<?= $productId ?>)" class="btn btn-outline" style="flex: 1;">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <a href="<?= baseUrl('pages/cart.php') ?>" class="btn btn-primary" style="flex: 1;">
                            <i class="fas fa-shopping-cart"></i> Buy Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
