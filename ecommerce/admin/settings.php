<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireAdmin();

$db = Database::getInstance()->getConnection();

// Get current settings
$settings = getSettings();

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = sanitize($_POST['site_name'] ?? '');
    $site_email = sanitize($_POST['site_email'] ?? '');
    $currency_symbol = sanitize($_POST['currency_symbol'] ?? '₹');
    
    updateSetting('site_name', $site_name);
    updateSetting('site_email', $site_email);
    updateSetting('currency_symbol', $currency_symbol);
    
    $success = "Settings updated successfully!";
    $settings = getSettings(); // Refresh settings
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Dashboard</title>
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
                <li><a href="<?= baseUrl('admin/settings.php') ?>" class="active">Settings</a></li>
                <li><a href="<?= baseUrl() ?>">View Site</a></li>
                <li><a href="<?= baseUrl('api/auth/logout.php') ?>">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Site Settings</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" class="form-control" 
                           value="<?= htmlspecialchars($settings['site_name'] ?? 'Digital Marketplace') ?>" required>
                </div>

                <div class="form-group">
                    <label for="site_email">Site Email</label>
                    <input type="email" id="site_email" name="site_email" class="form-control" 
                           value="<?= htmlspecialchars($settings['site_email'] ?? 'admin@marketplace.com') ?>" required>
                </div>

                <div class="form-group">
                    <label for="currency_symbol">Currency Symbol</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" class="form-control" 
                           value="<?= htmlspecialchars($settings['currency_symbol'] ?? '₹') ?>" required>
                </div>

                <div class="form-group">
                    <label for="payment_gateway">Payment Gateway</label>
                    <select id="payment_gateway" name="payment_gateway" class="form-control">
                        <option value="razorpay" <?= ($settings['payment_gateway'] ?? 'razorpay') === 'razorpay' ? 'selected' : '' ?>>Razorpay</option>
                        <option value="stripe" <?= ($settings['payment_gateway'] ?? '') === 'stripe' ? 'selected' : '' ?>>Stripe</option>
                        <option value="paypal" <?= ($settings['payment_gateway'] ?? '') === 'paypal' ? 'selected' : '' ?>>PayPal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="items_per_page">Items Per Page</label>
                    <input type="number" id="items_per_page" name="items_per_page" class="form-control" 
                           value="<?= htmlspecialchars($settings['items_per_page'] ?? '12') ?>" min="1" max="100">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </form>
        </div>

        <div class="card mt-4">
            <h3 class="mb-3">System Information</h3>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 0.5rem;"><strong>PHP Version:</strong></td>
                    <td style="padding: 0.5rem;"><?= phpversion() ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem;"><strong>Database:</strong></td>
                    <td style="padding: 0.5rem;">MySQL/MariaDB</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem;"><strong>Application Version:</strong></td>
                    <td style="padding: 0.5rem;">1.0.0</td>
                </tr>
            </table>
        </div>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
