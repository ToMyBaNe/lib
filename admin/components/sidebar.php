<?php
/**
 * Admin Sidebar Navigation Component
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="fixed left-0 top-0 h-screen w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white shadow-xl overflow-y-auto z-30">
    <!-- Logo Section -->
    <div class="p-6 border-b border-gray-700">
        <a href="./index.php" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center font-bold text-lg">
                <i class="fas fa-poll"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold">Survey</h1>
                <p class="text-xs text-gray-400">Admin</p>
            </div>
        </a>
    </div>

    <!-- Navigation Items -->
    <nav class="py-6 space-y-1 px-3">
        <!-- Home -->
        <a href="./index.php" class="<?php echo $currentPage === 'index' ? 'bg-indigo-600 border-r-4 border-indigo-400' : 'hover:bg-gray-700/50'; ?> px-4 py-3 rounded-lg flex items-center gap-3 transition duration-200">
            <i class="fas fa-home w-5"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Manage Questions -->
        <a href="./manage_questions.php" class="<?php echo $currentPage === 'manage_questions' ? 'bg-indigo-600 border-r-4 border-indigo-400' : 'hover:bg-gray-700/50'; ?> px-4 py-3 rounded-lg flex items-center gap-3 transition duration-200">
            <i class="fas fa-question-circle w-5"></i>
            <span class="font-medium">Questions</span>
        </a>

        <!-- Survey Responses -->
        <a href="./responses.php" class="<?php echo $currentPage === 'responses' ? 'bg-indigo-600 border-r-4 border-indigo-400' : 'hover:bg-gray-700/50'; ?> px-4 py-3 rounded-lg flex items-center gap-3 transition duration-200">
            <i class="fas fa-comments w-5"></i>
            <span class="font-medium">Responses</span>
        </a>

        <!-- Settings -->
        <a href="./settings.php" class="<?php echo $currentPage === 'settings' ? 'bg-indigo-600 border-r-4 border-indigo-400' : 'hover:bg-gray-700/50'; ?> px-4 py-3 rounded-lg flex items-center gap-3 transition duration-200">
            <i class="fas fa-cog w-5"></i>
            <span class="font-medium">Settings</span>
        </a>
    </nav>

    <!-- Divider -->
    <div class="my-4 mx-3 border-t border-gray-700"></div>

    <!-- Tools Section -->
    <div class="px-3 py-4">
        <p class="text-xs text-gray-400 uppercase font-semibold px-4 mb-2">Developer Tools</p>
        <a href="./diagnostic.php" class="<?php echo $currentPage === 'diagnostic' ? 'bg-indigo-600 border-r-4 border-indigo-400' : 'hover:bg-gray-700/50'; ?> px-4 py-3 rounded-lg flex items-center gap-3 transition duration-200 text-sm">
            <i class="fas fa-stethoscope w-5"></i>
            <span>Diagnostic</span>
        </a>
        <a href="./login_debug.php" class="<?php echo $currentPage === 'login_debug' ? 'bg-indigo-600 border-r-4 border-indigo-400' : 'hover:bg-gray-700/50'; ?> px-4 py-3 rounded-lg flex items-center gap-3 transition duration-200 text-sm">
            <i class="fas fa-bug w-5"></i>
            <span>Debug Panel</span>
        </a>
        <a href="../api_tester.php" target="_blank" class="hover:bg-gray-700/50 px-4 py-3 rounded-lg flex items-center gap-3 transition duration-200 text-sm">
            <i class="fas fa-flask w-5"></i>
            <span>API Tester</span>
            <i class="fas fa-external-link-alt text-xs ml-auto text-gray-400"></i>
        </a>
    </div>

    <!-- Footer / Logout -->
    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gray-900/80 border-t border-gray-700 space-y-2">
        <div class="px-3 py-2 text-sm text-gray-300">
            <p class="text-xs text-gray-500">Logged in as</p>
            <p class="font-medium text-lg"><?php echo htmlspecialchars(($_SESSION['username'] ?? 'Admin')); ?></p>
        </div>
        <a href="?logout" class="w-full text-center px-4 py-2 rounded-lg text-sm text-red-400 hover:bg-red-900/20 hover:text-red-300 transition duration-200 flex items-center gap-2 justify-center">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>
</aside>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./login.php');
    exit;
}

