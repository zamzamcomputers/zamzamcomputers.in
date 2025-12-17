<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireLogin();

$user = $auth->getCurrentUser();
$db = Database::getInstance()->getConnection();

// Get active tab from URL
$activeTab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Digital Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        .profile-sidebar {
            background: var(--bg-secondary);
            border-radius: 1rem;
            padding: 1.5rem;
            position: sticky;
            top: 100px;
        }
        
        .profile-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .profile-menu li {
            margin-bottom: 0.5rem;
        }
        
        .profile-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .profile-menu a:hover {
            background: var(--bg-primary);
            color: var(--primary);
        }
        
        .profile-menu a.active {
            background: var(--gradient-primary);
            color: white;
        }
        
        .profile-menu i {
            font-size: 1.25rem;
            width: 24px;
        }
        
        .profile-content {
            background: var(--bg-primary);
            border-radius: 1rem;
            padding: 2rem;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 1rem;
        }
        
        .order-card {
            background: var(--bg-secondary);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
        }
        
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .badge.bg-success { background: var(--success); color: white; }
        .badge.bg-warning { background: var(--warning); color: white; }
        .badge.bg-danger { background: var(--danger); color: white; }
        
        @media (max-width: 768px) {
            .profile-sidebar {
                position: static;
                margin-bottom: 1rem;
            }
            
            .profile-menu {
                display: flex;
                overflow-x: auto;
                gap: 0.5rem;
            }
            
            .profile-menu li {
                margin-bottom: 0;
            }
            
            .profile-menu a {
                white-space: nowrap;
                padding: 0.75rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Desktop Navbar -->
    <nav class="desktop-navbar">
        <div class="container">
            <a href="<?= baseUrl() ?>" class="logo">
                <i class="fas fa-store"></i> Digital Marketplace
            </a>
            <ul class="nav-links">
                <li><a href="<?= baseUrl() ?>">Home</a></li>
                <li><a href="<?= baseUrl('pages/products.php') ?>">Products</a></li>
                <li><a href="<?= baseUrl('pages/cart.php') ?>">Cart</a></li>
                <li><a href="<?= baseUrl('pages/profile.php') ?>" class="active">Profile</a></li>
                <li>
                    <button class="theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="grid grid-4" style="gap: 2rem;">
            <!-- Sidebar Navigation -->
            <div>
                <div class="profile-sidebar">
                    <!-- User Info -->
                    <div class="text-center mb-4">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <h3><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="text-secondary"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <ul class="profile-menu">
                        <li>
                            <a href="?tab=profile" class="<?= $activeTab === 'profile' ? 'active' : '' ?>">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="?tab=orders" class="<?= $activeTab === 'orders' ? 'active' : '' ?>">
                                <i class="fas fa-shopping-bag"></i>
                                <span>My Orders</span>
                            </a>
                        </li>
                        <li>
                            <a href="?tab=downloads" class="<?= $activeTab === 'downloads' ? 'active' : '' ?>">
                                <i class="fas fa-download"></i>
                                <span>Downloads</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= baseUrl('api/auth/logout.php') ?>" onclick="return confirm('Are you sure you want to logout?');">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div style="grid-column: span 3;">
                <div class="profile-content">
                    <?php if ($activeTab === 'profile'): ?>
                        <!-- Profile Tab -->
                        <h2 class="mb-4">Profile Settings</h2>
                        
                        <form id="profileForm">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </form>
                        
                        <hr style="margin: 2rem 0;">
                        
                        <h3 class="mb-3">Change Password</h3>
                        <form id="passwordForm">
                            <div class="form-group">
                                <label for="currentPassword">Current Password</label>
                                <input type="password" id="currentPassword" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="newPassword">New Password</label>
                                <input type="password" id="newPassword" class="form-control" required minlength="6">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmPassword">Confirm New Password</label>
                                <input type="password" id="confirmPassword" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </form>
                        
                    <?php elseif ($activeTab === 'orders'): ?>
                        <!-- Orders Tab -->
                        <h2 class="mb-4">My Orders</h2>
                        <div id="ordersContainer">
                            <div class="loading-spinner">
                                <div class="spinner"></div>
                            </div>
                        </div>
                        
                    <?php elseif ($activeTab === 'downloads'): ?>
                        <!-- Downloads Tab -->
                        <h2 class="mb-4">My Downloads</h2>
                        <div id="downloadsContainer">
                            <div class="loading-spinner">
                                <div class="spinner"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="<?= baseUrl() ?>" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?= baseUrl('pages/products.php') ?>" class="nav-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Products</span>
        </a>
        <a href="<?= baseUrl('pages/cart.php') ?>" class="nav-item">
            <i class="fas fa-shopping-cart"></i>
            <span>Cart</span>
        </a>
        <a href="<?= baseUrl('pages/profile.php') ?>" class="nav-item active">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        // Profile Update
        document.getElementById('profileForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            
            const result = await apiRequest('/user/profile.php', {
                method: 'PUT',
                body: JSON.stringify({ name, email })
            });
            
            if (result.success) {
                showToast('Profile updated successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.message, 'error');
            }
        });
        
        // Password Change
        document.getElementById('passwordForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                showToast('Passwords do not match', 'error');
                return;
            }
            
            const result = await apiRequest('/user/profile.php', {
                method: 'PUT',
                body: JSON.stringify({ 
                    current_password: currentPassword,
                    new_password: newPassword 
                })
            });
            
            if (result.success) {
                showToast('Password changed successfully!', 'success');
                document.getElementById('passwordForm').reset();
            } else {
                showToast(result.message, 'error');
            }
        });
        
        // Load Orders
        async function loadOrders() {
            const result = await apiRequest('/user/orders.php');
            
            if (result.success) {
                displayOrders(result.data.orders);
            }
        }
        
        function displayOrders(orders) {
            const container = document.getElementById('ordersContainer');
            if (!container) return;
            
            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="text-center" style="padding: 3rem;">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                        <h3>No orders yet</h3>
                        <p class="text-secondary">Start shopping to see your orders here</p>
                        <a href="<?= baseUrl('pages/products.php') ?>" class="btn btn-primary mt-3">
                            Browse Products
                        </a>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = orders.map(order => `
                <div class="order-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <div>
                            <h4>Order #${order.order_number}</h4>
                            <p class="text-secondary" style="margin: 0;">${order.created_at_formatted}</p>
                        </div>
                        <span class="badge ${order.payment_status === 'completed' ? 'bg-success' : order.payment_status === 'pending' ? 'bg-warning' : 'bg-danger'}">
                            ${order.payment_status}
                        </span>
                    </div>
                    
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        ${order.items.map(item => `
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                                <span>${item.product_title}</span>
                                <span>${item.price_formatted}</span>
                            </div>
                        `).join('')}
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                        <strong>Total: ${order.final_amount_formatted}</strong>
                        ${order.payment_status === 'completed' ? `
                            <a href="?tab=downloads" class="btn btn-primary">
                                <i class="fas fa-download"></i> View Downloads
                            </a>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        }
        
        // Load Downloads
        async function loadDownloads() {
            const result = await apiRequest('/user/orders.php');
            
            if (result.success) {
                displayDownloads(result.data.orders);
            }
        }
        
        function displayDownloads(orders) {
            const container = document.getElementById('downloadsContainer');
            if (!container) return;
            
            // Extract all downloadable items
            const downloads = [];
            orders.forEach(order => {
                if (order.payment_status === 'completed') {
                    order.items.forEach(item => {
                        if (item.download_token) {
                            downloads.push({
                                ...item,
                                order_number: order.order_number,
                                order_date: order.created_at_formatted
                            });
                        }
                    });
                }
            });
            
            if (downloads.length === 0) {
                container.innerHTML = `
                    <div class="text-center" style="padding: 3rem;">
                        <i class="fas fa-download" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                        <h3>No downloads available</h3>
                        <p class="text-secondary">Purchase products to access downloads</p>
                        <a href="<?= baseUrl('pages/products.php') ?>" class="btn btn-primary mt-3">
                            Browse Products
                        </a>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = downloads.map(item => `
                <div class="order-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h4>${item.product_title}</h4>
                            <p class="text-secondary" style="margin: 0.5rem 0;">
                                Order #${item.order_number} • ${item.order_date}
                            </p>
                            <p class="text-secondary" style="margin: 0;">
                                Downloads: ${item.download_count} / ${item.max_downloads}
                                ${item.expires_at ? `• Expires: ${new Date(item.expires_at).toLocaleDateString()}` : ''}
                            </p>
                        </div>
                        <a href="<?= baseUrl('api/user/download.php') ?>?token=${item.download_token}" 
                           class="btn btn-primary"
                           ${item.download_count >= item.max_downloads ? 'disabled' : ''}>
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                    
                    ${item.download_count >= item.max_downloads ? `
                        <div style="background: var(--bg-tertiary); padding: 1rem; border-radius: 0.5rem;">
                            <i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i>
                            Download limit reached. Contact support if you need more downloads.
                        </div>
                    ` : ''}
                </div>
            `).join('');
        }
        
        // Logout Function
        async function logout() {
            if (confirm('Are you sure you want to logout?')) {
                const result = await apiRequest('/auth/logout.php', { method: 'POST' });
                if (result.success) {
                    showToast('Logged out successfully', 'success');
                    setTimeout(() => {
                        window.location.href = '<?= baseUrl() ?>';
                    }, 1000);
                }
            }
        }
        
        // Load data based on active tab
        const activeTab = '<?= $activeTab ?>';
        if (activeTab === 'orders') {
            loadOrders();
        } else if (activeTab === 'downloads') {
            loadDownloads();
        }
    </script>
</body>
</html>
