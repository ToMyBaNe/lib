<?php
// Global configuration file
// Copy this file to config.php and customize as needed

// Database Configuration
const DB_CONFIG = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'database' => 'library_survey',
    'charset' => 'utf8mb4'
];

// Application Configuration
const APP_CONFIG = [
    'name' => 'Library Survey System',
    'version' => '1.0.0',
    'timezone' => 'UTC',
    'language' => 'en',
    'debug' => false,
];

// Survey Configuration
const SURVEY_CONFIG = [
    'max_rating' => 5,
    'min_feedback_length' => 5,
    'max_feedback_length' => 1000,
    'require_email' => false,
    'email_notifications' => false,
    'notification_email' => 'admin@library.local'
];

// Session Configuration
const SESSION_CONFIG = [
    'lifetime' => 3600, // 1 hour
    'name' => 'library_survey_session',
    'path' => '/',
    'domain' => '',
    'secure' => false, // Set to true if using HTTPS
    'http_only' => true,
];

// API Configuration
const API_CONFIG = [
    'rate_limit' => 100, // requests per minute
    'enable_cors' => false,
    'allowed_origins' => ['localhost'],
];

// Pagination
const PAGINATION_CONFIG = [
    'items_per_page' => 10,
    'max_items' => 1000
];

// Features
const FEATURES = [
    'enable_feedback' => true,
    'enable_analytics' => true,
    'enable_export' => false,
    'enable_email_notifications' => false,
];
?>
