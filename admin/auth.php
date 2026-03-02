<?php
// Authentication middleware for admin pages

function requireAuth() {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        http_response_code(401);
        header('Location: ./login.php');
        exit('Unauthorized');
    }
    
    // Optional: Add token validation
    if (!isset($_SESSION['token'])) {
        http_response_code(401);
        header('Location: ./login.php');
        exit('Invalid session');
    }
}

function getCurrentUser() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ];
}

function logout() {
    session_start();
    session_destroy();
    header('Location: ./login.php');
    exit;
}

// Helper function to check API authentication
function requireApiAuth() {
    // For API endpoints, can check session or token
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}
?>
