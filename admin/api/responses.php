<?php
/**
 * Admin API - Survey Responses Management
 * Handles fetching, filtering, exporting responses and analytics
 */

session_start();
header('Content-Type: application/json');

// Authentication check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Include database configuration
require_once '../../api/db_config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if ($action === 'list' || empty($action)) {
            listResponses();
        } elseif ($action === 'get') {
            getResponse($_GET['id'] ?? 0);
        } elseif ($action === 'analytics') {
            getAnalytics();
        } elseif ($action === 'export') {
            exportResponses();
        }
    } elseif ($method === 'DELETE') {
        deleteResponse($_GET['id'] ?? 0);
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

/**
 * List all responses with pagination and filtering
 */
function listResponses() {
    global $conn;
    
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(100, max(10, (int)($_GET['limit'] ?? 50)));
    $offset = ($page - 1) * $limit;
    $search = trim($_GET['search'] ?? '');
    $date_from = trim($_GET['date_from'] ?? '');
    $date_to = trim($_GET['date_to'] ?? '');
    
    // Build WHERE clause
    $where = "1=1";
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $where .= " AND (visitor_name LIKE ? OR visitor_email LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= "ss";
    }
    
    if (!empty($date_from)) {
        $where .= " AND DATE(created_at) >= ?";
        $params[] = $date_from;
        $types .= "s";
    }
    
    if (!empty($date_to)) {
        $where .= " AND DATE(created_at) <= ?";
        $params[] = $date_to;
        $types .= "s";
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM survey_responses WHERE $where";
    $countStmt = $conn->prepare($countQuery);
    if ($types && count($params) < 10) { // Avoid bind issues
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $countRow = $countStmt->get_result()->fetch_assoc();
    $total = $countRow['total'] ?? 0;
    $countStmt->close();
    
    // Get responses
    $query = "SELECT id, visitor_name, visitor_email, visit_frequency, purpose, created_at 
              FROM survey_responses 
              WHERE $where 
              ORDER BY created_at DESC 
              LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($query);
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $responses = [];
    while ($row = $result->fetch_assoc()) {
        $responses[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'responses' => $responses,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
    $stmt->close();
}

/**
 * Get single response details
 */
function getResponse($id) {
    global $conn;

    $id = isset($id) ? (int)$id : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

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

function deleteResponse($id) {
    global $conn;

    $id = isset($id) ? (int)$id : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

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

/**
 * Get analytics data
 */
function getAnalytics() {
    global $conn;
    
    // Total responses
    $totalResult = $conn->query("SELECT COUNT(*) as total FROM survey_responses");
    $total = $totalResult->fetch_assoc()['total'];
    
    // Today's responses
    $todayResult = $conn->query("SELECT COUNT(*) as count FROM survey_responses WHERE DATE(created_at) = CURDATE()");
    $today = $todayResult->fetch_assoc()['count'];
    
    // This week
    $weekResult = $conn->query("SELECT COUNT(*) as count FROM survey_responses WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $week = $weekResult->fetch_assoc()['count'];
    
    // Visit frequency breakdown
    $frequencyResult = $conn->query("SELECT visit_frequency, COUNT(*) as count FROM survey_responses GROUP BY visit_frequency");
    $frequency = [];
    while ($row = $frequencyResult->fetch_assoc()) {
        $frequency[] = $row;
    }
    
    // Purpose breakdown
    $purposeResult = $conn->query("SELECT purpose, COUNT(*) as count FROM survey_responses GROUP BY purpose");
    $purpose = [];
    while ($row = $purposeResult->fetch_assoc()) {
        $purpose[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'analytics' => [
            'total_responses' => $total,
            'today_responses' => $today,
            'week_responses' => $week,
            'visit_frequency' => $frequency,
            'purpose_breakdown' => $purpose
        ]
    ]);
}
