<?php
/**
 * Admin API for Survey Questions CRUD
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

// Load database config with error handling
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

// Check if survey_questions table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'survey_questions'");
if (!$tableCheck || $tableCheck->num_rows == 0) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Survey questions table not found. Please run setup_questions.php first',
        'setup_url' => '../setup_questions.php'
    ]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

if (!$action) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Action parameter required']);
    exit;
}

try {
    switch ($action) {
        case 'list':
            listQuestions();
            break;
        case 'get':
            getQuestion();
            break;
        case 'create':
            if ($method !== 'POST') throw new Exception('POST required');
            createQuestion();
            break;
        case 'update':
            if ($method !== 'POST') throw new Exception('POST required');
            updateQuestion();
            break;
        case 'delete':
            if ($method !== 'POST') throw new Exception('POST required');
            deleteQuestion();
            break;
        case 'reorder':
            if ($method !== 'POST') throw new Exception('POST required');
            reorderQuestions();
            break;
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

function listQuestions() {
    global $conn;
    
    $query = "SELECT * FROM survey_questions WHERE is_active = 1 ORDER BY display_order ASC";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Database error: ' . $conn->error);
    }
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['options']) {
            $row['options'] = json_decode($row['options'], true);
        }
        $questions[] = $row;
    }
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $questions,
        'count' => count($questions)
    ]);
    exit;
}

function getQuestion() {
    global $conn;
    
    $id = intval($_GET['id'] ?? 0);
    if (!$id) throw new Exception('Question ID required');
    
    $stmt = $conn->prepare("SELECT * FROM survey_questions WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $question = $result->fetch_assoc();
    
    if (!$question) {
        throw new Exception('Question not found');
    }
    
    if ($question['options']) {
        $question['options'] = json_decode($question['options'], true);
    }
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $question
    ]);
    exit;
}

function createQuestion() {
    global $conn;
    
    $question_text = trim($_POST['question_text'] ?? '');
    $question_type = trim($_POST['question_type'] ?? 'text');
    $category = trim($_POST['category'] ?? '');
    $required = isset($_POST['required']) ? 1 : 0;
    $options = isset($_POST['options']) ? json_encode($_POST['options']) : null;
    
    if (empty($question_text)) {
        throw new Exception('Question text is required');
    }
    
    if (!in_array($question_type, ['text', 'rating', 'select', 'checkbox'])) {
        throw new Exception('Invalid question type');
    }
    
    // Get next display order
    $result = $conn->query("SELECT MAX(display_order) as max_order FROM survey_questions");
    $row = $result->fetch_assoc();
    $display_order = ($row['max_order'] ?? 0) + 1;
    
    $stmt = $conn->prepare(
        "INSERT INTO survey_questions (question_text, question_type, category, required, options, display_order) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    $stmt->bind_param('sssisi', $question_text, $question_type, $category, $required, $options, $display_order);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create question: ' . $stmt->error);
    }
    
    $id = $conn->insert_id;
    
    ob_end_clean();
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Question created successfully',
        'id' => $id
    ]);
    exit;
}

function updateQuestion() {
    global $conn;
    
    $id = intval($_POST['id'] ?? 0);
    if (!$id) throw new Exception('Question ID required');
    
    $question_text = trim($_POST['question_text'] ?? '');
    $question_type = trim($_POST['question_type'] ?? 'text');
    $category = trim($_POST['category'] ?? '');
    $required = isset($_POST['required']) ? 1 : 0;
    $options = isset($_POST['options']) ? json_encode($_POST['options']) : null;
    
    if (empty($question_text)) {
        throw new Exception('Question text is required');
    }
    
    if (!in_array($question_type, ['text', 'rating', 'select', 'checkbox'])) {
        throw new Exception('Invalid question type');
    }
    
    $stmt = $conn->prepare(
        "UPDATE survey_questions SET question_text = ?, question_type = ?, category = ?, required = ?, options = ? 
         WHERE id = ?"
    );
    
    $stmt->bind_param('sssisi', $question_text, $question_type, $category, $required, $options, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update question: ' . $stmt->error);
    }
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Question updated successfully'
    ]);
    exit;
}

function deleteQuestion() {
    global $conn;
    
    $id = intval($_POST['id'] ?? 0);
    if (!$id) throw new Exception('Question ID required');
    
    // Soft delete
    $stmt = $conn->prepare("UPDATE survey_questions SET is_active = 0 WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete question: ' . $stmt->error);
    }
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Question deleted successfully'
    ]);
    exit;
}

function reorderQuestions() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $orders = $data['orders'] ?? [];
    
    if (empty($orders)) {
        throw new Exception('Order data required');
    }
    
    foreach ($orders as $order => $id) {
        $stmt = $conn->prepare("UPDATE survey_questions SET display_order = ? WHERE id = ?");
        $stmt->bind_param('ii', $order, $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to reorder questions');
        }
    }
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Questions reordered successfully'
    ]);
    exit;
}
?>
