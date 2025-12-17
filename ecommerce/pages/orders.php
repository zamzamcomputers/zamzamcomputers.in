<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Digital Marketplace</title>
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
        <h1 class="mb-4">My Orders & Downloads</h1>
        
        <div id="ordersContainer">
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
        </div>
    </div>

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
        async function loadOrders() {
            const result = await apiRequest('/user/orders.php');
            
            if (result.success) {
                displayOrders(result.data.orders);
            }
        }
        
        function displayOrders(orders) {
            const container = document.getElementById('ordersContainer');
            
            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="card text-center">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                        <h3>No orders yet</h3>
                        <a href="<?= baseUrl('pages/products.php') ?>" class="btn btn-primary mt-3">
                            Start Shopping
                        </a>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = orders.map(order => `
                <div class="card mb-4">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <div>
                            <h3>Order #${order.order_number}</h3>
                            <p class="text-secondary">${order.created_at_formatted}</p>
                        </div>
                        <div>
                            <span class="badge ${order.payment_status === 'completed' ? 'bg-success' : 'bg-warning'}">
                                ${order.payment_status}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        ${order.items.map(item => `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-top: 1px solid var(--border-color);">
                                <div>
                                    <strong>${item.product_title}</strong>
                                    <p class="text-secondary">${item.price_formatted}</p>
                                </div>
                                ${item.download_token && order.payment_status === 'completed' ? `
                                    <a href="<?= baseUrl('api/user/download.php') ?>?token=${item.download_token}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download
                                        <small>(${item.download_count}/${item.max_downloads})</small>
                                    </a>
                                ` : ''}
                            </div>
                        `).join('')}
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                        <strong>Total:</strong>
                        <strong style="color: var(--primary); font-size: 1.25rem;">${order.final_amount_formatted}</strong>
                    </div>
                </div>
            `).join('');
        }
        
        loadOrders();
    </script>
</body>
</html>
