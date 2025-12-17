<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

requireLogin();

$token = sanitize($_GET['token'] ?? '');

if (empty($token)) {
    die('Invalid download link');
}

$db = Database::getInstance()->getConnection();

// Get download record
$stmt = $db->prepare("
    SELECT 
        d.*,
        p.title,
        p.digital_file_path,
        o.payment_status
    FROM downloads d
    JOIN products p ON d.product_id = p.id
    JOIN orders o ON d.order_id = o.id
    WHERE d.download_token = ? AND d.user_id = ?
");
$stmt->execute([$token, $_SESSION['user_id']]);
$download = $stmt->fetch();

if (!$download) {
    die('Invalid download link');
}

// Check payment status
if ($download['payment_status'] !== 'completed') {
    die('Payment not completed');
}

// Check expiry
if ($download['expires_at'] && strtotime($download['expires_at']) < time()) {
    die('Download link has expired');
}

// Check download limit
if ($download['max_downloads'] > 0 && $download['download_count'] >= $download['max_downloads']) {
    die('Download limit exceeded');
}

// Check if file exists
$filePath = BASE_PATH . '/' . $download['digital_file_path'];

if (!file_exists($filePath)) {
    die('File not found');
}

// Update download count
$stmt = $db->prepare("
    UPDATE downloads 
    SET download_count = download_count + 1, last_downloaded_at = NOW()
    WHERE id = ?
");
$stmt->execute([$download['id']]);

// Download file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

readfile($filePath);
exit;
