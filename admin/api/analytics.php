<?php
/**
 * Admin Analytics API
 */

session_start();
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Check admin authentication
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Load database config
require_once '../../api/db_config.php';

if (!isset($conn) || !$conn) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Get analytics data
    $analytics = [
        'total_responses' => getTotalResponses(),
        'today_responses' => getTodayResponses(),
        'total_questions' => getTotalQuestions(),
        'satisfaction_stats' => getSatisfactionStats(),
        'visit_frequency' => getVisitFrequency(),
        'visit_purpose' => getVisitPurpose(),
        'recommendation_rate' => getRecommendationRate(),
        'ratings_breakdown' => getRatingsBreakdown()
    ];

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'data' => $analytics
    ]);
    exit;

} catch(Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

function getTotalResponses() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM survey_responses");
    $row = $result->fetch_assoc();
    return (int)$row['count'];
}

function getTodayResponses() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM survey_responses WHERE DATE(created_at) = CURDATE()");
    $row = $result->fetch_assoc();
    return (int)$row['count'];
}

function getTotalQuestions() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM survey_questions");
    $row = $result->fetch_assoc();
    return (int)$row['count'];
}

function getSatisfactionStats() {
    global $conn;
    $result = $conn->query("
        SELECT 
            AVG(satisfaction) as average,
            MIN(satisfaction) as minimum,
            MAX(satisfaction) as maximum,
            COUNT(*) as count
        FROM survey_responses
    ");
    $row = $result->fetch_assoc();
    return [
        'average' => round((float)$row['average'], 2),
        'minimum' => (int)$row['minimum'],
        'maximum' => (int)$row['maximum'],
        'count' => (int)$row['count']
    ];
}

function getVisitFrequency() {
    global $conn;
    $result = $conn->query("
        SELECT visit_frequency, COUNT(*) as count
        FROM survey_responses
        GROUP BY visit_frequency
        ORDER BY count DESC
    ");
    
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = [
            'label' => $row['visit_frequency'] ?? 'Not specified',
            'value' => (int)$row['count']
        ];
    }
    return $data;
}

function getVisitPurpose() {
    global $conn;
    $result = $conn->query("
        SELECT purpose, COUNT(*) as count
        FROM survey_responses
        WHERE purpose IS NOT NULL AND purpose != ''
        GROUP BY purpose
        ORDER BY count DESC
        LIMIT 10
    ");
    
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = [
            'label' => $row['purpose'],
            'value' => (int)$row['count']
        ];
    }
    return $data;
}

function getRecommendationRate() {
    global $conn;
    $result = $conn->query("
        SELECT 
            SUM(would_recommend = 1) as recommend_yes,
            SUM(would_recommend = 0) as recommend_no,
            COUNT(*) as total
        FROM survey_responses
    ");
    $row = $result->fetch_assoc();
    
    $yes = (int)$row['recommend_yes'];
    $no = (int)$row['recommend_no'];
    $total = (int)$row['total'];
    
    return [
        'yes' => $yes,
        'no' => $no,
        'total' => $total,
        'percentage' => $total > 0 ? round(($yes / $total) * 100, 1) : 0
    ];
}

function getRatingsBreakdown() {
    global $conn;
    
    // Since questions are now dynamic, we'll look for numeric rating questions in responses_data
    // This is a fallback if old hardcoded columns exist, otherwise we'll return empty
    
    // Check if old columns exist
    $columnsResult = $conn->query("SHOW COLUMNS FROM survey_responses LIKE 'book_availability'");
    
    if ($columnsResult && $columnsResult->num_rows > 0) {
        // Old columns exist, use them for backward compatibility
        $result = $conn->query("
            SELECT 
                ROUND(AVG(CAST(book_availability AS DECIMAL)), 1) as book_availability,
                ROUND(AVG(CAST(staff_helpfulness AS DECIMAL)), 1) as staff_helpfulness,
                ROUND(AVG(CAST(facilities_rating AS DECIMAL)), 1) as facilities_rating
            FROM survey_responses
        ");
        $row = $result->fetch_assoc();
        
        return [
            'book_availability' => (float)($row['book_availability'] ?? 0),
            'staff_helpfulness' => (float)($row['staff_helpfulness'] ?? 0),
            'facilities_rating' => (float)($row['facilities_rating'] ?? 0)
        ];
    } else {
        // Return empty or default values for new dynamic system
        return [
            'book_availability' => 0,
            'staff_helpfulness' => 0,
            'facilities_rating' => 0
        ];
    }
}
?>
