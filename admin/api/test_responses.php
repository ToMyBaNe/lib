<?php
/**
 * Test endpoint to debug responses API
 */

session_start();
ob_start();

header('Content-Type: application/json');

// Check database connection
require_once '../../api/db_config.php';

if (!isset($conn) || !$conn) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => 'conn variable not set or connection is false'
    ]);
    exit;
}

// Test the connection
if ($conn->connect_error) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error',
        'error' => $conn->connect_error
    ]);
    exit;
}

// Check if survey_responses table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'survey_responses'");

if (!$tableCheck) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Table query failed',
        'error' => $conn->error
    ]);
    exit;
}

$tableExists = $tableCheck->num_rows > 0;

if (!$tableExists) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'survey_responses table not found - need to run setup',
        'setup_url' => '../setup_responses.php',
        'tables_in_db' => []
    ]);
    exit;
}

// Table exists - try a simple query
$result = $conn->query("SELECT COUNT(*) as count FROM survey_responses");

if (!$result) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Query failed',
        'error' => $conn->error
    ]);
    exit;
}

$row = $result->fetch_assoc();

ob_end_clean();
echo json_encode([
    'success' => true,
    'message' => 'Everything is working!',
    'response_count' => $row['count'],
    'database' => $dbname ?? 'unknown',
    'table_exists' => true
]);
exit;
