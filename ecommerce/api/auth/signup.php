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

$name = sanitize($data['name'] ?? '');
$email = sanitize($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validation
if (empty($name) || empty($email) || empty($password)) {
    jsonResponse(false, 'All fields are required');
}

if (!validateEmail($email)) {
    jsonResponse(false, 'Invalid email address');
}

if (strlen($password) < 6) {
    jsonResponse(false, 'Password must be at least 6 characters');
}

// Check if email already exists
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    jsonResponse(false, 'Email already registered');
}

// Register user
$auth = new Auth();
if ($auth->register($name, $email, $password)) {
    jsonResponse(true, 'Registration successful! Please login.');
} else {
    jsonResponse(false, 'Registration failed. Please try again.');
}
