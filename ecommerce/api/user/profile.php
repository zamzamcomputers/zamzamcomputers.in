<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$auth = new Auth();
requireLogin();

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get user profile
    $user = $auth->getCurrentUser();
    
    if ($user) {
        unset($user['password']); // Don't send password
        jsonResponse(true, '', ['user' => $user]);
    } else {
        jsonResponse(false, 'User not found', null, 404);
    }
    
} elseif ($method === 'PUT') {
    // Update profile or password
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if it's a password change request
    if (isset($data['current_password']) && isset($data['new_password'])) {
        // Change password
        $currentPassword = $data['current_password'];
        $newPassword = $data['new_password'];
        
        if (strlen($newPassword) < 6) {
            jsonResponse(false, 'New password must be at least 6 characters');
        }
        
        if ($auth->changePassword($_SESSION['user_id'], $currentPassword, $newPassword)) {
            jsonResponse(true, 'Password changed successfully');
        } else {
            jsonResponse(false, 'Current password is incorrect');
        }
        
    } else {
        // Update profile
        $name = sanitize($data['name'] ?? '');
        $email = sanitize($data['email'] ?? '');
        
        if (empty($name) || empty($email)) {
            jsonResponse(false, 'Name and email are required');
        }
        
        if (!validateEmail($email)) {
            jsonResponse(false, 'Invalid email address');
        }
        
        // Check if email is already taken by another user
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        
        if ($stmt->fetch()) {
            jsonResponse(false, 'Email already in use by another account');
        }
        
        if ($auth->updateProfile($_SESSION['user_id'], $name, $email)) {
            // Update session
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            jsonResponse(true, 'Profile updated successfully');
        } else {
            jsonResponse(false, 'Failed to update profile');
        }
    }
    
} else {
    jsonResponse(false, 'Method not allowed', null, 405);
}
