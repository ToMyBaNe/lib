<?php
/**
 * Admin Panel Home/Index
 */
session_start();
require_once 'config.php';
requireAdminAuth();

// Get admin info
$admin = getCurrentAdmin();

// Get statistics from database
require_once '../api/db_config.php';

// Total questions
$questionsResult = $conn->query("SELECT COUNT(*) as count FROM survey_questions WHERE is_active = 1");
$totalQuestions = $questionsResult->fetch_assoc()['count'] ?? 0;

// Total responses
$responsesResult = $conn->query("SELECT COUNT(*) as count FROM survey_responses");
$totalResponses = $responsesResult->fetch_assoc()['count'] ?? 0;

// Today's responses
$todayResult = $conn->query("SELECT COUNT(*) as count FROM survey_responses WHERE DATE(created_at) = CURDATE()");
$todayResponses = $todayResult->fetch_assoc()['count'] ?? 0;

// Recent responses (last 7 days)
$recentResult = $conn->query("SELECT COUNT(*) as count FROM survey_responses WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$recentResponses = $recentResult->fetch_assoc()['count'] ?? 0;

// Categories count
$categoriesResult = $conn->query("SELECT COUNT(*) as count FROM survey_categories");
$totalCategories = $categoriesResult->fetch_assoc()['count'] ?? 0;

$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Library Survey</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="./assets/admin.css">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php require_once './components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 ml-64 overflow-auto flex flex-col">
            <!-- Header -->
            <?php require_once './components/header.php'; ?>
            
            <!-- Dashboard Content -->
            <div class="flex-1 p-8 overflow-auto">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Welcome, <?php echo htmlspecialchars($admin['username']); ?>!</h1>
                    <p class="text-gray-600">Here's your survey dashboard overview</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Responses -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Responses</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($totalResponses); ?></h3>
                            </div>
                            <i class="fas fa-chart-pie text-4xl text-blue-500 opacity-20"></i>
                        </div>
                    </div>

                    <!-- Today's Responses -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Today's Responses</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($todayResponses); ?></h3>
                                <p class="text-xs text-gray-400 mt-1"><?php echo date('Y-m-d'); ?></p>
                            </div>
                            <i class="fas fa-calendar text-4xl text-green-500 opacity-20"></i>
                        </div>
                    </div>

                    <!-- This Week's Responses -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">This Week</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($recentResponses); ?></h3>
                                <p class="text-xs text-gray-400 mt-1">Last 7 days</p>
                            </div>
                            <i class="fas fa-fire text-4xl text-purple-500 opacity-20"></i>
                        </div>
                    </div>

                    <!-- Total Questions -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Questions</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($totalQuestions); ?></h3>
                                <p class="text-xs text-gray-400 mt-1"><?php echo number_format($totalCategories); ?> categories</p>
                            </div>
                            <i class="fas fa-question-circle text-4xl text-orange-500 opacity-20"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            <i class="fas fa-bolt text-yellow-500 mr-2"></i> Quick Actions
                        </h2>
                        <div class="space-y-3">
                            <a href="./manage_questions.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 transition">
                                <i class="fas fa-edit text-indigo-600 text-lg"></i>
                                <span class="text-gray-700 font-medium">Manage Questions</span>
                                <i class="fas fa-arrow-right text-gray-400 ml-auto"></i>
                            </a>
                            <a href="./responses.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 transition">
                                <i class="fas fa-comments text-green-600 text-lg"></i>
                                <span class="text-gray-700 font-medium">View Responses</span>
                                <i class="fas fa-arrow-right text-gray-400 ml-auto"></i>
                            </a>
                            <a href="./settings.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 transition">
                                <i class="fas fa-cog text-red-600 text-lg"></i>
                                <span class="text-gray-700 font-medium">Settings</span>
                                <i class="fas fa-arrow-right text-gray-400 ml-auto"></i>
                            </a>
                            <a href="../api_tester.php" target="_blank" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 transition">
                                <i class="fas fa-flask text-blue-600 text-lg"></i>
                                <span class="text-gray-700 font-medium">API Tester</span>
                                <i class="fas fa-arrow-right text-gray-400 ml-auto"></i>
                            </a>
                        </div>
                    </div>

                    <!-- System Info Card -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow p-6 border border-indigo-200">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            <i class="fas fa-info-circle text-indigo-600 mr-2"></i> System Information
                        </h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Admin Panel Status</span>
                                <span class="text-green-600 font-medium"><i class="fas fa-check-circle"></i> Operational</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Database Connection</span>
                                <span class="text-green-600 font-medium"><i class="fas fa-check-circle"></i> Connected</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">API Status</span>
                                <span class="text-green-600 font-medium"><i class="fas fa-check-circle"></i> Working</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Server Time</span>
                                <span class="text-gray-900 font-medium"><?php echo date('H:i:s'); ?></span>
                            </div>
                            <hr class="my-3 border-indigo-200">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Logged In As</span>
                                <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($admin['username']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="./assets/admin.js"></script>
</body>
</html>
