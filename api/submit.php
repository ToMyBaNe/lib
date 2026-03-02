<?php
/**
 * Public API - Submit Survey Response
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

require_once '../api/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection failed');
    }

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('No data provided');
    }

    // Validate required fields
    $visitor_name = trim($data['visitor_name'] ?? '');
    $visitor_email = trim($data['visitor_email'] ?? '');
    $visit_frequency = trim($data['visit_frequency'] ?? '');
    $purpose = trim($data['purpose'] ?? '');
    $responses = $data['responses'] ?? [];

    if (empty($visitor_name)) {
        throw new Exception('Visitor name is required');
    }

    if (empty($visit_frequency)) {
        throw new Exception('Visit frequency is required');
    }

    if (empty($purpose)) {
        throw new Exception('Purpose is required');
    }

    // Store responses as JSON
    $responses_json = json_encode($responses);

    // Prepare insert statement
    $stmt = $conn->prepare("
        INSERT INTO survey_responses (
            visitor_name, 
            visitor_email, 
            visit_frequency, 
            purpose,
            responses_data,
            created_at
        ) VALUES (?, ?, ?, ?, ?, NOW())
    ");

    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("sssss", $visitor_name, $visitor_email, $visit_frequency, $purpose, $responses_json);

    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $response_id = $stmt->insert_id;
    $stmt->close();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Survey response submitted successfully',
        'response_id' => $response_id
    ]);

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
