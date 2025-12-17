<?php
require_once '../../config/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';
require_once '../../includes/functions.php';

// Check if this is an API request (AJAX call)
$isApiRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Alternative check: Content-Type header or Accept header
if (!$isApiRequest) {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $isApiRequest = (strpos($contentType, 'application/json') !== false) || 
                    (strpos($accept, 'application/json') !== false);
}

try {
    $auth = new Auth();
    $auth->logout();
    
    // If it's an API request, return JSON
    if ($isApiRequest) {
        jsonResponse(true, 'Logged out successfully', [
            'redirect' => baseUrl()
        ]);
    } else {
        // Traditional redirect for direct browser access
        redirect(baseUrl());
    }
} catch (Exception $e) {
    if ($isApiRequest) {
        jsonResponse(false, 'Logout failed: ' . $e->getMessage(), null, 500);
    } else {
        // Even if logout fails, redirect to homepage
        redirect(baseUrl());
    }
}
