<?php
/**
 * Admin API - Survey Questions Management
 * Handles CRUD operations for survey questions
 */

session_start();
header('Content-Type: application/json');

// Authentication check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../api/db_config.php';

// Verify tables exist
$tables = $conn->query("SHOW TABLES LIKE 'survey_questions'");
if (!$tables || $tables->num_rows === 0) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database tables not found']);
    exit;
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if ($action === 'list' || empty($action)) {
            getAllQuestions();
        } elseif ($action === 'get') {
            getQuestion($_GET['id'] ?? 0);
        } elseif ($action === 'categories') {
            getCategories();
        }
    } elseif ($method === 'POST') {
        if ($action === 'create') {
            createQuestion();
        } elseif ($action === 'update') {
            updateQuestion($_GET['id'] ?? 0);
        } elseif ($action === 'reorder') {
            reorderQuestions();
        }
    } elseif ($method === 'DELETE') {
        deleteQuestion($_GET['id'] ?? 0);
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
 * Get all questions with categories
 */
function getAllQuestions() {
    global $conn;
    
    $query = "SELECT 
                q.id,
                q.category_id,
                c.category_name,
                q.question,
                q.question_type,
                q.options,
                q.is_required,
                q.display_order,
                q.is_active,
                q.created_at
              FROM survey_questions q
              LEFT JOIN survey_categories c ON q.category_id = c.id
              ORDER BY c.display_order ASC, q.display_order ASC";
    
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $row['options'] = $row['options'] ? json_decode($row['options'], true) : [];
        $row['is_required'] = (bool)$row['is_required'];
        $row['is_active'] = (bool)$row['is_active'];
        $questions[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'questions' => $questions,
        'count' => count($questions)
    ]);
}

/**
 * Get single question
 */
function getQuestion($id) {
    global $conn;
    
    $id = (int)$id;
    if (!$id) {
        throw new Exception('Question ID required');
    }
    
    $stmt = $conn->prepare("SELECT q.*, c.category_name FROM survey_questions q 
                           LEFT JOIN survey_categories c ON q.category_id = c.id
                           WHERE q.id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Question not found');
    }
    
    $question = $result->fetch_assoc();
    $question['options'] = $question['options'] ? json_decode($question['options'], true) : [];
    $question['is_required'] = (bool)$question['is_required'];
    $question['is_active'] = (bool)$question['is_active'];
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'question' => $question
    ]);
    $stmt->close();
}

/**
 * Get all categories
 */
function getCategories() {
    global $conn;
    
    $result = $conn->query("SELECT id, category_name, display_order FROM survey_categories ORDER BY display_order ASC");
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
}

/**
 * Create new question
 */
function createQuestion() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $category_id = $data['category_id'] ?? null;
    $question = trim($data['question'] ?? '');
    $question_type = trim($data['question_type'] ?? 'text');
    $options = $data['options'] ?? [];
    $is_required = $data['is_required'] ? 1 : 0;
    $is_active = $data['is_active'] ?? 1;
    
    if (empty($question)) {
        throw new Exception('Question text is required');
    }
    
    if (!in_array($question_type, ['text', 'textarea', 'select', 'radio', 'checkbox', 'rating'])) {
        throw new Exception('Invalid question type');
    }
    
    $options_json = json_encode($options);
    
    // Get next display order for this category
    $orderQuery = $conn->prepare("SELECT MAX(display_order) as max_order FROM survey_questions WHERE category_id <=> ?");
    $orderQuery->bind_param('i', $category_id);
    $orderQuery->execute();
    $orderResult = $orderQuery->get_result()->fetch_assoc();
    $display_order = ($orderResult['max_order'] ?? 0) + 1;
    $orderQuery->close();
    
    $stmt = $conn->prepare("INSERT INTO survey_questions 
                           (category_id, question, question_type, options, is_required, is_active, display_order) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param('issssii', $category_id, $question, $question_type, $options_json, $is_required, $is_active, $display_order);
    
    if (!$stmt->execute()) {
        throw new Exception('Insert failed: ' . $stmt->error);
    }
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Question created successfully',
        'id' => $conn->insert_id
    ]);
    $stmt->close();
}

/**
 * Update question
 */
function updateQuestion($id) {
    global $conn;
    
    $id = (int)$id;
    if (!$id) {
        throw new Exception('Question ID required');
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $category_id = $data['category_id'] ?? null;
    $question = trim($data['question'] ?? '');
    $question_type = trim($data['question_type'] ?? 'text');
    $options = $data['options'] ?? [];
    $is_required = $data['is_required'] ? 1 : 0;
    $is_active = $data['is_active'] ? 1 : 0;
    
    if (empty($question)) {
        throw new Exception('Question text is required');
    }
    
    if (!in_array($question_type, ['text', 'textarea', 'select', 'radio', 'checkbox', 'rating'])) {
        throw new Exception('Invalid question type');
    }
    
    $options_json = json_encode($options);
    
    $stmt = $conn->prepare("UPDATE survey_questions 
                           SET category_id = ?, question = ?, question_type = ?, options = ?, is_required = ?, is_active = ?
                           WHERE id = ?");
    
    $stmt->bind_param('issssii', $category_id, $question, $question_type, $options_json, $is_required, $is_active, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Update failed: ' . $stmt->error);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Question updated successfully'
    ]);
    $stmt->close();
}

/**
 * Delete question
 */
function deleteQuestion($id) {
    global $conn;
    
    $id = (int)$id;
    if (!$id) {
        throw new Exception('Question ID required');
    }
    
    $stmt = $conn->prepare("DELETE FROM survey_questions WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Delete failed: ' . $stmt->error);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Question deleted successfully'
    ]);
    $stmt->close();
}

/**
 * Reorder questions
 */
function reorderQuestions() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $orders = $data['orders'] ?? [];
    
    if (empty($orders)) {
        throw new Exception('No order data provided');
    }
    
    foreach ($orders as $item) {
        $id = (int)$item['id'];
        $display_order = (int)$item['display_order'];
        
        $stmt = $conn->prepare("UPDATE survey_questions SET display_order = ? WHERE id = ?");
        $stmt->bind_param('ii', $display_order, $id);
        $stmt->execute();
        $stmt->close();
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Questions reordered successfully'
    ]);
}
?>
