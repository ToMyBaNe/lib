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

    // Validate and sanitize input data
    $visitor_name = trim($data['visitor_name'] ?? '');
    $visitor_email = trim($data['visitor_email'] ?? '');
    $visit_frequency = trim($data['visit_frequency'] ?? '');
    $purpose = trim($data['purpose'] ?? '');
    $responses = $data['responses'] ?? [];

    // Required field validation
    // if (empty($visitor_name)) {
    //     throw new Exception('Visitor name is required');
    // }

    // if (strlen($visitor_name) > 100) {
    //     throw new Exception('Visitor name must not exceed 100 characters');
    // }

    // if (!empty($visitor_email)) {
    //     if (!filter_var($visitor_email, FILTER_VALIDATE_EMAIL)) {
    //         throw new Exception('Please provide a valid email address');
    //     }
    //     if (strlen($visitor_email) > 100) {
    //         throw new Exception('Email must not exceed 100 characters');
    //     }
    // }

    // if (empty($visit_frequency)) {
    //     throw new Exception('Visit frequency is required');
    // }

    // if (empty($purpose)) {
    //     throw new Exception('Purpose is required');
    // }

    // Get client IP address
    $ip_address = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // Store responses as JSON
    $responses_json = json_encode($responses);
    if ($responses_json === false) {
        throw new Exception('Failed to encode response data');
    }

    // Prepare insert statement with additional metadata fields
    $stmt = $conn->prepare("
        INSERT INTO survey_responses (
            visitor_name, 
            visitor_email, 
            visit_frequency, 
            purpose,
            responses_data,
            ip_address,
            user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("sssssss", $visitor_name, $visitor_email, $visit_frequency, $purpose, $responses_json, $ip_address, $user_agent);

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
