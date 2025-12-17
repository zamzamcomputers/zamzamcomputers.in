<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?= $settings['site_name'] ?? 'Digital Marketplace' ?></title>
    <meta name="description" content="Get in touch with us for any questions or support">
    
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
                <li><a href="<?= baseUrl('pages/contact.php') ?>" style="color: var(--primary);">Contact</a></li>
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
            <h1><i class="fas fa-envelope"></i> Contact Us</h1>
            <p>We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>
        </section>

        <div class="grid grid-2" style="margin-top: 2rem; margin-bottom: 4rem;">
            <!-- Contact Form -->
            <div class="card fade-in">
                <h2 class="mb-3"><i class="fas fa-paper-plane"></i> Send us a Message</h2>
                
                <form id="contactForm">
                    <div class="form-group">
                        <label for="name">Full Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="name" class="form-control" required 
                               placeholder="Enter your full name"
                               value="<?= $auth->isLoggedIn() ? htmlspecialchars($auth->user()['name']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address <span style="color: var(--danger);">*</span></label>
                        <input type="email" id="email" class="form-control" required 
                               placeholder="your.email@example.com"
                               value="<?= $auth->isLoggedIn() ? htmlspecialchars($auth->user()['email']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="subject" class="form-control" required 
                               placeholder="What is this about?">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message <span style="color: var(--danger);">*</span></label>
                        <textarea id="message" class="form-control" rows="6" required 
                                  placeholder="Tell us more about your inquiry..."
                                  style="resize: vertical; min-height: 150px;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div>
                <div class="card fade-in mb-3">
                    <h3 class="mb-3"><i class="fas fa-info-circle"></i> Contact Information</h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                            <div style="width: 50px; height: 50px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-envelope" style="color: white; font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <strong>Email</strong>
                                <p class="text-secondary" style="margin: 0;"><?= $settings['email_from'] ?? 'support@marketplace.com' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                            <div style="width: 50px; height: 50px; background: var(--gradient-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clock" style="color: white; font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <strong>Business Hours</strong>
                                <p class="text-secondary" style="margin: 0;">Monday - Friday: 9:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; background: var(--gradient-success); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-headset" style="color: white; font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <strong>Support</strong>
                                <p class="text-secondary" style="margin: 0;">24/7 Customer Support</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Quick Links -->
                <div class="card fade-in">
                    <h3 class="mb-3"><i class="fas fa-question-circle"></i> Quick Help</h3>
                    <p class="text-secondary">Before contacting us, you might find answers to common questions:</p>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.75rem;">
                            <a href="<?= baseUrl('pages/faq.php') ?>" style="color: var(--primary); text-decoration: none;">
                                <i class="fas fa-chevron-right"></i> Frequently Asked Questions
                            </a>
                        </li>
                        <li style="margin-bottom: 0.75rem;">
                            <a href="<?= baseUrl('pages/products.php') ?>" style="color: var(--primary); text-decoration: none;">
                                <i class="fas fa-chevron-right"></i> Browse Products
                            </a>
                        </li>
                        <?php if ($auth->isLoggedIn()): ?>
                        <li>
                            <a href="<?= baseUrl('pages/orders.php') ?>" style="color: var(--primary); text-decoration: none;">
                                <i class="fas fa-chevron-right"></i> My Orders
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
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
        <a href="<?= baseUrl() ?>" class="nav-item">
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
    
    <script>
        document.getElementById('contactForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            // Basic validation
            if (!name || !email || !subject || !message) {
                showToast('Please fill in all required fields', 'error');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('Please enter a valid email address', 'error');
                return;
            }
            
            try {
                const result = await apiRequest('/api/contact.php', {
                    method: 'POST',
                    body: JSON.stringify({ name, email, subject, message })
                });
                
                if (result.success) {
                    showToast('Message sent successfully! We\'ll get back to you soon.', 'success');
                    // Clear form
                    document.getElementById('contactForm').reset();
                    // If logged in, restore user info
                    <?php if ($auth->isLoggedIn()): ?>
                    document.getElementById('name').value = '<?= htmlspecialchars($auth->user()['name']) ?>';
                    document.getElementById('email').value = '<?= htmlspecialchars($auth->user()['email']) ?>';
                    <?php endif; ?>
                } else {
                    showToast(result.message || 'Failed to send message', 'error');
                }
            } catch (error) {
                console.error('Contact form error:', error);
                showToast('An error occurred while sending your message', 'error');
            }
        });
    </script>
</body>
</html>
