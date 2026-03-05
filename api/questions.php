<?php
/**
 * Public API - Get Survey Questions
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly, log them

require_once '../api/db_config.php';

try {
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection failed');
    }

    // Check if tables exist
    $tableCheck = $conn->query("SHOW TABLES LIKE 'survey_questions'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        throw new Exception('survey_questions table not found. Please run setup_questions.php');
    }

    // Check if survey_categories table exists
    $categoryTableCheck = $conn->query("SHOW TABLES LIKE 'survey_categories'");
    $hasCategoryTable = $categoryTableCheck && $categoryTableCheck->num_rows > 0;

    // Build query based on what tables exist
    if ($hasCategoryTable) {
        $query = "
            SELECT 
                q.id, 
                q.category_id,
                q.question,
                q.question_type,
                q.options,
                q.is_required,
                COALESCE(c.category_name, 'General') as category_name,
                COALESCE(c.display_order, 999) as category_order,
                COALESCE(q.display_order, q.id) as question_order
            FROM survey_questions q
            LEFT JOIN survey_categories c ON q.category_id = c.id
            WHERE q.is_active = 1
            ORDER BY category_order, question_order, q.id
        ";
    } else {
        // Fallback if survey_categories doesn't exist
        $query = "
            SELECT 
                q.id, 
                q.category_id,
                q.question,
                q.question_type,
                q.options,
                q.is_required,
                'General' AS category_name,
                999 as category_order,
                COALESCE(q.display_order, q.id) as question_order
            FROM survey_questions q
            WHERE q.is_active = 1
            ORDER BY question_order, q.id
        ";
    }

    $result = $conn->query($query);

    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error . ' | Query: ' . $query);
    }

    $questions = [];
    $categorized = [];

    while($row = $result->fetch_assoc()) {
        // Parse JSON options if present
        $options = [];
        if (!empty($row['options'])) {
            $decoded = json_decode($row['options'], true);
            if (is_array($decoded)) {
                $options = $decoded;
            }
        }

        // Validate required fields
        if (empty($row['id']) || empty($row['question'])) {
            error_log('Invalid question data: ' . json_encode($row));
            continue; // Skip invalid questions
        }

        $question = [
            'id' => (int)$row['id'],
            'question' => (string)($row['question'] ?? ''),
            'type' => (string)($row['question_type'] ?? 'text'),
            'required' => (bool)$row['is_required'],
            'options' => $options,
            'category' => (string)($row['category_name'] ?? 'General')
        ];

        $questions[] = $question;

        // Also organize by category - ensure category name is never empty
        $category = (string)($row['category_name'] ?? 'General');
        if (empty($category)) {
            $category = 'General';
        }
        
        if (!isset($categorized[$category])) {
            $categorized[$category] = [];
        }
        $categorized[$category][] = $question;
    }

    // Check if we got any questions
    if (empty($questions)) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'questions' => [],
            'categorized' => [],
            'count' => 0,
            'warning' => 'No active questions found. Please create questions in the admin panel.'
        ]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'questions' => $questions,
        'categorized' => $categorized,
        'count' => count($questions)
    ]);

} catch(Exception $e) {
    http_response_code(500);
    error_log('Questions API Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => 'Please check the troubleshooting page or run setup_questions.php'
    ]);
}
?>
