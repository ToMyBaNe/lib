<?php
/**
 * Admin Database Connection Diagnostic Tool
 * Check if admin side is properly connected to database
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Database Diagnostic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-database text-indigo-600 mr-2"></i> Admin Database Diagnostic
            </h1>
            <p class="text-gray-600">Verify database connection and configuration</p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <?php
            // Test 1: Database Connection
            echo '<div class="bg-white rounded-lg shadow p-6">';
            echo '<h2 class="text-xl font-semibold text-gray-800 mb-4">';
            echo '<i class="fas fa-plug text-blue-600 mr-2"></i> Database Connection</h2>';
            
            try {
                require_once '../api/db_config.php';
                
                if (!isset($conn) || !$conn) {
                    throw new Exception('Database connection object not available');
                }
                
                if ($conn->connect_error) {
                    throw new Exception('Connection Error: ' . $conn->connect_error);
                }
                
                echo '<div class="bg-green-50 border-l-4 border-green-600 p-4">';
                echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> ✓ Connected successfully</p>';
                echo '<p class="text-sm text-green-700 mt-2">Host: ' . htmlspecialchars(DB_HOST) . '</p>';
                echo '<p class="text-sm text-green-700">Database: ' . htmlspecialchars(DB_NAME) . '</p>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                echo '<p class="text-red-800"><i class="fas fa-times-circle mr-2"></i> ✗ Connection Failed</p>';
                echo '<p class="text-sm text-red-700 mt-2">' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
                exit;
            }
            echo '</div>';

            // Test 2: Users Table
            echo '<div class="bg-white rounded-lg shadow p-6">';
            echo '<h2 class="text-xl font-semibold text-gray-800 mb-4">';
            echo '<i class="fas fa-users text-blue-600 mr-2"></i> Users Table</h2>';
            
            try {
                $result = $conn->query("SHOW TABLES LIKE 'users'");
                
                if (!$result) {
                    throw new Exception('Query failed: ' . $conn->error);
                }
                
                if ($result->num_rows > 0) {
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
                        echo '<p class="text-sm text-yellow-700 mt-2">Please create an admin user.</p>';
                        echo '</div>';
                    } else {
                        // List users
                        $usersResult = $conn->query("SELECT id, username, email FROM users LIMIT 5");
                        echo '<div class="bg-gray-50 p-4 rounded overflow-x-auto">';
                        echo '<table class="w-full text-sm">';
                        echo '<thead><tr class="border-b"><th class="text-left py-2">ID</th><th class="text-left py-2">Username</th><th class="text-left py-2">Email</th></tr></thead>';
                        echo '<tbody>';
                        while ($user = $usersResult->fetch_assoc()) {
                            echo '<tr class="border-b"><td class="py-2">' . htmlspecialchars($user['id']) . '</td>';
                            echo '<td class="py-2">' . htmlspecialchars($user['username']) . '</td>';
                            echo '<td class="py-2">' . htmlspecialchars($user['email']) . '</td></tr>';
                        }
                        echo '</tbody></table>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                    echo '<p class="text-red-800"><i class="fas fa-times-circle mr-2"></i> ✗ Table not found</p>';
                    echo '<p class="text-sm text-red-700 mt-2">Please run: database/survey.sql</p>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                echo '<p class="text-red-800">' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
            echo '</div>';

            // Test 3: Survey Questions Table
            echo '<div class="bg-white rounded-lg shadow p-6">';
            echo '<h2 class="text-xl font-semibold text-gray-800 mb-4">';
            echo '<i class="fas fa-question-circle text-blue-600 mr-2"></i> Survey Questions Table</h2>';
            
            try {
                $result = $conn->query("SHOW TABLES LIKE 'survey_questions'");
                
                if ($result->num_rows > 0) {
                    echo '<div class="bg-green-50 border-l-4 border-green-600 p-4 mb-4">';
                    echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> ✓ Table exists</p>';
                    echo '</div>';
                    
                    // Check question count
                    $countResult = $conn->query("SELECT COUNT(*) as total FROM survey_questions WHERE is_active = 1");
                    $row = $countResult->fetch_assoc();
                    echo '<p class="text-gray-700">Active questions: <strong>' . $row['total'] . '</strong></p>';
                } else {
                    echo '<div class="bg-yellow-50 border-l-4 border-yellow-600 p-4">';
                    echo '<p class="text-yellow-800"><i class="fas fa-exclamation-triangle mr-2"></i> ⚠ Table not found</p>';
                    echo '<p class="text-sm text-yellow-700 mt-2">Run setup_questions.php to create it.</p>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                echo '<p class="text-red-800">' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
            echo '</div>';

            // Test 4: Survey Categories Table
            echo '<div class="bg-white rounded-lg shadow p-6">';
            echo '<h2 class="text-xl font-semibold text-gray-800 mb-4">';
            echo '<i class="fas fa-tags text-blue-600 mr-2"></i> Survey Categories Table</h2>';
            
            try {
                $result = $conn->query("SHOW TABLES LIKE 'survey_categories'");
                
                if ($result->num_rows > 0) {
                    echo '<div class="bg-green-50 border-l-4 border-green-600 p-4 mb-4">';
                    echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> ✓ Table exists</p>';
                    echo '</div>';
                    
                    // Check categories count
                    $countResult = $conn->query("SELECT COUNT(*) as total FROM survey_categories");
                    $row = $countResult->fetch_assoc();
                    echo '<p class="text-gray-700">Total categories: <strong>' . $row['total'] . '</strong></p>';
                } else {
                    echo '<div class="bg-yellow-50 border-l-4 border-yellow-600 p-4">';
                    echo '<p class="text-yellow-800"><i class="fas fa-exclamation-triangle mr-2"></i> ⚠ Table not found</p>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                echo '<p class="text-red-800">' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
            echo '</div>';

            // Test 5: Survey Responses Table
            echo '<div class="bg-white rounded-lg shadow p-6">';
            echo '<h2 class="text-xl font-semibold text-gray-800 mb-4">';
            echo '<i class="fas fa-comments text-blue-600 mr-2"></i> Survey Responses Table</h2>';
            
            try {
                $result = $conn->query("SHOW TABLES LIKE 'survey_responses'");
                
                if ($result->num_rows > 0) {
                    echo '<div class="bg-green-50 border-l-4 border-green-600 p-4 mb-4">';
                    echo '<p class="text-green-800"><i class="fas fa-check-circle mr-2"></i> ✓ Table exists</p>';
                    echo '</div>';
                    
                    // Check response count
                    $countResult = $conn->query("SELECT COUNT(*) as total FROM survey_responses");
                    $row = $countResult->fetch_assoc();
                    echo '<p class="text-gray-700">Total responses: <strong>' . $row['total'] . '</strong></p>';
                } else {
                    echo '<div class="bg-yellow-50 border-l-4 border-yellow-600 p-4">';
                    echo '<p class="text-yellow-800"><i class="fas fa-exclamation-triangle mr-2"></i> ⚠ Table not found</p>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                echo '<p class="text-red-800">' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
            echo '</div>';

            ?>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-cog text-blue-600 mr-2"></i> Quick Links
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="login.php" class="block bg-indigo-600 text-white py-3 px-4 rounded-lg text-center hover:bg-indigo-700 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i> Admin Login
                </a>
                <a href="../setup_questions.php" class="block bg-blue-600 text-white py-3 px-4 rounded-lg text-center hover:bg-blue-700 transition">
                    <i class="fas fa-cogs mr-2"></i> Setup Questions
                </a>
                <a href="../admin/setup_responses.php" class="block bg-green-600 text-white py-3 px-4 rounded-lg text-center hover:bg-green-700 transition">
                    <i class="fas fa-database mr-2"></i> Setup Responses
                </a>
                <a href="javascript:history.back()" class="block bg-gray-600 text-white py-3 px-4 rounded-lg text-center hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Go Back
                </a>
            </div>
        </div>

        <div class="text-center mt-6 text-gray-600 text-sm">
            <p><i class="fas fa-info-circle mr-2"></i> Run this diagnostic to verify your setup before logging in.</p>
        </div>
    </div>
</body>
</html>
        
        <div id="results" class="space-y-4"></div>
    </div>

    <script>
        async function runDiagnostics() {
            const results = document.getElementById('results');
            const tests = [];

            // Test 1: Session/Authentication
            console.log('Checking session...');
            tests.push({
                name: 'Admin Session',
                pass: document.cookie.includes('PHPSESSID'),
                details: 'Check if logged in to admin panel'
            });

            // Test 2: Questions API
            console.log('Testing Questions API...');
            try {
                const response = await fetch('./api/questions.php?action=list');
                const text = await response.text();
                
                let isJson = false;
                try {
                    const data = JSON.parse(text);
                    isJson = true;
                    tests.push({
                        name: 'Questions API Response',
                        pass: data.success !== false,
                        details: `Status: ${response.status}, Message: ${data.message || 'Success'}`
                    });
                } catch {
                    tests.push({
                        name: 'Questions API Response',
                        pass: false,
                        details: `Status: ${response.status}, Response type: ${isJson ? 'JSON' : 'HTML/Text'}, First 200 chars: ${text.substring(0, 200)}`
                    });
                }
            } catch (e) {
                tests.push({
                    name: 'Questions API Response',
                    pass: false,
                    details: `Error: ${e.message}`
                });
            }

            // Test 3: Database Table
            console.log('Testing database...');
            try {
                const response = await fetch('../api/test.php');
                const data = await response.json();
                const hasQuestionsTable = data.database?.tables?.survey_questions === true;
                tests.push({
                    name: 'Survey Questions Table',
                    pass: hasQuestionsTable,
                    details: hasQuestionsTable ? 'Table exists' : 'Table NOT found - Run setup_questions.php'
                });
            } catch (e) {
                tests.push({
                    name: 'Survey Questions Table',
                    pass: false,
                    details: `Error: ${e.message}`
                });
            }

            // Render results
            results.innerHTML = tests.map(test => `
                <div class="p-4 rounded-lg border-l-4 ${test.pass ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500'}">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">${test.pass ? '✓' : '✗'}</span>
                        <div>
                            <h3 class="font-semibold ${test.pass ? 'text-green-900' : 'text-red-900'}">${test.name}</h3>
                            <p class="text-sm ${test.pass ? 'text-green-700' : 'text-red-700'}">${test.details}</p>
                        </div>
                    </div>
                </div>
            `).join('');

            // Summary
            const passCount = tests.filter(t => t.pass).length;
            const summary = document.createElement('div');
            summary.className = 'mt-8 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500';
            summary.innerHTML = `
                <h2 class="font-semibold text-blue-900 mb-3">Results: ${passCount}/${tests.length} checks passed</h2>
                ${passCount < tests.length ? `
                    <div class="text-sm text-blue-800 space-y-2">
                        <p><strong>Troubleshooting steps:</strong></p>
                        <ol class="list-decimal ml-4 space-y-1">
                            <li>Make sure you're logged into admin panel (are you redirected to login?)</li>
                            <li>Run setup_questions.php first to create the database table</li>
                            <li>Check browser console (F12) for JavaScript errors</li>
                            <li>Check XAMPP error logs for PHP errors</li>
                        </ol>
                    </div>
                ` : `<p class="text-green-700"><i class="fas fa-check-circle"></i> All systems operational!</p>`}
            `;
            results.insertBefore(summary, results.firstChild);
        }

        window.addEventListener('load', runDiagnostics);
    </script>
</body>
</html>
