<?php
// Start session BEFORE sending any headers
session_start();

header('Content-Type: application/json');
require_once '../../api/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $current = trim($current);
    $new = trim($new);
    $confirm = trim($confirm);

    if ($current === '' || $new === '' || $confirm === '') {
        throw new Exception('All password fields are required');
    }

    if ($new !== $confirm) {
        throw new Exception('New password and confirmation do not match');
    }

    if (strlen($new) < 8) {
        throw new Exception('New password must be at least 8 characters');
    }

    // Get current password hash
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $userId);
    if (!$stmt->execute()) {
        throw new Exception('Query failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('User not found');
    }

    $row = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($current, $row['password'])) {
        throw new Exception('Current password is incorrect');
    }

    $newHash = password_hash($new, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->bind_param('si', $newHash, $userId);
    if (!$update->execute()) {
        throw new Exception('Failed to update password: ' . $update->error);
    }
    $update->close();

    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
