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
    <title>Shopping Cart - Digital Marketplace</title>
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
        <h1 class="mb-4">Shopping Cart</h1>
        
        <div class="grid grid-2" style="gap: 2rem; align-items: start;">
            <!-- Cart Items -->
            <div>
                <div id="cartItems">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="card" style="position: sticky; top: 100px;">
                <h3 class="mb-3">Order Summary</h3>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span>Subtotal:</span>
                    <strong id="subtotal">₹0.00</strong>
                </div>
                
                <div class="form-group">
                    <label for="couponCode">Coupon Code</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" id="couponCode" class="form-control" placeholder="Enter code">
                        <button onclick="applyCoupon()" class="btn btn-outline">Apply</button>
                    </div>
                </div>
                
                <div id="discountSection" style="display: none; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; color: var(--success);">
                        <span>Discount:</span>
                        <strong id="discount">-₹0.00</strong>
                    </div>
                </div>
                
                <hr>
                
                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; margin-bottom: 1.5rem;">
                    <strong>Total:</strong>
                    <strong id="total" style="color: var(--primary);">₹0.00</strong>
                </div>
                
                <button onclick="proceedToCheckout()" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </button>
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
        <a href="<?= baseUrl('pages/cart.php') ?>" class="nav-item active">
            <i class="fas fa-shopping-cart"></i>
            <span>Cart</span>
        </a>
        <a href="<?= baseUrl('pages/profile.php') ?>" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        let cartData = null;
        let appliedCoupon = null;
        
        async function loadCart() {
            const result = await apiRequest('/cart/index.php');
            
            if (result.success) {
                cartData = result.data;
                displayCart(result.data);
                updateSummary();
            }
        }
        
        function displayCart(data) {
            const container = document.getElementById('cartItems');
            
            if (data.items.length === 0) {
                container.innerHTML = `
                    <div class="card text-center">
                        <i class="fas fa-shopping-cart" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                        <h3>Your cart is empty</h3>
                        <a href="<?= baseUrl('pages/products.php') ?>" class="btn btn-primary mt-3">
                            Browse Products
                        </a>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = data.items.map(item => {
                const image = item.screenshots && item.screenshots[0]
                    ? '<?= baseUrl() ?>/' + item.screenshots[0]
                    : '<?= asset('images/placeholder.jpg') ?>';
                
                return `
                    <div class="card mb-3">
                        <div style="display: flex; gap: 1rem;">
                            <img src="${image}" alt="${item.title}" 
                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.5rem;">
                            <div style="flex: 1;">
                                <h4>${item.title}</h4>
                                <p class="price">${item.price_formatted}</p>
                            </div>
                            <button onclick="removeItem(${item.product_id})" class="btn btn-outline" style="height: fit-content;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        function updateSummary() {
            if (!cartData) return;
            
            const subtotal = cartData.total;
            const discount = appliedCoupon ? appliedCoupon.discount : 0;
            const total = subtotal - discount;
            
            document.getElementById('subtotal').textContent = formatCurrency(subtotal);
            document.getElementById('total').textContent = formatCurrency(total);
            
            if (discount > 0) {
                document.getElementById('discount').textContent = '-' + formatCurrency(discount);
                document.getElementById('discountSection').style.display = 'block';
            } else {
                document.getElementById('discountSection').style.display = 'none';
            }
        }
        
        async function removeItem(productId) {
            const result = await apiRequest('/cart/index.php', {
                method: 'DELETE',
                body: JSON.stringify({ product_id: productId })
            });
            
            if (result.success) {
                showToast('Item removed from cart', 'success');
                loadCart();
            }
        }
        
        async function proceedToCheckout() {
            if (!cartData || cartData.items.length === 0) {
                showToast('Your cart is empty', 'error');
                return;
            }
            
            window.location.href = 'checkout.php';
        }
        
        loadCart();
    </script>
</body>
</html>
