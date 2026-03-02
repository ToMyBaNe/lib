<?php
/**
 * Admin Configuration & Initialization
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define admin pages
$pages = [
    [
        'name' => 'Dashboard',
        'icon' => 'fas fa-chart-line',
        'file' => 'dashboard.php',
        'path' => './dashboard.php'
    ],
    [
        'name' => 'Manage Questions',
        'icon' => 'fas fa-question-circle',
        'file' => 'manage_questions.php',
        'path' => './manage_questions.php'
    ],
    [
        'name' => 'Survey Responses',
        'icon' => 'fas fa-comments',
        'file' => 'responses.php',
        'path' => './responses.php'
    ],
    [
        'name' => 'Settings',
        'icon' => 'fas fa-cog',
        'file' => 'settings.php',
        'path' => './settings.php'
    ]
];

// Check authentication
function requireAdminAuth() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        header('Location: ' . dirname($_SERVER['PHP_SELF']) . '/login.php');
        exit;
    }
}

// Get current user
function getCurrentAdmin() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? 'Admin'
    ];
}

// Get current page name
function getCurrentPageName() {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current;
}

// Get page title
function getPageTitle($page = null) {
    if ($page === null) {
        $page = getCurrentPageName();
    }
    
    $titles = [
        'dashboard' => 'Dashboard',
        'manage_questions' => 'Manage Survey Questions',
        'responses' => 'Survey Responses',
        'settings' => 'Settings',
        'login' => 'Admin Login'
    ];
    return $titles[$page] ?? 'Admin Panel';
}
?>
