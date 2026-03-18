<?php
/**
 * Admin API - Survey Responses Management
 * Handles fetching, filtering, exporting responses and analytics
 */

session_start();

$action = $_GET['action'] ?? '';

// Set content type header based on action
if ($action === 'export') {
    // Don't set JSON header for export (will use CSV header)
} else {
    header('Content-Type: application/json');
}

// Authentication check
if (!isset($_SESSION['user_id'])) {
    if ($action === 'export') {
        http_response_code(401);
        exit;
    }
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Include database configuration
require_once '../../api/db_config.php';

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

    // Fetch all active survey questions ordered by display_order
    $questionsStmt = $conn->prepare("
        SELECT id, question, question_type
        FROM survey_questions
        WHERE is_active = 1
        ORDER BY display_order ASC
    ");

    if (!$questionsStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if (!$questionsStmt->execute()) {
        throw new Exception("Execute failed: " . $questionsStmt->error);
    }

    $questionsResult = $questionsStmt->get_result();
    $questions = [];
    $questionIds = [];

    while ($q = $questionsResult->fetch_assoc()) {
        $questions[] = $q;
        $questionIds[] = $q['id'];
    }
    $questionsStmt->close();

    // Fetch responses
    $stmt = $conn->prepare("
        SELECT visitor_name, visitor_email, visit_frequency, purpose, responses_data, created_at
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
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Write BOM for UTF-8 (helps with Excel)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Build CSV header with visitor info + all questions
    $headers = ['Name', 'Email', 'Visit Frequency', 'Purpose'];
    foreach ($questions as $question) {
        $headers[] = $question['question'];
    }
    $headers[] = 'Submitted At';

    fputcsv($output, $headers);

    // Collect all responses and calculate averages
    $allResponses = [];
    $questionAverages = array_fill_keys($questionIds, []);
    
    while($row = $result->fetch_assoc()) {
        // Decode JSON responses
        $responses = [];
        if (!empty($row['responses_data'])) {
            $decoded = json_decode($row['responses_data'], true);
            if (is_array($decoded)) {
                $responses = $decoded;
            }
        }

        // Build row with visitor info + answers for each question
        $rowData = [
            $row['visitor_name'],
            $row['visitor_email'],
            $row['visit_frequency'],
            $row['purpose']
        ];

        // Add answer for each question and collect numeric values for averaging
        foreach ($questions as $question) {
            $qId = $question['id'];
            $answer = isset($responses[$qId]) ? $responses[$qId] : '';
            $rowData[] = $answer;

            // Collect numeric values for rating questions
            if ($question['question_type'] === 'rating' && is_numeric($answer)) {
                $questionAverages[$qId][] = (float)$answer;
            }
        }

        $rowData[] = $row['created_at'];
        $allResponses[] = $rowData;

        fputcsv($output, $rowData);
    }

    // Add blank row
    fputcsv($output, []);

    // Calculate and add averages row
    $averageData = ['AVERAGE', '', '', ''];
    
    foreach ($questions as $question) {
        $qId = $question['id'];
        if (!empty($questionAverages[$qId])) {
            $avg = array_sum($questionAverages[$qId]) / count($questionAverages[$qId]);
            // Check if this is the satisfaction question
            if (stripos($question['question'], 'satisfaction') !== false) {
                $averageData[] = round($avg, 2) . ' (Satisfaction Average)';
            } else {
                $averageData[] = round($avg, 2);
            }
        } else {
            $averageData[] = '-';
        }
    }
    
    $averageData[] = '';
    fputcsv($output, $averageData);

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
