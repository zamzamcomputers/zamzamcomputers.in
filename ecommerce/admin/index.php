<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireAdmin();

$db = Database::getInstance()->getConnection();

// Get statistics
$stats = [];

$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$stats['users'] = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM products");
$stats['products'] = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM orders WHERE payment_status = 'completed'");
$stats['orders'] = $stmt->fetch()['count'];

$stmt = $db->query("SELECT SUM(final_amount) as total FROM orders WHERE payment_status = 'completed'");
$stats['revenue'] = $stmt->fetch()['total'] ?? 0;

// Recent orders
$stmt = $db->query("
    SELECT o.*, u.name as user_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$recentOrders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Digital Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
</head>
<body>
    <nav class="desktop-navbar">
        <div class="container">
            <a href="<?= baseUrl('admin/index.php') ?>" class="logo">
                <i class="fas fa-shield-alt"></i> Admin Dashboard
            </a>
            <ul class="nav-links">
                <li><a href="<?= baseUrl('admin/index.php') ?>">Dashboard</a></li>
                <li><a href="<?= baseUrl('admin/products.php') ?>">Products</a></li>
                <li><a href="<?= baseUrl('admin/orders.php') ?>">Orders</a></li>
                <li><a href="<?= baseUrl('admin/users.php') ?>">Users</a></li>
                <li><a href="<?= baseUrl('admin/settings.php') ?>">Settings</a></li>
                <li><a href="<?= baseUrl() ?>">View Site</a></li>
                <li><a href="<?= baseUrl('api/auth/logout.php') ?>">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Dashboard Overview</h1>
        
        <!-- Statistics Cards -->
        <div class="grid grid-4 mb-4">
            <div class="card text-center">
                <i class="fas fa-users" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
                <h3><?= $stats['users'] ?></h3>
                <p class="text-secondary">Total Users</p>
            </div>
            
            <div class="card text-center">
                <i class="fas fa-box" style="font-size: 3rem; color: var(--secondary); margin-bottom: 1rem;"></i>
                <h3><?= $stats['products'] ?></h3>
                <p class="text-secondary">Products</p>
            </div>
            
            <div class="card text-center">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--success); margin-bottom: 1rem;"></i>
                <h3><?= $stats['orders'] ?></h3>
                <p class="text-secondary">Orders</p>
            </div>
            
            <div class="card text-center">
                <i class="fas fa-rupee-sign" style="font-size: 3rem; color: var(--accent); margin-bottom: 1rem;"></i>
                <h3><?= formatCurrency($stats['revenue']) ?></h3>
                <p class="text-secondary">Revenue</p>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card">
            <h2 class="mb-3">Recent Orders</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="padding: 1rem; text-align: left;">Order #</th>
                            <th style="padding: 1rem; text-align: left;">Customer</th>
                            <th style="padding: 1rem; text-align: left;">Amount</th>
                            <th style="padding: 1rem; text-align: left;">Status</th>
                            <th style="padding: 1rem; text-align: left;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['order_number']) ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['user_name']) ?></td>
                                <td style="padding: 1rem;"><?= formatCurrency($order['final_amount']) ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge <?= $order['payment_status'] === 'completed' ? 'bg-success' : 'bg-warning' ?>">
                                        <?= htmlspecialchars($order['payment_status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;"><?= timeAgo($order['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
