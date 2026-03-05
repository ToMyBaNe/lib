<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup & Verification Guide</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-8 text-white mb-6">
            <h1 class="text-4xl font-bold mb-2">
                <i class="fas fa-rocket mr-2"></i> Setup & Verification Guide
            </h1>
            <p class="text-indigo-100">Complete checklist to get your survey working</p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Step 1 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-indigo-100 text-indigo-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">1</div>
                    <h2 class="text-2xl font-semibold text-gray-800">Import Database</h2>
                </div>
                
                <div class="space-y-3 text-gray-700">
                    <p>• Go to <a href="http://localhost/phpmyadmin" class="text-indigo-600 hover:underline" target="_blank">phpMyAdmin</a></p>
                    <p>• Click "Import" tab</p>
                    <p>• Select <code class="bg-gray-100 px-2 py-1 rounded">database/survey.sql</code></p>
                    <p>• Click "Import"</p>
                </div>
                
                <p class="text-sm text-gray-500 mt-3">✓ Tables created: users, survey_categories, survey_questions, survey_responses</p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-indigo-100 text-indigo-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">2</div>
                    <h2 class="text-2xl font-semibold text-gray-800">Create Admin User</h2>
                </div>
                
                <p class="text-gray-700 mb-4">Visit: <a href="admin/login_debug.php" class="text-indigo-600 hover:underline">Admin Debug Panel</a></p>
                <p class="text-gray-700 mb-4">Use the "Create Admin User" form to add:</p>
                <ul class="space-y-2 text-gray-700">
                    <li>• Username: <code class="bg-gray-100 px-2 py-1 rounded">admin</code></li>
                    <li>• Email: <code class="bg-gray-100 px-2 py-1 rounded">admin@library.local</code></li>
                    <li>• Password: <code class="bg-gray-100 px-2 py-1 rounded">password123</code></li>
                </ul>
                
                <p class="text-sm text-gray-500 mt-3">✓ Admin account created and ready to log in</p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-indigo-100 text-indigo-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">3</div>
                    <h2 class="text-2xl font-semibold text-gray-800">Setup Default Questions</h2>
                </div>
                
                <p class="text-gray-700 mb-4">Visit: <a href="setup_questions.php" class="text-indigo-600 hover:underline">Setup Questions Page</a></p>
                <p class="text-gray-700 mb-3">This will create:</p>
                <ul class="space-y-1 text-gray-700 text-sm">
                    <li>• 4 survey categories (About You, Your Visit, Feedback, Additional Feedback)</li>
                    <li>• 10 default survey questions</li>
                    <li>• Mixed question types (text, textarea, select, rating)</li>
                </ul>
                
                <p class="text-sm text-gray-500 mt-3">✓ Default questions created and ready to display</p>
            </div>

            <!-- Step 4 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-indigo-100 text-indigo-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">4</div>
                    <h2 class="text-2xl font-semibold text-gray-800">Verify API Endpoint</h2>
                </div>
                
                <p class="text-gray-700 mb-4">Visit: <a href="api_tester.php" class="text-indigo-600 hover:underline">API Tester</a></p>
                <p class="text-gray-700 mb-3">Check that:</p>
                <ul class="space-y-1 text-gray-700 text-sm">
                    <li>• Status: <span class="text-green-600">200 OK</span></li>
                    <li>• Questions found: <span class="text-green-600">&gt; 0</span></li>
                    <li>• Categories: <span class="text-green-600">&gt; 0</span></li>
                </ul>
                
                <p class="text-sm text-gray-500 mt-3">✓ API is functioning correctly</p>
            </div>

            <!-- Step 5 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-indigo-100 text-indigo-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">5</div>
                    <h2 class="text-2xl font-semibold text-gray-800">Test Survey Form</h2>
                </div>
                
                <p class="text-gray-700 mb-4">Visit: <a href="public/index.php" class="text-indigo-600 hover:underline">Public Survey</a></p>
                <p class="text-gray-700 mb-3">Verify:</p>
                <ul class="space-y-1 text-gray-700 text-sm">
                    <li>• Questions load successfully</li>
                    <li>• Form displays all question types correctly</li>
                    <li>• Submit button works</li>
                    <li>• Success message appears</li>
                </ul>
                
                <p class="text-sm text-gray-500 mt-3">✓ Survey form working perfectly!</p>
            </div>

            <!-- Emergency Reset -->
            <div class="bg-white rounded-lg shadow p-6 border-2 border-red-300">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i> Emergency Reset
                </h2>
                
                <p class="text-gray-700 mb-4">If something goes wrong, follow these steps:</p>
                
                <ol class="space-y-3 text-gray-700 text-sm">
                    <li><strong>1. Clear Browser Cache:</strong> Press Ctrl+Shift+Delete and clear all</li>
                    <li><strong>2. Drop Database:</strong> In phpMyAdmin, right-click library_survey → Drop</li>
                    <li><strong>3. Re-import SQL:</strong> Import database/survey.sql again</li>
                    <li><strong>4. Re-setup Questions:</strong> Run setup_questions.php</li>
                    <li><strong>5. Refresh:</strong> Press F5 on the survey page</li>
                </ol>
            </div>

            <!-- Quick Links -->
            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-lightning-bolt text-yellow-600 mr-2"></i> Quick Navigation
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="http://localhost/phpmyadmin" target="_blank" class="block bg-blue-600 text-white py-3 px-4 rounded-lg text-center hover:bg-blue-700 transition">
                        <i class="fas fa-database mr-2"></i> phpMyAdmin
                    </a>
                    <a href="admin/login.php" class="block bg-indigo-600 text-white py-3 px-4 rounded-lg text-center hover:bg-indigo-700 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i> Admin Login
                    </a>
                    <a href="setup_questions.php" class="block bg-green-600 text-white py-3 px-4 rounded-lg text-center hover:bg-green-700 transition">
                        <i class="fas fa-tools mr-2"></i> Setup Questions
                    </a>
                    <a href="public/index.php" class="block bg-purple-600 text-white py-3 px-4 rounded-lg text-center hover:bg-purple-700 transition">
                        <i class="fas fa-survey mr-2"></i> Public Survey
                    </a>
                    <a href="api_tester.php" class="block bg-orange-600 text-white py-3 px-4 rounded-lg text-center hover:bg-orange-700 transition">
                        <i class="fas fa-flask mr-2"></i> API Tester
                    </a>
                    <a href="troubleshoot_questions.php" class="block bg-red-600 text-white py-3 px-4 rounded-lg text-center hover:bg-red-700 transition">
                        <i class="fas fa-wrench mr-2"></i> Troubleshoot
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-8 text-gray-600 text-sm">
            <p><i class="fas fa-heart text-red-500 mr-1"></i> Follow each step in order for best results</p>
        </div>
    </div>
</body>
</html>
