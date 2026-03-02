<?php
// Start output buffering to catch any accidental output
ob_start();

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set proper headers BEFORE any output
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean(); // Clear any buffered output
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'PHP Error',
        'debug' => $errstr . ' at line ' . $errline
    ]);
    exit;
});

// Catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && $error['type'] === E_ERROR) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Fatal server error',
            'debug' => $error['message']
        ]);
    }
});

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit;
}

session_start();

// Load database config
if (!file_exists('db_config.php')) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database configuration not found'
    ]);
    exit;
}

require_once 'db_config.php';

// Verify connection
if (!isset($conn) || !$conn) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get and validate input
    $visitor_name = trim($_POST['visitor_name'] ?? '');
    $visitor_email = trim($_POST['visitor_email'] ?? '');
    $visit_frequency = trim($_POST['visit_frequency'] ?? '');
    $purpose = trim($_POST['purpose'] ?? '');
    $satisfaction = intval($_POST['satisfaction'] ?? 0);
    $book_availability = intval($_POST['book_availability'] ?? 0);
    $staff_helpfulness = intval($_POST['staff_helpfulness'] ?? 0);
    $facilities_rating = intval($_POST['facilities_rating'] ?? 0);
    $would_recommend = intval($_POST['would_recommend'] ?? 0);
    $improvements_feedback = trim($_POST['improvements_feedback'] ?? '');
    
    // Validate required fields
    if (empty($visitor_name)) {
        throw new Exception('Visitor name is required');
    }
    
    if (empty($visit_frequency)) {
        throw new Exception('Visit frequency is required');
    }
    
    if (empty($purpose)) {
        throw new Exception('Purpose is required');
    }
    
    // Validate ratings
    if ($satisfaction < 1 || $satisfaction > 5) {
        throw new Exception('Invalid satisfaction rating');
    }
    
    if ($book_availability < 1 || $book_availability > 5) {
        throw new Exception('Invalid book availability rating');
    }
    
    if ($staff_helpfulness < 1 || $staff_helpfulness > 5) {
        throw new Exception('Invalid staff helpfulness rating');
    }
    
    if ($facilities_rating < 1 || $facilities_rating > 5) {
        throw new Exception('Invalid facilities rating');
    }
    
    if ($would_recommend < 0 || $would_recommend > 4) {
        throw new Exception('Invalid recommendation value');
    }
    
    // Validate email if provided
    if (!empty($visitor_email) && !filter_var($visitor_email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Prepare and execute query
    $stmt = $conn->prepare(
        "INSERT INTO survey_responses 
        (visitor_name, visitor_email, visit_frequency, purpose, satisfaction, 
         book_availability, staff_helpfulness, facilities_rating, would_recommend, improvements_feedback)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param(
        'ssssiiiiis',
        $visitor_name,
        $visitor_email,
        $visit_frequency,
        $purpose,
        $satisfaction,
        $book_availability,
        $staff_helpfulness,
        $facilities_rating,
        $would_recommend,
        $improvements_feedback
    );
    
    // Clear empty email field
    if (empty($visitor_email)) {
        $visitor_email = null;
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to insert survey response: ' . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    // Mark survey as completed in session
    $_SESSION['survey_completed'] = true;
    
    // Get return URL if set
    $return_url = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : null;
    
    // Clear output buffer and send clean JSON
    ob_end_clean();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Survey submitted successfully',
        'redirect_url' => $return_url
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}