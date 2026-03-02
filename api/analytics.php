<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $action = $_GET['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Action parameter required');
    }
    
    switch ($action) {
        case 'total_responses':
            $result = $conn->query("SELECT COUNT(*) as total FROM survey_responses");
            $data = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'average_ratings':
            $result = $conn->query(
                "SELECT 
                    ROUND(AVG(satisfaction), 2) as avg_satisfaction,
                    ROUND(AVG(book_availability), 2) as avg_book_availability,
                    ROUND(AVG(staff_helpfulness), 2) as avg_staff_helpfulness,
                    ROUND(AVG(facilities_rating), 2) as avg_facilities,
                    ROUND(AVG(would_recommend), 2) as avg_recommendation
                FROM survey_responses"
            );
            $data = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'visit_frequency':
            $result = $conn->query(
                "SELECT 
                    visit_frequency,
                    COUNT(*) as count
                FROM survey_responses
                GROUP BY visit_frequency
                ORDER BY count DESC"
            );
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'satisfaction_distribution':
            $result = $conn->query(
                "SELECT 
                    satisfaction,
                    COUNT(*) as count
                FROM survey_responses
                GROUP BY satisfaction
                ORDER BY satisfaction"
            );
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'daily_submissions':
            $result = $conn->query(
                "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as count
                FROM survey_responses
                GROUP BY DATE(created_at)
                ORDER BY date DESC
                LIMIT 30"
            );
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'recommendation_breakdown':
            $labels = ['Definitely Not', 'Probably Not', 'Neutral', 'Probably Yes', 'Definitely Yes'];
            $result = $conn->query(
                "SELECT 
                    would_recommend,
                    COUNT(*) as count
                FROM survey_responses
                GROUP BY would_recommend
                ORDER BY would_recommend"
            );
            $data = $result->fetch_all(MYSQLI_ASSOC);
            
            $formatted_data = [];
            for ($i = 0; $i < 5; $i++) {
                $count = 0;
                foreach ($data as $row) {
                    if ((int)$row['would_recommend'] === $i) {
                        $count = (int)$row['count'];
                        break;
                    }
                }
                $formatted_data[] = [
                    'label' => $labels[$i],
                    'value' => $i,
                    'count' => $count
                ];
            }
            echo json_encode(['success' => true, 'data' => $formatted_data]);
            break;
            
        case 'all_responses':
            $limit = intval($_GET['limit'] ?? 100);
            $offset = intval($_GET['offset'] ?? 0);
            
            $result = $conn->query(
                "SELECT 
                    id,
                    visitor_name,
                    visitor_email,
                    visit_frequency,
                    purpose,
                    satisfaction,
                    book_availability,
                    staff_helpfulness,
                    facilities_rating,
                    would_recommend,
                    improvements_feedback,
                    created_at
                FROM survey_responses
                ORDER BY created_at DESC
                LIMIT $limit OFFSET $offset"
            );
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'response_detail':
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Invalid response ID');
            }
            
            $result = $conn->query(
                "SELECT * FROM survey_responses WHERE id = $id"
            );
            $data = $result->fetch_assoc();
            
            if (!$data) {
                throw new Exception('Response not found');
            }
            
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        default:
            throw new Exception('Unknown action: ' . $action);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
