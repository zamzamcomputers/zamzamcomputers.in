<?php
require_once 'config/config.php';
require_once 'includes/Database.php';
require_once 'includes/Auth.php';

// Test admin login
$email = 'admin@marketplace.com';
$password = 'admin123'; // Default password

$auth = new Auth();
$result = $auth->login($email, $password);

if ($result) {
    echo "✅ Login successful!\n";
    echo "Session data:\n";
    print_r($_SESSION);
} else {
    echo "❌ Login failed!\n";
    echo "Testing password verification...\n";
    
    // Get user from database
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "User found: " . $user['name'] . "\n";
        echo "Password hash: " . substr($user['password'], 0, 20) . "...\n";
        echo "Password verify result: " . (password_verify($password, $user['password']) ? 'MATCH' : 'NO MATCH') . "\n";
        
        // Try to reset password
        echo "\nResetting password to 'admin123'...\n";
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        if ($stmt->execute([$hashedPassword, $email])) {
            echo "✅ Password reset successful!\n";
            echo "You can now login with:\n";
            echo "Email: admin@marketplace.com\n";
            echo "Password: admin123\n";
        } else {
            echo "❌ Password reset failed!\n";
        }
    } else {
        echo "User not found!\n";
    }
}
