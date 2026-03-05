<?php
/**
 * Survey Questions Loading Troubleshooter
 * Helps diagnose "Error Loading Questions" issues
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions Loading Troubleshooter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-question-circle text-orange-600 mr-2"></i> Questions Loading Troubleshooter
            </h1>
            <p class="text-gray-600">Fix "Error Loading Questions" issues</p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Step 1: API Test -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-flask text-blue-600 mr-2"></i> Step 1: Test API Endpoint
                </h2>
                
                <p class="text-gray-700 mb-4">Testing if the questions API is working correctly...</p>
                
                <div id="apiTest" class="space-y-4">
                    <div class="bg-gray-100 p-4 rounded font-mono text-sm">
                        <p class="text-gray-600">Testing GET /api/questions.php</p>
                        <p id="apiStatus">Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Database Check -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-database text-blue-600 mr-2"></i> Step 2: Database Check
                </h2>
                <p class="text-gray-700 mb-4">Verifying database tables and data...</p>
                <?php
                try {
                    require_once 'api/db_config.php';
                    
                    if (!isset($conn) || !$conn) {
                        throw new Exception('No database connection');
                    }
                    
                    // Check tables
                    $tables = ['survey_categories', 'survey_questions'];
                    foreach ($tables as $table) {
                        $result = $conn->query("SHOW TABLES LIKE '$table'");
                        $exists = $result && $result->num_rows > 0;
                        $status = $exists ? '<span class="text-green-600">✓ Exists</span>' : '<span class="text-red-600">✗ Missing</span>';
                        echo "<p class='text-gray-700'>• $table: $status</p>";
                    }
                    
                    // Check questions count
                    echo "<hr class='my-4'>";
                    echo "<div class='space-y-2'>";
                    
                    $result = $conn->query("SELECT COUNT(*) as total FROM survey_categories");
                    $row = $result->fetch_assoc();
                    echo "<p class='text-gray-700'>• Categories: <strong>" . $row['total'] . "</strong></p>";
                    
                    $result = $conn->query("SELECT COUNT(*) as total FROM survey_questions WHERE is_active = 1");
                    $row = $result->fetch_assoc();
                    echo "<p class='text-gray-700'>• Active Questions: <strong>" . $row['total'] . "</strong></p>";
                    
                    if ($row['total'] == 0) {
                        echo "<div class='bg-yellow-50 border-l-4 border-yellow-600 p-4 mt-4'>";
                        echo "<p class='text-yellow-800'><strong>⚠ No questions found!</strong></p>";
                        echo "<p class='text-sm text-yellow-700 mt-2'>Run: <code>setup_questions.php</code> from admin panel to create default questions.</p>";
                        echo "</div>";
                    }
                    
                    echo "</div>";
                    
                } catch (Exception $e) {
                    echo '<div class="bg-red-50 border-l-4 border-red-600 p-4">';
                    echo '<p class="text-red-800"><strong>Database Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Step 3: Browser Console -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-terminal text-blue-600 mr-2"></i> Step 3: Browser Console
                </h2>
                <p class="text-gray-700 mb-4">Check detailed error messages:</p>
                <ol class="space-y-3 text-gray-700">
                    <li><strong>1.</strong> Press <code class="bg-gray-100 px-2 py-1 rounded">F12</code> to open Developer Tools</li>
                    <li><strong>2.</strong> Click the <strong>Console</strong> tab</li>
                    <li><strong>3.</strong> Look for any error messages in red</li>
                    <li><strong>4.</strong> Screenshots can help with debugging</li>
                </ol>
                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mt-4">
                    <p class="text-blue-800"><strong>Common Errors:</strong></p>
                    <ul class="text-sm text-blue-700 mt-2 space-y-1">
                        <li>• <code>Cannot read properties of undefined (reading 'replace')</code> - Fixed in latest update</li>
                        <li>• <code>Failed to fetch</code> - Server not running or wrong URL</li>
                        <li>• <code>JSON.parse error</code> - API returning invalid JSON</li>
                    </ul>
                </div>
            </div>

            <!-- Step 4: Solution Checklist -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-list-check text-green-600 mr-2"></i> Solution Checklist
                </h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4">
                        <span class="text-gray-700">MySQL/Database is running</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4">
                        <span class="text-gray-700">Database has been imported (survey.sql)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4">
                        <span class="text-gray-700">Run setup_questions.php to create default questions</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4">
                        <span class="text-gray-700">Clear browser cache (Ctrl+Shift+Delete)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4">
                        <span class="text-gray-700">Refresh the page (F5)</span>
                    </label>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-link text-blue-600 mr-2"></i> Quick Links
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="setup_questions.php" class="block bg-indigo-600 text-white py-3 px-4 rounded-lg text-center hover:bg-indigo-700 transition">
                        <i class="fas fa-tools mr-2"></i> Setup Questions
                    </a>
                    <a href="public/index.php" class="block bg-green-600 text-white py-3 px-4 rounded-lg text-center hover:bg-green-700 transition">
                        <i class="fas fa-survey mr-2"></i> Test Survey
                    </a>
                    <a href="admin/login_debug.php" class="block bg-blue-600 text-white py-3 px-4 rounded-lg text-center hover:bg-blue-700 transition">
                        <i class="fas fa-bug mr-2"></i> Admin Debug
                    </a>
                    <a href="api_tester.php" class="block bg-orange-600 text-white py-3 px-4 rounded-lg text-center hover:bg-orange-700 transition">
                        <i class="fas fa-flask mr-2"></i> API Tester
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-8 text-gray-600 text-sm">
            <p><i class="fas fa-info-circle mr-2"></i> If problems persist, check the admin debug panel or contact support.</p>
        </div>
    </div>

    <script>
        // Test API endpoint
        async function testAPI() {
            const statusEl = document.getElementById('apiStatus');
            try {
                const response = await fetch('api/questions.php');
                const text = await response.text();
                
                if (!text || text.trim().length === 0) {
                    statusEl.innerHTML = '<span class="text-red-600">❌ Empty response - Check database</span>';
                    return;
                }
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    statusEl.innerHTML = '<span class="text-red-600">❌ Invalid JSON response</span><pre class="mt-2 bg-red-50 p-2 text-xs overflow-auto">' + text.substring(0, 200) + '</pre>';
                    return;
                }
                
                if (!data.success) {
                    statusEl.innerHTML = '<span class="text-red-600">❌ ' + (data.message || 'API error') + '</span>';
                    return;
                }
                
                const count = (data.categorized && Object.keys(data.categorized).length) || 0;
                statusEl.innerHTML = '<span class="text-green-600">✓ Success! Found ' + count + ' question categories</span>';
                
            } catch (error) {
                statusEl.innerHTML = '<span class="text-red-600">❌ ' + error.message + '</span>';
            }
        }
        
        // Run tests on load
        document.addEventListener('DOMContentLoaded', testAPI);
    </script>
</body>
</html>
