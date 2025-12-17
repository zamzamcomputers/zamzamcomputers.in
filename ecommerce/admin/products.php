<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireAdmin();

$db = Database::getInstance()->getConnection();

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle add/edit/delete product
    // This will be implemented via API calls from JavaScript
}

// Get all products
$stmt = $db->query("
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Dashboard</title>
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
                <li><a href="<?= baseUrl('admin/products.php') ?>" class="active">Products</a></li>
                <li><a href="<?= baseUrl('admin/orders.php') ?>">Orders</a></li>
                <li><a href="<?= baseUrl('admin/users.php') ?>">Users</a></li>
                <li><a href="<?= baseUrl('admin/settings.php') ?>">Settings</a></li>
                <li><a href="<?= baseUrl() ?>">View Site</a></li>
                <li><a href="<?= baseUrl('api/auth/logout.php') ?>">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Products Management</h1>
            <a href="<?= baseUrl('admin/product-form.php') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>

        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="padding: 1rem; text-align: left;">Image</th>
                            <th style="padding: 1rem; text-align: left;">Name</th>
                            <th style="padding: 1rem; text-align: left;">Category</th>
                            <th style="padding: 1rem; text-align: left;">Price</th>
                            <th style="padding: 1rem; text-align: left;">Status</th>
                            <th style="padding: 1rem; text-align: left;">Sales</th>
                            <th style="padding: 1rem; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem;">
                                    <?php if ($product['thumbnail']): ?>
                                        <img src="<?= baseUrl($product['thumbnail']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.25rem;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: var(--border-color); border-radius: 0.25rem; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($product['title']) ?></td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                                <td style="padding: 1rem;"><?= formatCurrency($product['price']) ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge <?= $product['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= htmlspecialchars($product['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;"><?= $product['sales_count'] ?></td>
                                <td style="padding: 1rem;">
                                    <a href="<?= baseUrl('admin/product-form.php?id=' . $product['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteProduct(<?= $product['id'] ?>)" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        async function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product?')) return;
            
            const result = await apiRequest('/products/delete.php', {
                method: 'DELETE',
                body: JSON.stringify({ id })
            });
            
            if (result.success) {
                showToast('Product deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.message, 'error');
            }
        }
    </script>
</body>
</html>
