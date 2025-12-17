<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireAdmin();

$db = Database::getInstance()->getConnection();

// Get all users
$stmt = $db->query("
    SELECT id, name, email, role, status, created_at,
           (SELECT COUNT(*) FROM orders WHERE user_id = users.id AND payment_status = 'completed') as order_count,
           (SELECT SUM(final_amount) FROM orders WHERE user_id = users.id AND payment_status = 'completed') as total_spent
    FROM users
    ORDER BY created_at DESC
");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Dashboard</title>
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
                <li><a href="<?= baseUrl('admin/users.php') ?>" class="active">Users</a></li>
                <li><a href="<?= baseUrl('admin/settings.php') ?>">Settings</a></li>
                <li><a href="<?= baseUrl() ?>">View Site</a></li>
                <li><a href="<?= baseUrl('api/auth/logout.php') ?>">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Users Management</h1>

        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="padding: 1rem; text-align: left;">Name</th>
                            <th style="padding: 1rem; text-align: left;">Email</th>
                            <th style="padding: 1rem; text-align: left;">Role</th>
                            <th style="padding: 1rem; text-align: left;">Status</th>
                            <th style="padding: 1rem; text-align: left;">Orders</th>
                            <th style="padding: 1rem; text-align: left;">Total Spent</th>
                            <th style="padding: 1rem; text-align: left;">Joined</th>
                            <th style="padding: 1rem; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem;">
                                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                                </td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($user['email']) ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge <?= $user['role'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= htmlspecialchars($user['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;"><?= $user['order_count'] ?></td>
                                <td style="padding: 1rem;"><?= formatCurrency($user['total_spent'] ?? 0) ?></td>
                                <td style="padding: 1rem;"><?= timeAgo($user['created_at']) ?></td>
                                <td style="padding: 1rem;">
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <button onclick="toggleUserStatus(<?= $user['id'] ?>, '<?= $user['status'] ?>')" class="btn btn-sm btn-warning">
                                            <i class="fas fa-ban"></i> <?= $user['status'] === 'active' ? 'Block' : 'Unblock' ?>
                                        </button>
                                    <?php endif; ?>
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
        async function toggleUserStatus(userId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'blocked' : 'active';
            const action = newStatus === 'blocked' ? 'block' : 'unblock';
            
            if (!confirm(`Are you sure you want to ${action} this user?`)) return;
            
            const result = await apiRequest('/admin/toggle-user-status.php', {
                method: 'POST',
                body: JSON.stringify({ user_id: userId, status: newStatus })
            });
            
            if (result.success) {
                showToast(`User ${action}ed successfully`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.message, 'error');
            }
        }
    </script>
</body>
</html>
