<?php
/**
 * Admin API for Survey Responses
 */

session_start();
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Check admin authentication
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Please log in']);
    exit;
}

// Load database config
if (!file_exists('../../api/db_config.php')) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database config not found']);
    exit;
}

require_once '../../api/db_config.php';

// Verify connection
if (!isset($conn) || !$conn) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if survey_responses table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'survey_responses'");
if (!$tableCheck || $tableCheck->num_rows == 0) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Survey responses table not found',
        'setup_url' => '../setup_responses.php'
    ]);
    exit;
}

// Custom error handler to catch PHP errors/warnings
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $errstr,
        'error' => [
            'file' => $errfile,
            'line' => $errline
        ]
    ]);
    exit;
});

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';

    switch($action) {
        case 'list':
            listResponses();
            break;
        case 'get':
            getResponse();
            break;
        case 'delete':
            deleteResponse();
            break;
        case 'export':
            exportResponses();
            break;
        default:
            throw new Exception('Invalid action');
    }

} catch(Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

function listResponses() {
    global $conn;

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter_date = isset($_GET['date']) ? trim($_GET['date']) : '';

    // Build query
    $where = "1=1";
    $params = [];
    $types = "";

    if (!empty($search)) {
        $where .= " AND (visitor_email LIKE ? OR visitor_name LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= "ss";
    }

    if (!empty($filter_date)) {
        $where .= " AND DATE(created_at) = ?";
        $params[] = $filter_date;
        $types .= "s";
    }

    // Get total count
    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM survey_responses WHERE $where");
    if ($types && !empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    if (!$countStmt->execute()) {
        throw new Exception("Query failed: " . $countStmt->error);
    }
    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();
    $totalRows = $countRow ? $countRow['total'] : 0;
    $countStmt->close();

    // Get responses - add pagination params to array
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    // Use simple query without responses_data (will be merged when viewing detail)
    $stmt = $conn->prepare("
        SELECT id, visitor_email, visitor_name, created_at
        FROM survey_responses
        WHERE $where
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if ($types && !empty($params)) {
        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("Bind param failed: " . $stmt->error);
        }
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $responses = [];

    while($row = $result->fetch_assoc()) {
        $responses[] = [
            'id' => $row['id'],
            'email' => $row['visitor_email'],
            'visitor_name' => $row['visitor_name'],
            'submitted_at' => $row['created_at'],
            'answer_count' => 4  // Basic fields count
        ];
    }

    $stmt->close();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'data' => $responses,
        'total' => $totalRows,
        'limit' => $limit,
        'offset' => $offset
    ]);
    exit;
}

function getResponse() {
    global $conn;

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$id) {
        throw new Exception('Response ID is required');
    }

    // Check if responses_data column exists
    $columnCheck = $conn->query("SHOW COLUMNS FROM survey_responses LIKE 'responses_data'");
    $hasResponsesData = $columnCheck && $columnCheck->num_rows > 0;

    // Build query based on column existence
    $selectFields = "id, visitor_name, visitor_email, visit_frequency, purpose, created_at";
    if ($hasResponsesData) {
        $selectFields .= ", responses_data";
    }

    $stmt = $conn->prepare("
        SELECT $selectFields
        FROM survey_responses
        WHERE id = ?
    ");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if (!$stmt->bind_param("i", $id)) {
        throw new Exception("Bind param failed: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt->close();
        throw new Exception('Response not found');
    }

    $row = $result->fetch_assoc();
    $stmt->close();

    // Build response object from individual fields plus dynamic responses
    $responses = [
        'visitor_name' => $row['visitor_name'],
        'visitor_email' => $row['visitor_email'],
        'visit_frequency' => $row['visit_frequency'],
        'purpose' => $row['purpose']
    ];

    // Parse and merge responses_data JSON if present and column exists
    if ($hasResponsesData && !empty($row['responses_data'])) {
        $decoded = json_decode($row['responses_data'], true);
        if (is_array($decoded)) {
            $responses = array_merge($responses, $decoded);
        }
    }

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $row['id'],
            'email' => $row['visitor_email'],
            'submitted_at' => $row['created_at'],
            'responses' => $responses
        ]
    ]);
    exit;
}

function deleteResponse() {
    global $conn;

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (!$id) {
        throw new Exception('Response ID is required');
    }

    $stmt = $conn->prepare("DELETE FROM survey_responses WHERE id = ?");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if (!$stmt->bind_param("i", $id)) {
        throw new Exception("Bind param failed: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows == 0) {
        $stmt->close();
        throw new Exception('Response not found');
    }

    $stmt->close();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Response deleted successfully'
    ]);
    exit;
}

function exportResponses() {
    global $conn;

    $filter_date = isset($_GET['date']) ? trim($_GET['date']) : '';

    $where = "1=1";
    $params = [];
    $types = "";

    if (!empty($filter_date)) {
        $where .= " AND DATE(created_at) = ?";
        $params[] = $filter_date;
        $types .= "s";
    }

    $stmt = $conn->prepare("
        SELECT visitor_name, visitor_email, visit_frequency, purpose,
               satisfaction, book_availability, staff_helpfulness, facilities_rating,
               would_recommend, improvements_feedback, created_at
        FROM survey_responses
        WHERE $where
        ORDER BY created_at DESC
    ");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if ($types && !empty($params)) {
        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("Bind param failed: " . $stmt->error);
        }
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    // Generate CSV
    $filename = 'survey_responses_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $output = fopen('php://output', 'w');

    // Write header
    fputcsv($output, [
        'Name',
        'Email',
        'Visit Frequency',
        'Purpose',
        'Satisfaction',
        'Book Availability',
        'Staff Helpfulness',
        'Facilities Rating',
        'Would Recommend',
        'Improvements',
        'Submitted At'
    ]);

    while($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['visitor_name'],
            $row['visitor_email'],
            $row['visit_frequency'],
            $row['purpose'],
            $row['satisfaction'],
            $row['book_availability'],
            $row['staff_helpfulness'],
            $row['facilities_rating'],
            $row['would_recommend'],
            $row['improvements_feedback'],
            $row['created_at']
        ]);
    }

    fclose($output);
    $stmt->close();
    exit;
}
?>
