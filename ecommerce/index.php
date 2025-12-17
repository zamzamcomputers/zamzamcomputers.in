<?php
require_once 'config/config.php';
require_once 'includes/Database.php';
require_once 'includes/Auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$db = Database::getInstance()->getConnection();

// Get featured products
$stmt = $db->query("
    SELECT id, title, slug, price, short_description, screenshots
    FROM products
    WHERE status = 'active' AND featured = 1
    LIMIT 6
");
$featuredProducts = $stmt->fetchAll();

// Get categories
$stmt = $db->query("SELECT * FROM categories LIMIT 6");
$categories = $stmt->fetchAll();

$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $settings['site_name'] ?? 'Digital Marketplace' ?></title>
    <meta name="description" content="<?= $settings['site_description'] ?? 'Premium Digital Products' ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- MDBootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
</head>
<body>
    <!-- Desktop Navbar -->
    <nav class="desktop-navbar">
        <div class="container">
            <a href="<?= baseUrl() ?>" class="logo">
                <i class="fas fa-store"></i> <?= $settings['site_name'] ?? 'Digital Marketplace' ?>
            </a>
            
            <ul class="nav-links">
                <li><a href="<?= baseUrl() ?>">Home</a></li>
                <li><a href="<?= baseUrl('pages/products.php') ?>">Products</a></li>
                <li><a href="<?= baseUrl('pages/contact.php') ?>">Contact</a></li>
                <?php if ($auth->isLoggedIn()): ?>
                    <li><a href="<?= baseUrl('pages/profile.php') ?>">Profile</a></li>
                    <li><a href="<?= baseUrl('pages/cart.php') ?>">
                        Cart <span class="cart-count" style="display:none;">0</span>
                    </a></li>
                    <?php if ($auth->isAdmin()): ?>
                        <li><a href="<?= baseUrl('admin/index.php') ?>">Admin</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?= baseUrl('pages/login.php') ?>">Login</a></li>
                    <li><a href="<?= baseUrl('pages/signup.php') ?>">Sign Up</a></li>
                <?php endif; ?>
                <li>
                    <button class="theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Hero Section -->
        <section class="hero fade-in">
            <h1>Welcome to Digital Marketplace</h1>
            <p>Discover premium digital products for your projects</p>
            <a href="<?= baseUrl('pages/products.php') ?>" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Explore Products
            </a>
        </section>

        <!-- Featured Products -->
        <section class="mt-4">
            <h2 class="text-center mb-3">Featured Products</h2>
            <div class="grid grid-3">
                <?php foreach ($featuredProducts as $product): 
                    $screenshots = json_decode($product['screenshots'] ?? '[]');
                    $image = !empty($screenshots) ? $screenshots[0] : 'assets/images/placeholder.jpg';
                ?>
                    <div class="card product-card fade-in">
                        <img src="<?= baseUrl($image) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                        <h3><?= htmlspecialchars($product['title']) ?></h3>
                        <p class="text-secondary"><?= htmlspecialchars($product['short_description'] ?? '') ?></p>
                        <div class="price"><?= formatCurrency($product['price']) ?></div>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="<?= baseUrl('pages/product-detail.php?id=' . $product['id']) ?>" class="btn btn-primary" style="flex: 1;">
                                View Details
                            </a>
                            <?php if ($auth->isLoggedIn()): ?>
                                <button onclick="addToCart(<?= $product['id'] ?>)" class="btn btn-outline">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Categories -->
        <section class="mt-4">
            <h2 class="text-center mb-3">Browse Categories</h2>
            <div class="grid grid-3">
                <?php foreach ($categories as $category): ?>
                    <a href="<?= baseUrl('pages/products.php?category=' . $category['id']) ?>" class="card text-center" style="text-decoration: none;">
                        <i class="<?= $category['icon'] ?? 'fas fa-folder' ?>" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                        <p class="text-secondary"><?= htmlspecialchars($category['description'] ?? '') ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="mt-4">
            <h2 class="text-center mb-3">What Our Customers Say</h2>
            <div class="grid grid-3">
                <div class="card fade-in">
                    <p>"Amazing quality products! Highly recommended."</p>
                    <strong>- John Doe</strong>
                </div>
                <div class="card fade-in">
                    <p>"Fast delivery and excellent support."</p>
                    <strong>- Jane Smith</strong>
                </div>
                <div class="card fade-in">
                    <p>"Best digital marketplace I've used!"</p>
                    <strong>- Mike Johnson</strong>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="mt-4 mb-4">
            <h2 class="text-center mb-3">Frequently Asked Questions</h2>
            <div class="card">
                <details class="mb-2">
                    <summary style="cursor: pointer; font-weight: 600; padding: 1rem;">How do I download my products?</summary>
                    <p style="padding: 0 1rem 1rem;">After purchase, you can download your products from your orders page.</p>
                </details>
                <details class="mb-2">
                    <summary style="cursor: pointer; font-weight: 600; padding: 1rem;">What payment methods do you accept?</summary>
                    <p style="padding: 0 1rem 1rem;">We accept all major payment methods including credit cards, UPI, and wallets.</p>
                </details>
                <details>
                    <summary style="cursor: pointer; font-weight: 600; padding: 1rem;">Can I get a refund?</summary>
                    <p style="padding: 0 1rem 1rem;">Yes, we offer refunds within 7 days of purchase if you're not satisfied.</p>
                </details>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer style="background: var(--bg-secondary); padding: 2rem 0; margin-top: 4rem; border-top: 1px solid var(--border-color);">
        <div class="container">
            <div class="grid grid-3">
                <div>
                    <h4>About Us</h4>
                    <p>Your trusted source for premium digital products.</p>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <ul style="list-style: none;">
                        <li><a href="<?= baseUrl('pages/products.php') ?>">Products</a></li>
                        <li><a href="<?= baseUrl('pages/contact.php') ?>">Contact</a></li>
                        <li><a href="<?= baseUrl('pages/faq.php') ?>">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Contact</h4>
                    <p>Email: <?= $settings['email_from'] ?? 'support@marketplace.com' ?></p>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>&copy; <?= date('Y') ?> <?= $settings['site_name'] ?? 'Digital Marketplace' ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="<?= baseUrl() ?>" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?= baseUrl('pages/products.php') ?>" class="nav-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Products</span>
        </a>
        <?php if ($auth->isLoggedIn()): ?>
            <a href="<?= baseUrl('pages/cart.php') ?>" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Cart</span>
            </a>
            <a href="<?= baseUrl('pages/profile.php') ?>" class="nav-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        <?php else: ?>
            <a href="<?= baseUrl('pages/login.php') ?>" class="nav-item">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- MDBootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
