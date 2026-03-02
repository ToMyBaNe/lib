<?php
/**
 * Public API - Get Survey Questions
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../api/db_config.php';

try {
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection failed');
    }

    // Get all active questions ordered by category
    $result = $conn->query("
        SELECT 
            q.id, 
            q.category_id,
            q.question,
            q.question_type,
            q.options,
            q.is_required,
            c.category_name
        FROM survey_questions q
        LEFT JOIN survey_categories c ON q.category_id = c.id
        WHERE q.is_active = 1
        ORDER BY q.category_id, q.id
    ");

    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
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

        $question = [
            'id' => (int)$row['id'],
            'question' => $row['question'],
            'type' => $row['question_type'],
            'required' => (bool)$row['is_required'],
            'options' => $options,
            'category' => $row['category_name'] ?? 'General'
        ];

        $questions[] = $question;

        // Also organize by category
        $category = $row['category_name'] ?? 'General';
        if (!isset($categorized[$category])) {
            $categorized[$category] = [];
        }
        $categorized[$category][] = $question;
    }

    echo json_encode([
        'success' => true,
        'questions' => $questions,
        'categorized' => $categorized,
        'count' => count($questions)
    ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
