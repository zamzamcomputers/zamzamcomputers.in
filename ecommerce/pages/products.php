<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
$db = Database::getInstance()->getConnection();

// Get categories for filter
$stmt = $db->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Digital Marketplace</title>
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
            <ul class="nav-links">
                <li><a href="<?= baseUrl() ?>">Home</a></li>
                <li><a href="<?= baseUrl('pages/products.php') ?>" class="active">Products</a></li>
                <?php if ($auth->isLoggedIn()): ?>
                    <li><a href="<?= baseUrl('pages/cart.php') ?>">Cart</a></li>
                    <li><a href="<?= baseUrl('pages/profile.php') ?>">Profile</a></li>
                <?php else: ?>
                    <li><a href="<?= baseUrl('pages/login.php') ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Browse Products</h1>
        
        <!-- Filters -->
        <div class="card mb-4">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
                </div>
                
                <div class="form-group" style="min-width: 150px;">
                    <select id="categoryFilter" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="min-width: 150px;">
                    <select id="sortFilter" class="form-control">
                        <option value="latest">Latest</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="popular">Most Popular</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div id="productsContainer" class="grid grid-3">
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div id="pagination" class="text-center mt-4"></div>
    </div>

    <nav class="mobile-bottom-nav">
        <a href="<?= baseUrl() ?>" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?= baseUrl('pages/products.php') ?>" class="nav-item active">
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
        <?php endif; ?>
    </nav>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        let currentPage = 1;
        
        async function loadProducts() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const sort = document.getElementById('sortFilter').value;
            
            const params = new URLSearchParams({
                page: currentPage,
                search,
                category,
                sort
            });
            
            const result = await apiRequest(`/products/list.php?${params}`);
            
            if (result.success) {
                displayProducts(result.data.products);
                displayPagination(result.data.pagination);
            }
        }
        
        function displayProducts(products) {
            const container = document.getElementById('productsContainer');
            
            if (products.length === 0) {
                container.innerHTML = '<p class="text-center">No products found</p>';
                return;
            }
            
            container.innerHTML = products.map(product => {
                const image = product.screenshots && product.screenshots[0] 
                    ? '<?= baseUrl() ?>/' + product.screenshots[0]
                    : '<?= asset('images/placeholder.jpg') ?>';
                
                return `
                    <div class="card product-card fade-in">
                        <img src="${image}" alt="${product.title}">
                        <h3>${product.title}</h3>
                        <p class="text-secondary">${product.short_description || ''}</p>
                        <div class="price">${product.price_formatted}</div>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="product-detail.php?id=${product.id}" class="btn btn-primary" style="flex: 1;">
                                View Details
                            </a>
                            <?php if ($auth->isLoggedIn()): ?>
                                <button onclick="addToCart(${product.id})" class="btn btn-outline">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        function displayPagination(pagination) {
            const container = document.getElementById('pagination');
            const pages = [];
            
            for (let i = 1; i <= pagination.total_pages; i++) {
                pages.push(`
                    <button 
                        class="btn ${i === pagination.current_page ? 'btn-primary' : 'btn-outline'}"
                        onclick="currentPage = ${i}; loadProducts();"
                        style="margin: 0 0.25rem;"
                    >
                        ${i}
                    </button>
                `);
            }
            
            container.innerHTML = pages.join('');
        }
        
        // Event listeners
        document.getElementById('searchInput').addEventListener('input', () => {
            currentPage = 1;
            loadProducts();
        });
        
        document.getElementById('categoryFilter').addEventListener('change', () => {
            currentPage = 1;
            loadProducts();
        });
        
        document.getElementById('sortFilter').addEventListener('change', () => {
            currentPage = 1;
            loadProducts();
        });
        
        // Load products on page load
        loadProducts();
    </script>
</body>
</html>
