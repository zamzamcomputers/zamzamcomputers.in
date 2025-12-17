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

if (empty($email) || !validateEmail($email)) {
    jsonResponse(false, 'Valid email is required');
}

$auth = new Auth();
$token = $auth->createPasswordResetToken($email);

if ($token) {
    // In production, send email with reset link
    // For now, return the token (remove this in production)
    $resetLink = baseUrl("pages/reset-password.php?token=$token");
    
    jsonResponse(true, 'Password reset link sent to your email', [
        'reset_link' => $resetLink // Remove in production
    ]);
} else {
    jsonResponse(false, 'Email not found');
}
