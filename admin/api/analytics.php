<?php
/**
 * Admin Analytics API (cleaned)
 * Returns JSON with dashboard metrics. Computes ratings per question dynamically.
 */

session_start();
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Simple auth check
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../api/db_config.php';

if (!isset($conn) || !$conn) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

function hasColumn($conn, $column) {
    $r = $conn->query("SHOW COLUMNS FROM survey_responses LIKE '" . $conn->real_escape_string($column) . "'");
    return $r && $r->num_rows > 0;
}

function getTotalResponses($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM survey_responses");
    if (!$result) return 0;
    $row = $result->fetch_assoc();
    return (int)($row['count'] ?? 0);
}

function getTodayResponses($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM survey_responses WHERE DATE(created_at) = CURDATE()");
    if (!$result) return 0;
    $row = $result->fetch_assoc();
    return (int)($row['count'] ?? 0);
}

function getTotalRespondents($conn) {
    $result = $conn->query("SELECT COUNT(DISTINCT visitor_email) as count FROM survey_responses");
    if (!$result) return 0;
    $row = $result->fetch_assoc();
    return (int)($row['count'] ?? 0);
}

function getSatisfactionStats($conn) {
    if (hasColumn($conn, 'satisfaction')) {
        $result = $conn->query(
            "SELECT AVG(satisfaction) as average, MIN(satisfaction) as minimum, MAX(satisfaction) as maximum, COUNT(*) as count FROM survey_responses"
        );
        if ($result && $row = $result->fetch_assoc()) {
            return [
                'average' => round((float)($row['average'] ?? 0), 2),
                'minimum' => (int)($row['minimum'] ?? 0),
                'maximum' => (int)($row['maximum'] ?? 0),
                'count' => (int)($row['count'] ?? 0)
            ];
        }
    }

    // Fallback: compute from responses_data JSON (search for any numeric 1-5)
    $result = $conn->query("SELECT responses_data FROM survey_responses WHERE responses_data IS NOT NULL AND responses_data != '' AND responses_data != '{}'");
    if (!$result || $result->num_rows === 0) {
        return ['average' => 0, 'minimum' => 0, 'maximum' => 0, 'count' => 0];
    }
    $sum = 0; $count = 0; $min = 5; $max = 1;
    while ($row = $result->fetch_assoc()) {
        $data = json_decode($row['responses_data'], true);
        if (!is_array($data)) continue;
        $v = null;
        if (isset($data['5']) && is_numeric($data['5'])) {
            $v = (float)$data['5'];
        } else {
            foreach ($data as $val) {
                if (is_numeric($val) && (float)$val >= 1 && (float)$val <= 5) {
                    $v = (float)$val; break;
                }
            }
        }
        if ($v !== null) {
            $sum += $v; $count++;
            if ($v < $min) $min = $v; if ($v > $max) $max = $v;
        }
    }
    return [
        'average' => $count > 0 ? round($sum / $count, 2) : 0,
        'minimum' => $count > 0 ? (int)$min : 0,
        'maximum' => $count > 0 ? (int)$max : 0,
        'count' => $count
    ];
}

function getRecommendationRate($conn) {
    if (hasColumn($conn, 'would_recommend')) {
        $result = $conn->query(
            "SELECT SUM(would_recommend = 1) as recommend_yes, SUM(would_recommend = 0) as recommend_no, COUNT(*) as total FROM survey_responses"
        );
        if ($result && $row = $result->fetch_assoc()) {
            $yes = (int)($row['recommend_yes'] ?? 0);
            $no = (int)($row['recommend_no'] ?? 0);
            $total = (int)($row['total'] ?? 0);
            return ['yes'=>$yes,'no'=>$no,'total'=>$total,'percentage'=>$total>0?round(($yes/$total)*100,1):0];
        }
    }

    // Fallback: compute from responses_data question 9 text values
    $result = $conn->query("SELECT responses_data FROM survey_responses WHERE responses_data IS NOT NULL AND responses_data != ''");
    if (!$result) return ['yes'=>0,'no'=>0,'total'=>0,'percentage'=>0];
    $yes = 0; $no = 0;
    while ($row = $result->fetch_assoc()) {
        $data = json_decode($row['responses_data'], true);
        if (!is_array($data)) continue;
        $v = isset($data['9']) ? trim((string)$data['9']) : '';
        if ($v === '') continue;
        if (stripos($v, 'Definitely Yes') !== false || stripos($v, 'Probably Yes') !== false) $yes++; else $no++;
    }
    $total = $yes + $no;
    return ['yes'=>$yes,'no'=>$no,'total'=>$total,'percentage'=>$total>0?round(($yes/$total)*100,1):0];
}

function getVisitFrequency($conn) {
    $result = $conn->query("SELECT visit_frequency, COUNT(*) as count FROM survey_responses GROUP BY visit_frequency ORDER BY count DESC");
    if (!$result) return [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['label' => $row['visit_frequency'] ?? 'Not specified', 'value' => (int)$row['count']];
    }
    return $data;
}

function getVisitPurpose($conn) {
    $result = $conn->query("SELECT purpose, COUNT(*) as count FROM survey_responses WHERE purpose IS NOT NULL AND purpose != '' GROUP BY purpose ORDER BY count DESC LIMIT 10");
    if (!$result) return [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['label' => $row['purpose'] ?? '', 'value' => (int)$row['count']];
    }
    return $data;
}

function getRatingsBreakdown($conn) {
    // Find rating-type questions
    $questions = [];
    $qRes = $conn->query("SELECT id, question FROM survey_questions WHERE question_type = 'rating' ORDER BY display_order ASC");
    if ($qRes) {
        while ($r = $qRes->fetch_assoc()) {
            $questions[(int)$r['id']] = ['id'=>(int)$r['id'],'question'=>$r['question'],'sum'=>0,'count'=>0];
        }
    }
    if (empty($questions)) return [];

    $resp = $conn->query("SELECT responses_data FROM survey_responses WHERE responses_data IS NOT NULL AND responses_data != ''");
    if (!$resp) return [];
    while ($row = $resp->fetch_assoc()) {
        $data = json_decode($row['responses_data'], true);
        if (!is_array($data)) continue;
        foreach ($questions as $qid=>&$meta) {
            $k = (string)$qid;
            if (isset($data[$k]) && is_numeric($data[$k])) {
                $v = (float)$data[$k];
                if ($v >= 0) { $meta['sum'] += $v; $meta['count']++; }
            }
        }
        unset($meta);
    }

    $out = [];
    foreach ($questions as $meta) {
        $avg = $meta['count']>0 ? round($meta['sum']/$meta['count'],1):0;
        $out[] = ['id'=>$meta['id'],'question'=>$meta['question'],'average'=>$avg,'responses'=>(int)$meta['count']];
    }
    return $out;
}

try {
    $analytics = [
        'total_responses' => getTotalResponses($conn),
        'today_responses' => getTodayResponses($conn),
        'total_respondents' => getTotalRespondents($conn),
        'satisfaction_stats' => getSatisfactionStats($conn),
        'visit_frequency' => getVisitFrequency($conn),
        'visit_purpose' => getVisitPurpose($conn),
        'recommendation_rate' => getRecommendationRate($conn),
        'ratings_breakdown' => getRatingsBreakdown($conn)
    ];

    ob_end_clean();
    echo json_encode(['success'=>true,'data'=>$analytics]);
    exit;

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}

?>
