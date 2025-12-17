<?php
require_once 'config/config.php';
require_once 'includes/Database.php';
require_once 'includes/Auth.php';

echo "Testing admin login...\n\n";

$auth = new Auth();
$result = $auth->login('admin@marketplace.com', 'admin123');

echo "Login result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
echo "Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "Session user_role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "\n";
echo "Session user_name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "\n";
echo "Session user_email: " . ($_SESSION['user_email'] ?? 'NOT SET') . "\n";

if ($result) {
    echo "\n✅ Session is working correctly!\n";
    echo "Admin can access admin panel.\n";
} else {
    echo "\n❌ Login failed!\n";
}
