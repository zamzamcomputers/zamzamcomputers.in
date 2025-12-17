<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (empty($data['name']) || empty($data['email']) || empty($data['subject']) || empty($data['message'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    $name = trim($data['name']);
    $email = trim($data['email']);
    $subject = trim($data['subject']);
    $message = trim($data['message']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    // Validate length
    if (strlen($name) < 2 || strlen($name) > 100) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Name must be between 2 and 100 characters']);
        exit;
    }
    
    if (strlen($subject) < 3 || strlen($subject) > 255) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Subject must be between 3 and 255 characters']);
        exit;
    }
    
    if (strlen($message) < 10) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Message must be at least 10 characters long']);
        exit;
    }
    
    // Get user ID if logged in
    $auth = new Auth();
    $userId = $auth->isLoggedIn() ? $auth->user()['id'] : null;
    
    // Insert into database
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        INSERT INTO support_tickets (user_id, name, email, subject, message, status) 
        VALUES (?, ?, ?, ?, ?, 'open')
    ");
    
    $stmt->execute([$userId, $name, $email, $subject, $message]);
    
    // TODO: Send email notification to admin
    // You can implement email sending here using PHPMailer or similar
    
    echo json_encode([
        'success' => true,
        'message' => 'Your message has been sent successfully. We will get back to you soon!',
        'data' => [
            'ticket_id' => $db->lastInsertId()
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Contact form error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request. Please try again later.'
    ]);
}
