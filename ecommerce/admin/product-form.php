<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireAdmin();

$db = Database::getInstance()->getConnection();

// Get product ID if editing
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

if ($productId > 0) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        redirect(baseUrl('admin/products.php'));
    }
}

// Get all categories
$stmt = $db->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product ? 'Edit Product' : 'Add New Product' ?> - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        .form-section {
            background: var(--card-bg);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-section h3 {
            margin-bottom: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .file-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .file-upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(var(--primary-rgb), 0.05);
        }
        .file-upload-area i {
            font-size: 3rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        .file-info {
            margin-top: 1rem;
            padding: 0.75rem;
            background: var(--bg-secondary);
            border-radius: 0.25rem;
            display: none;
        }
        .file-info.active {
            display: block;
        }
    </style>
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
            <h1><?= $product ? 'Edit Product' : 'Add New Product' ?></h1>
            <a href="<?= baseUrl('admin/products.php') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>

        <form id="productForm" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Product Title *</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= htmlspecialchars($product['title'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label">Short Description</label>
                    <input type="text" class="form-control" id="short_description" name="short_description" 
                           value="<?= htmlspecialchars($product['short_description'] ?? '') ?>" 
                           maxlength="500" placeholder="Brief description (max 500 characters)">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Full Description</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="6"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price (<?= getSettings()['currency_symbol'] ?? '₹' ?>) *</label>
                        <input type="number" class="form-control" id="price" name="price" 
                               value="<?= $product['price'] ?? '' ?>" step="0.01" min="0" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Category *</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= ($product && $product['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- File Uploads -->
            <div class="form-section">
                <h3><i class="fas fa-file-upload"></i> Files</h3>
                
                <div class="mb-4">
                    <label class="form-label">Digital Product File</label>
                    <div class="file-upload-area" onclick="document.getElementById('digital_file').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="mb-0">Click to upload digital product file</p>
                        <small class="text-muted">ZIP, PDF, or any digital file</small>
                    </div>
                    <input type="file" id="digital_file" name="digital_file" style="display: none;" 
                           onchange="showFileInfo(this, 'file-info')">
                    <div id="file-info" class="file-info">
                        <i class="fas fa-file"></i> <span id="file-name"></span>
                    </div>
                    <?php if ($product && $product['digital_file_path']): ?>
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-info-circle"></i> Current file: 
                            <strong><?= basename($product['digital_file_path']) ?></strong>
                            (<?= $product['file_size'] ?>)
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Screenshots</label>
                    <div class="file-upload-area" onclick="document.getElementById('screenshots').click()">
                        <i class="fas fa-images"></i>
                        <p class="mb-0">Click to upload screenshots</p>
                        <small class="text-muted">PNG, JPG (Multiple files allowed)</small>
                    </div>
                    <input type="file" id="screenshots" name="screenshots[]" multiple accept="image/*" 
                           style="display: none;" onchange="showFileInfo(this, 'screenshots-info')">
                    <div id="screenshots-info" class="file-info">
                        <i class="fas fa-images"></i> <span id="screenshots-count"></span>
                    </div>
                    <?php if ($product && $product['screenshots']): ?>
                        <?php $screenshots = json_decode($product['screenshots'], true); ?>
                        <?php if (!empty($screenshots)): ?>
                            <div class="alert alert-info mt-2">
                                <i class="fas fa-info-circle"></i> Current screenshots: 
                                <strong><?= count($screenshots) ?> file(s)</strong>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="form-section">
                <h3><i class="fas fa-cog"></i> Additional Details</h3>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="demo_url" class="form-label">Demo URL</label>
                        <input type="url" class="form-control" id="demo_url" name="demo_url" 
                               value="<?= htmlspecialchars($product['demo_url'] ?? '') ?>" 
                               placeholder="https://demo.example.com">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="version" class="form-label">Version</label>
                        <input type="text" class="form-control" id="version" name="version" 
                               value="<?= htmlspecialchars($product['version'] ?? '') ?>" 
                               placeholder="1.0.0">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="requirements" class="form-label">Requirements</label>
                    <textarea class="form-control" id="requirements" name="requirements" 
                              rows="3" placeholder="System requirements, dependencies, etc."><?= htmlspecialchars($product['requirements'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= ($product && $product['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($product && $product['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            <option value="pending" <?= ($product && $product['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                                   <?= ($product && $product['featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="featured">
                                <i class="fas fa-star"></i> Featured Product
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $product ? 'Update Product' : 'Create Product' ?>
                </button>
                <a href="<?= baseUrl('admin/products.php') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        function showFileInfo(input, infoId) {
            const infoDiv = document.getElementById(infoId);
            
            if (input.files.length > 0) {
                if (input.multiple) {
                    const count = input.files.length;
                    document.getElementById('screenshots-count').textContent = 
                        `${count} file${count > 1 ? 's' : ''} selected`;
                } else {
                    document.getElementById('file-name').textContent = input.files[0].name;
                }
                infoDiv.classList.add('active');
            } else {
                infoDiv.classList.remove('active');
            }
        }

        document.getElementById('productForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('<?= baseUrl('api/products/save.php') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = '<?= baseUrl('admin/products.php') ?>';
                    }, 1500);
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('Error saving product: ' + error.message, 'error');
            }
        });
    </script>
</body>
</html>
