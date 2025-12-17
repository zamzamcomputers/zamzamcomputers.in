<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireAdmin();

$db = Database::getInstance()->getConnection();

// Get all orders with user information
$stmt = $db->query("
    SELECT o.*, u.name as user_name, u.email as user_email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Dashboard</title>
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
                <li><a href="<?= baseUrl('admin/orders.php') ?>" class="active">Orders</a></li>
                <li><a href="<?= baseUrl('admin/users.php') ?>">Users</a></li>
                <li><a href="<?= baseUrl('admin/settings.php') ?>">Settings</a></li>
                <li><a href="<?= baseUrl() ?>">View Site</a></li>
                <li><a href="<?= baseUrl('api/auth/logout.php') ?>">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Orders Management</h1>

        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="padding: 1rem; text-align: left;">Order #</th>
                            <th style="padding: 1rem; text-align: left;">Customer</th>
                            <th style="padding: 1rem; text-align: left;">Email</th>
                            <th style="padding: 1rem; text-align: left;">Amount</th>
                            <th style="padding: 1rem; text-align: left;">Payment Status</th>
                            <th style="padding: 1rem; text-align: left;">Date</th>
                            <th style="padding: 1rem; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem;">
                                    <strong><?= htmlspecialchars($order['order_number']) ?></strong>
                                </td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['user_name']) ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['user_email']) ?></td>
                                <td style="padding: 1rem;"><?= formatCurrency($order['final_amount']) ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge <?= $order['payment_status'] === 'completed' ? 'bg-success' : ($order['payment_status'] === 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                                        <?= htmlspecialchars($order['payment_status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;"><?= timeAgo($order['created_at']) ?></td>
                                <td style="padding: 1rem;">
                                    <a href="<?= baseUrl('admin/order-details.php?id=' . $order['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
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
