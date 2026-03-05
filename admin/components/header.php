<?php
/**
 * Admin Header/Top Navigation Component
 */
$admin = getCurrentAdmin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle = getPageTitle($currentPage);
?>

<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20">
    <div class="px-8 py-4 flex justify-between items-center">
        <!-- Page Title -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($pageTitle); ?></h1>
            <p class="text-xs text-gray-400 mt-1">
                <i class="fas fa-clock mr-1"></i>
                Last updated: <?php echo date('Y-m-d H:i:s'); ?>
            </p>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-6">
            <!-- User Info -->
            <div class="flex items-center gap-3 pl-6 border-l border-gray-200">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($admin['username'] ?? 'Admin'); ?></p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['username'] ?? 'Admin'); ?>&background=667eea&color=fff&size=40" 
                     alt="User Avatar" class="w-10 h-10 rounded-full ring-2 ring-indigo-500/20">
            </div>
        </div>
    </div>
</header>
