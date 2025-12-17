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
    <title>FAQ - <?= $settings['site_name'] ?? 'Digital Marketplace' ?></title>
    <meta name="description" content="Frequently asked questions about our digital marketplace">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- MDBootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    
    <style>
        .faq-category {
            margin-bottom: 2rem;
        }
        
        .faq-category-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary);
        }
        
        .faq-category-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .faq-item {
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .faq-question {
            padding: 1.25rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            background: var(--bg-secondary);
            transition: all 0.3s ease;
            user-select: none;
        }
        
        .faq-question:hover {
            background: var(--bg-hover);
        }
        
        .faq-question.active {
            background: var(--primary);
            color: white;
        }
        
        .faq-question-text {
            font-weight: 600;
            font-size: 1.05rem;
            flex: 1;
        }
        
        .faq-toggle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        
        .faq-question.active .faq-toggle {
            background: white;
            color: var(--primary);
            transform: rotate(180deg);
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .faq-answer.active {
            max-height: 1000px;
        }
        
        .faq-answer-content {
            padding: 1.25rem;
            background: var(--bg-primary);
            color: var(--text-secondary);
            line-height: 1.7;
        }
        
        .faq-answer-content ul {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }
        
        .faq-answer-content li {
            margin-bottom: 0.5rem;
        }
        
        .search-box {
            margin-bottom: 2rem;
        }
        
        .search-box input {
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1.1rem;
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.25rem;
        }
        
        .no-results {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }
        
        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .contact-cta {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            margin-top: 3rem;
        }
        
        .contact-cta h3 {
            color: white;
            margin-bottom: 1rem;
        }
        
        .contact-cta .btn {
            background: white;
            color: var(--primary);
            font-weight: 600;
        }
        
        .contact-cta .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
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
                <li><a href="<?= baseUrl('pages/faq.php') ?>" style="color: var(--primary);">FAQ</a></li>
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
            <h1><i class="fas fa-question-circle"></i> Frequently Asked Questions</h1>
            <p>Find answers to common questions about our digital marketplace</p>
        </section>

        <!-- Search Box -->
        <div class="search-box fade-in" style="position: relative; margin-top: 2rem;">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="faqSearch" class="form-control" placeholder="Search for answers...">
        </div>

        <!-- FAQ Categories -->
        <div id="faqContainer">
            <!-- General Questions -->
            <div class="faq-category fade-in" data-category="general">
                <div class="faq-category-header">
                    <div class="faq-category-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h2>General Questions</h2>
                </div>

                <div class="faq-item" data-keywords="what is digital marketplace platform about">
                    <div class="faq-question">
                        <span class="faq-question-text">What is this platform about?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Our platform is a digital marketplace where you can buy and sell premium digital products such as software, templates, graphics, courses, and more. We provide a secure and user-friendly environment for creators and buyers to connect.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="how do i create account register signup">
                    <div class="faq-question">
                        <span class="faq-question-text">How do I create an account?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Creating an account is easy! Click on the "Sign Up" button in the navigation bar, fill in your details (name, email, and password), and submit the form. You'll be logged in automatically and can start browsing products immediately.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="is it free to use browse products">
                    <div class="faq-question">
                        <span class="faq-question-text">Is it free to browse products?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Yes! Browsing our product catalog is completely free. You only need to create an account and make a payment when you want to purchase a product.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchasing & Payments -->
            <div class="faq-category fade-in" data-category="purchasing">
                <div class="faq-category-header">
                    <div class="faq-category-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Purchasing & Payments</h2>
                </div>

                <div class="faq-item" data-keywords="how to buy purchase product checkout">
                    <div class="faq-question">
                        <span class="faq-question-text">How do I purchase a product?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            To purchase a product:
                            <ul>
                                <li>Browse our product catalog and find what you need</li>
                                <li>Click "Add to Cart" on the product page</li>
                                <li>Go to your cart and review your items</li>
                                <li>Click "Proceed to Checkout"</li>
                                <li>Fill in your billing information and complete the payment</li>
                                <li>Download your product immediately after successful payment</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="payment methods accepted credit card paypal">
                    <div class="faq-question">
                        <span class="faq-question-text">What payment methods do you accept?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            We accept various payment methods including credit cards, debit cards, and online payment gateways. All transactions are secured with industry-standard encryption to protect your financial information.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="coupon discount code promo apply">
                    <div class="faq-question">
                        <span class="faq-question-text">Can I use coupon codes?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Yes! During checkout, you'll find a field to enter your coupon code. Enter the code and click "Apply" to see the discount reflected in your total. Coupons may have specific terms and conditions such as minimum purchase requirements or expiration dates.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="is payment secure safe transaction">
                    <div class="faq-question">
                        <span class="faq-question-text">Is my payment information secure?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Absolutely! We use industry-standard SSL encryption to protect all transactions. Your payment information is processed securely and we never store your complete credit card details on our servers.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Downloads & Access -->
            <div class="faq-category fade-in" data-category="downloads">
                <div class="faq-category-header">
                    <div class="faq-category-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <h2>Downloads & Access</h2>
                </div>

                <div class="faq-item" data-keywords="how to download product after purchase">
                    <div class="faq-question">
                        <span class="faq-question-text">How do I download my purchased products?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            After completing your purchase, you can download your products in two ways:
                            <ul>
                                <li>Immediately from the order confirmation page</li>
                                <li>Anytime from your Profile → Downloads section</li>
                            </ul>
                            All your purchased products are available for unlimited downloads from your account.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="download link expired redownload">
                    <div class="faq-question">
                        <span class="faq-question-text">Do download links expire?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            No! Your download links never expire. You can access and download your purchased products anytime from your account's Downloads section. We recommend keeping backups of your downloads for your convenience.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="how many times can i download limit">
                    <div class="faq-question">
                        <span class="faq-question-text">How many times can I download a product?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            You can download your purchased products unlimited times. There are no restrictions on the number of downloads, so feel free to download whenever you need.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="lost file redownload access">
                    <div class="faq-question">
                        <span class="faq-question-text">What if I lose my downloaded files?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            No problem! Simply log in to your account, go to Profile → Downloads, and download the product again. All your purchases are permanently stored in your account.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account & Profile -->
            <div class="faq-category fade-in" data-category="account">
                <div class="faq-category-header">
                    <div class="faq-category-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h2>Account & Profile</h2>
                </div>

                <div class="faq-item" data-keywords="forgot password reset recover account">
                    <div class="faq-question">
                        <span class="faq-question-text">I forgot my password. How do I reset it?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Click on "Forgot Password?" on the login page, enter your email address, and we'll send you a password reset link. Follow the link to create a new password and regain access to your account.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="change email update profile information">
                    <div class="faq-question">
                        <span class="faq-question-text">Can I change my email address?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Yes! Go to your Profile page and you can update your email address and other account information. Make sure to use a valid email address as it's used for order confirmations and important notifications.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="view order history purchases past">
                    <div class="faq-question">
                        <span class="faq-question-text">How do I view my order history?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Log in to your account and navigate to Profile → Orders. Here you'll find a complete history of all your purchases, including order dates, products, and payment status.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="delete account close remove">
                    <div class="faq-question">
                        <span class="faq-question-text">Can I delete my account?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            If you wish to delete your account, please contact our support team through the Contact page. Note that deleting your account may affect your access to previously purchased products, so we recommend downloading all your files before requesting account deletion.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refunds & Support -->
            <div class="faq-category fade-in" data-category="support">
                <div class="faq-category-header">
                    <div class="faq-category-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h2>Refunds & Support</h2>
                </div>

                <div class="faq-item" data-keywords="refund policy return money back">
                    <div class="faq-question">
                        <span class="faq-question-text">What is your refund policy?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Due to the digital nature of our products, all sales are generally final. However, if you experience technical issues with a product or it doesn't match the description, please contact our support team within 7 days of purchase. We'll review your case and provide appropriate assistance or refund if warranted.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="product not working broken issue problem">
                    <div class="faq-question">
                        <span class="faq-question-text">What if a product doesn't work?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            If you encounter any issues with a product, please contact us immediately through the Contact page. Provide details about the problem, and our support team will assist you. We may offer a replacement, fix, or refund depending on the situation.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="contact support help customer service">
                    <div class="faq-question">
                        <span class="faq-question-text">How do I contact customer support?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            You can reach our customer support team through the Contact page. Fill out the contact form with your inquiry, and we'll respond as soon as possible. We typically respond within 24 hours during business days.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-keywords="business hours support availability when">
                    <div class="faq-question">
                        <span class="faq-question-text">What are your support hours?</span>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Our support team is available Monday through Friday, 9:00 AM to 6:00 PM. While we strive to provide 24/7 assistance, responses to inquiries submitted outside business hours will be addressed on the next business day.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fas fa-search"></i>
            <h3>No results found</h3>
            <p>Try different keywords or browse the categories above</p>
        </div>

        <!-- Contact CTA -->
        <div class="contact-cta fade-in">
            <h3><i class="fas fa-question-circle"></i> Still have questions?</h3>
            <p>Can't find the answer you're looking for? Our support team is here to help!</p>
            <a href="<?= baseUrl('pages/contact.php') ?>" class="btn" style="margin-top: 1rem;">
                <i class="fas fa-envelope"></i> Contact Support
            </a>
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
        // FAQ Accordion functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const isActive = this.classList.contains('active');
                
                // Close all other FAQs
                document.querySelectorAll('.faq-question').forEach(q => {
                    q.classList.remove('active');
                    q.nextElementSibling.classList.remove('active');
                });
                
                // Toggle current FAQ
                if (!isActive) {
                    this.classList.add('active');
                    answer.classList.add('active');
                }
            });
        });

        // Search functionality
        const searchInput = document.getElementById('faqSearch');
        const faqContainer = document.getElementById('faqContainer');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const faqItems = document.querySelectorAll('.faq-item');
            const categories = document.querySelectorAll('.faq-category');
            let visibleCount = 0;

            if (searchTerm === '') {
                // Show all items and categories
                faqItems.forEach(item => item.style.display = 'block');
                categories.forEach(cat => cat.style.display = 'block');
                noResults.style.display = 'none';
                return;
            }

            // Search through FAQ items
            faqItems.forEach(item => {
                const questionText = item.querySelector('.faq-question-text').textContent.toLowerCase();
                const answerText = item.querySelector('.faq-answer-content').textContent.toLowerCase();
                const keywords = item.getAttribute('data-keywords') || '';
                
                const matches = questionText.includes(searchTerm) || 
                               answerText.includes(searchTerm) || 
                               keywords.includes(searchTerm);
                
                if (matches) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Hide empty categories
            categories.forEach(category => {
                const visibleItems = category.querySelectorAll('.faq-item[style="display: block;"]');
                if (visibleItems.length === 0) {
                    category.style.display = 'none';
                } else {
                    category.style.display = 'block';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0) {
                noResults.style.display = 'block';
                faqContainer.style.display = 'none';
            } else {
                noResults.style.display = 'none';
                faqContainer.style.display = 'block';
            }
        });

        // Auto-expand FAQ from URL hash
        window.addEventListener('load', function() {
            if (window.location.hash) {
                const targetId = window.location.hash.substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement && targetElement.classList.contains('faq-question')) {
                    targetElement.click();
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    </script>
</body>
</html>
