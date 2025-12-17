<?php
/**
 * Fix Admin Password Script
 * This script will update the admin user's password to 'admin123'
 * Run this file once: http://localhost/ecommerce/fix-admin.php
 */

require_once 'config/config.php';
require_once 'includes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Generate correct password hash for 'admin123'
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    
    echo "<h2>Admin Password Fix</h2>";
    
    // Check if admin user exists
    $stmt = $db->prepare("SELECT id, email, role FROM users WHERE email = ?");
    $stmt->execute(['admin@marketplace.com']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p>Found admin user: {$admin['email']} (ID: {$admin['id']}, Role: {$admin['role']})</p>";
        
        // Update password
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, 'admin@marketplace.com']);
        
        echo "<p style='color: green;'>✓ Admin password updated successfully!</p>";
        echo "<p><strong>Admin Credentials:</strong></p>";
        echo "<ul>";
        echo "<li>Email: admin@marketplace.com</li>";
        echo "<li>Password: admin123</li>";
        echo "</ul>";
        
        // Verify the password works
        if (password_verify($password, $hashedPassword)) {
            echo "<p style='color: green;'>✓ Password verification test passed!</p>";
        }
        
        echo "<p><a href='pages/login.php'>Go to Login Page</a></p>";
        echo "<p><a href='admin/index.php'>Go to Admin Panel</a></p>";
        
    } else {
        // Create admin user if doesn't exist
        echo "<p style='color: orange;'>Admin user not found. Creating new admin user...</p>";
        
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin', 'admin@marketplace.com', $hashedPassword, 'admin']);
        
        echo "<p style='color: green;'>✓ Admin user created successfully!</p>";
        echo "<p><strong>Admin Credentials:</strong></p>";
        echo "<ul>";
        echo "<li>Email: admin@marketplace.com</li>";
        echo "<li>Password: admin123</li>";
        echo "</ul>";
        
        echo "<p><a href='pages/login.php'>Go to Login Page</a></p>";
    }
    
    echo "<p style='color: red;'><strong>Important:</strong> Delete this file (fix-admin.php) after use for security!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Admin Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 {
            color: #333;
        }
        ul {
            background: white;
            padding: 20px;
            border-radius: 5px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
</body>
</html>
