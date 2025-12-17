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

$email = sanitize($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validation
if (empty($email) || empty($password)) {
    jsonResponse(false, 'Email and password are required');
}

// Login
$auth = new Auth();
if ($auth->login($email, $password)) {
    $user = $auth->getCurrentUser();
    jsonResponse(true, 'Login successful', [
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ],
        'redirect' => $user['role'] === 'admin' ? baseUrl('admin/index.php') : baseUrl('index.php')
    ]);
} else {
    jsonResponse(false, 'Invalid email or password');
}
