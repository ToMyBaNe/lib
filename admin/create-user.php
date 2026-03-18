<?php
/**
 * Create admin user handler (POST only)
 */

session_start();
require_once '../api/db_config.php';
require_once 'config.php';

requireAdminAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'create_user') {
    header('Location: ./dashboard.php');
    exit;
}

try {
    $username = trim($_POST['create_username'] ?? '');
    $email = trim($_POST['create_email'] ?? '');
    $password = trim($_POST['create_password'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        throw new Exception('All fields are required.');
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('sss', $username, $hash, $email);

    if (!$stmt->execute()) {
        throw new Exception('Failed to create user: ' . $stmt->error);
    }

    $stmt->close();

    header('Location: ./dashboard.php?user_created=1');
    exit;
} catch (Exception $e) {
    $message = urlencode($e->getMessage());
    header('Location: ./dashboard.php?user_error=' . $message);
    exit;
}
