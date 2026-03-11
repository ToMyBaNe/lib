<?php
/**
 * Login Troubleshooting & Debug Page
 * Helps diagnose login issues
 */

session_start();
header('Content-Type: text/html; charset=utf-8');
require_once '../api/db_config.php';
require_once 'config.php';

requireAdminAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Debug & Troubleshooting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-users text-orange-600 mr-2"></i> Users Management
            </h1>
            <p class="text-gray-600">You can add more admins here</p>
        </div>

        <div class="grid grid-cols-1 gap-6">

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i> Create Admin User
                </h2>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="create_username" value="" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="create_email" value="" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="create_password" value="" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    
                    <button type="submit" name="action" value="create_user"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-plus mr-2"></i> Create User
                    </button>
                </form>
                
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {
                    try {
                        $username = trim($_POST['create_username'] ?? '');
                        $email = trim($_POST['create_email'] ?? '');
                        $password = trim($_POST['create_password'] ?? '');
                        
                        if (empty($username) || empty($email) || empty($password)) {
                            throw new Exception('All fields are required');
                        }
                        
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        
                        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                        $stmt->bind_param('sss', $username, $hash, $email);
                        
                        if ($stmt->execute()) {
                            echo '<div class="bg-green-50 border-l-4 border-green-600 p-4 mt-4">';
                            echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> User created successfully!</p>';
                            echo '</div>';
                        } else {
                            throw new Exception('Failed to create user: ' . $stmt->error);
                        }
                        $stmt->close();
                    } catch (Exception $e) {
                        echo '<div class="bg-red-50 border-l-4 border-red-600 p-4 mt-4">';
                        echo '<p class="text-red-800"><i class="fas fa-times-circle mr-2"></i> Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

        <div class="text-center mt-6">
            <a href="dashboard.php" class="inline-block bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
