<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$auth = new Auth();
requireAdmin();

$db = Database::getInstance()->getConnection();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Get form data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$title = sanitize($_POST['title'] ?? '');
$short_description = sanitize($_POST['short_description'] ?? '');
$description = sanitize($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$category_id = intval($_POST['category_id'] ?? 0);
$demo_url = sanitize($_POST['demo_url'] ?? '');
$version = sanitize($_POST['version'] ?? '');
$requirements = sanitize($_POST['requirements'] ?? '');
$status = sanitize($_POST['status'] ?? 'active');
$featured = isset($_POST['featured']) ? 1 : 0;

// Validation
if (empty($title)) {
    jsonResponse(false, 'Product title is required');
}

if ($price <= 0) {
    jsonResponse(false, 'Price must be greater than 0');
}

if ($category_id <= 0) {
    jsonResponse(false, 'Please select a category');
}

// Generate slug from title
$slug = generateSlug($title);

// Check if slug exists (for other products)
$stmt = $db->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
$stmt->execute([$slug, $id]);
if ($stmt->fetch()) {
    $slug = $slug . '-' . time();
}

// Handle file uploads
$digital_file_path = '';
$screenshots = [];
$file_size = '';

// Handle digital file upload
if (isset($_FILES['digital_file']) && $_FILES['digital_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['digital_file'];
    $uploadDir = '../../uploads/products/files/';
    
    // Create unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $slug . '-' . time() . '.' . $ext;
    $uploadPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $digital_file_path = 'uploads/products/files/' . $filename;
        $file_size = formatFileSize($file['size']);
    }
} else if ($id > 0) {
    // Keep existing file path if editing
    $stmt = $db->prepare("SELECT digital_file_path, file_size FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $digital_file_path = $existing['digital_file_path'];
        $file_size = $existing['file_size'];
    }
}

// Handle screenshots upload
if (isset($_FILES['screenshots']) && is_array($_FILES['screenshots']['name'])) {
    $uploadDir = '../../uploads/products/screenshots/';
    
    foreach ($_FILES['screenshots']['name'] as $key => $name) {
        if ($_FILES['screenshots']['error'][$key] === UPLOAD_ERR_OK) {
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $slug . '-' . time() . '-' . $key . '.' . $ext;
            $uploadPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['screenshots']['tmp_name'][$key], $uploadPath)) {
                $screenshots[] = 'uploads/products/screenshots/' . $filename;
            }
        }
    }
} else if ($id > 0) {
    // Keep existing screenshots if editing
    $stmt = $db->prepare("SELECT screenshots FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if ($existing && !empty($existing['screenshots'])) {
        $screenshots = json_decode($existing['screenshots'], true) ?? [];
    }
}

$screenshots_json = json_encode($screenshots);

try {
    if ($id > 0) {
        // Update existing product
        $sql = "UPDATE products SET 
                title = ?, 
                slug = ?, 
                description = ?, 
                short_description = ?, 
                price = ?, 
                category_id = ?, 
                digital_file_path = ?, 
                file_size = ?, 
                screenshots = ?, 
                demo_url = ?, 
                version = ?, 
                requirements = ?, 
                status = ?, 
                featured = ?,
                updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $title, 
            $slug, 
            $description, 
            $short_description, 
            $price, 
            $category_id, 
            $digital_file_path, 
            $file_size, 
            $screenshots_json, 
            $demo_url, 
            $version, 
            $requirements, 
            $status, 
            $featured,
            $id
        ]);
        
        jsonResponse(true, 'Product updated successfully', ['id' => $id]);
    } else {
        // Create new product
        $sql = "INSERT INTO products (
                    title, slug, description, short_description, price, 
                    category_id, digital_file_path, file_size, screenshots, 
                    demo_url, version, requirements, status, featured, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $title, 
            $slug, 
            $description, 
            $short_description, 
            $price, 
            $category_id, 
            $digital_file_path, 
            $file_size, 
            $screenshots_json, 
            $demo_url, 
            $version, 
            $requirements, 
            $status, 
            $featured,
            $_SESSION['user_id']
        ]);
        
        $newId = $db->lastInsertId();
        jsonResponse(true, 'Product created successfully', ['id' => $newId]);
    }
} catch (Exception $e) {
    jsonResponse(false, 'Error saving product: ' . $e->getMessage());
}
