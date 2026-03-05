<?php
/**
 * Login Troubleshooting & Debug Page
 * Helps diagnose login issues
 */

session_start();
header('Content-Type: text/html; charset=utf-8');
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
                <i class="fas fa-bug text-orange-600 mr-2"></i> Login Troubleshooting
            </h1>
            <p class="text-gray-600">Diagnose and fix login issues</p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Test 1: Database Connection -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-database text-blue-600 mr-2"></i> Database Connection
                </h2>
                
                <?php
                try {
                    require_once '../api/db_config.php';
                    
                    if (!isset($conn) || !$conn) {
                        throw new Exception('Connection object not available');
                    }
                    
                    if ($conn->connect_error) {
                        throw new Exception($conn->connect_error);
                    }
                    
                    echo '<div class="bg-green-50 border-l-4 border-green-600 p-4">';
                    echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> ✓ Connected</p>';
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                    echo '<p class="text-red-800"><i class="fas fa-times-circle mr-2"></i> ✗ Connection Failed</p>';
                    echo '<p class="text-sm text-red-700 mt-2">' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '</div>';
                    exit;
                }
                ?>
            </div>

            <!-- Test 2: Users Table -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-table text-blue-600 mr-2"></i> Users Table
                </h2>
                
                <?php
                $tableExists = false;
                try {
                    $result = $conn->query("SHOW TABLES LIKE 'users'");
                    if ($result && $result->num_rows > 0) {
                        $tableExists = true;
                        echo '<div class="bg-green-50 border-l-4 border-green-600 p-4 mb-4">';
                        echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> ✓ Table exists</p>';
                        echo '</div>';
                        
                        // Check user count
                        $countResult = $conn->query("SELECT COUNT(*) as total FROM users");
                        $row = $countResult->fetch_assoc();
                        $userCount = $row['total'];
                        
                        echo '<p class="text-gray-700 mb-4">Total users: <strong>' . $userCount . '</strong></p>';
                        
                        if ($userCount === 0) {
                            echo '<div class="bg-yellow-50 border-l-4 border-yellow-600 p-4">';
                            echo '<p class="text-yellow-800"><i class="fas fa-exclamation-triangle mr-2"></i> ⚠ No users found</p>';
                            echo '</div>';
                        } else {
                            // List users
                            $usersResult = $conn->query("SELECT id, username, email FROM users LIMIT 10");
                            echo '<div class="bg-gray-50 p-4 rounded overflow-x-auto mb-4">';
                            echo '<table class="w-full text-sm">';
                            echo '<thead><tr class="border-b"><th class="text-left py-2 px-2">ID</th><th class="text-left py-2 px-2">Username</th><th class="text-left py-2 px-2">Email</th></tr></thead>';
                            echo '<tbody>';
                            while ($user = $usersResult->fetch_assoc()) {
                                echo '<tr class="border-b"><td class="py-2 px-2">' . htmlspecialchars($user['id']) . '</td>';
                                echo '<td class="py-2 px-2"><code>' . htmlspecialchars($user['username']) . '</code></td>';
                                echo '<td class="py-2 px-2">' . htmlspecialchars($user['email']) . '</td></tr>';
                            }
                            echo '</tbody></table>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                        echo '<p class="text-red-800"><i class="fas fa-times-circle mr-2"></i> ✗ Table not found</p>';
                        echo '<p class="text-sm text-red-700 mt-2">Run database/survey.sql or setup_questions.php</p>';
                        echo '</div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                    echo '<p class="text-red-800">' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Test 3: Create Admin User -->
            <?php if ($tableExists): ?>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i> Create Admin User
                </h2>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="create_username" value="admin" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="create_email" value="admin@library.local" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="create_password" value="password123" required
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
                            echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> ✓ User created successfully!</p>';
                            echo '<p class="text-sm text-green-700 mt-2">Username: <code>' . htmlspecialchars($username) . '</code></p>';
                            echo '<p class="text-sm text-green-700">Password: <code>' . htmlspecialchars($password) . '</code></p>';
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
            <?php endif; ?>

            <!-- Test 4: Password Verification Test -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-lock text-blue-600 mr-2"></i> Test Password Verification
                </h2>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="test_username" placeholder="admin" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="test_password" placeholder="password123" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    
                    <button type="submit" name="action" value="test_password"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-flask mr-2"></i> Test Login
                    </button>
                </form>
                
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_password') {
                    try {
                        $test_username = trim($_POST['test_username'] ?? '');
                        $test_password = trim($_POST['test_password'] ?? '');
                        
                        if (empty($test_username) || empty($test_password)) {
                            throw new Exception('Username and password required');
                        }
                        
                        $stmt = $conn->prepare("SELECT id, username, password, email FROM users WHERE username = ?");
                        $stmt->bind_param('s', $test_username);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows === 0) {
                            echo '<div class="bg-yellow-50 border-l-4 border-yellow-600 p-4 mt-4">';
                            echo '<p class="text-yellow-800"><i class="fas fa-exclamation-triangle mr-2"></i> User not found</p>';
                            echo '</div>';
                        } else {
                            $user = $result->fetch_assoc();
                            $passwordMatch = password_verify($test_password, $user['password']);
                            
                            if ($passwordMatch) {
                                echo '<div class="bg-green-50 border-l-4 border-green-600 p-4 mt-4">';
                                echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> ✓ Password verified!</p>';
                                echo '<p class="text-sm text-green-700 mt-2">User: ' . htmlspecialchars($user['username']) . '</p>';
                                echo '<p class="text-sm text-green-700">Email: ' . htmlspecialchars($user['email']) . '</p>';
                                echo '</div>';
                            } else {
                                echo '<div class="bg-red-50 border-l-4 border-red-600 p-4 mt-4">';
                                echo '<p class="text-red-800"><i class="fas fa-times-circle mr-2"></i> Password does not match</p>';
                                echo '<p class="text-sm text-red-700 mt-2">User exists but password is incorrect</p>';
                                echo '</div>';
                            }
                        }
                        $stmt->close();
                    } catch (Exception $e) {
                        echo '<div class="bg-red-50 border-l-4 border-red-600 p-4 mt-4">';
                        echo '<p class="text-red-800">' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-lightbulb text-yellow-600 mr-2"></i> Troubleshooting Tips
            </h2>
            <ul class="space-y-3 text-gray-700">
                <li><i class="fas fa-check text-green-600 mr-2"></i> <strong>Database not running?</strong> Start MySQL/MariaDB service</li>
                <li><i class="fas fa-check text-green-600 mr-2"></i> <strong>Database not imported?</strong> Run database/survey.sql in phpMyAdmin</li>
                <li><i class="fas fa-check text-green-600 mr-2"></i> <strong>No admin user?</strong> Use the "Create Admin User" form above</li>
                <li><i class="fas fa-check text-green-600 mr-2"></i> <strong>Can't remember password?</strong> Delete the user and create a new one</li>
                <li><i class="fas fa-check text-green-600 mr-2"></i> <strong>Session issues?</strong> Clear browser cookies and try again</li>
            </ul>
        </div>

        <div class="text-center mt-6">
            <a href="login.php" class="inline-block bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Login
            </a>
        </div>
    </div>
</body>
</html>
