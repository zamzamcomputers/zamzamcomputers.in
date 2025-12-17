<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method', null, 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$token = sanitize($data['token'] ?? '');
$password = $data['password'] ?? '';

if (empty($token) || empty($password)) {
    jsonResponse(false, 'Token and password are required');
}

if (strlen($password) < 6) {
    jsonResponse(false, 'Password must be at least 6 characters');
}

$auth = new Auth();
if ($auth->resetPassword($token, $password)) {
    jsonResponse(true, 'Password reset successful! Please login.');
} else {
    jsonResponse(false, 'Invalid or expired reset token');
}
