<?php
/**
 * Admin Panel Configuration & Authentication
 * Centralized auth, session management, and configuration
 */

// Start session - MUST be first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once dirname(__FILE__) . '/../api/db_config.php';

// Admin navigation menu
$adminPages = [
    [
        'name' => 'Dashboard',
        'icon' => 'fas fa-chart-line',
        'file' => 'dashboard.php',
        'url' => './dashboard.php',
        'permission' => 'view_dashboard'
    ],
    [
        'name' => 'Manage Questions',
        'icon' => 'fas fa-question-circle',
        'file' => 'manage_questions.php',
        'url' => './manage_questions.php',
        'permission' => 'manage_questions'
    ],
    [
        'name' => 'Survey Responses',
        'icon' => 'fas fa-comments',
        'file' => 'responses.php',
        'url' => './responses.php',
        'permission' => 'view_responses'
    ],
    [
        'name' => 'Settings',
        'icon' => 'fas fa-cog',
        'file' => 'settings.php',
        'url' => './settings.php',
        'permission' => 'manage_settings'
    ]
];

/**
 * Verify admin is logged in and authorized
 */
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
        'settings' => 'Account Settings',
        'login' => 'Login',
        'generate_link' => 'Generate Link for Survey'
    ];
    return $titles[$page] ?? 'Library Survey Management System';
}
?>
