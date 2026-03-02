<!-- Admin Sidebar Navigation -->
<?php
// Get current page name for active highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="w-64 sidebar fixed h-full">
    <div class="p-6 text-white">
        <div class="flex items-center gap-3 mb-8">
            <i class="fas fa-book text-2xl"></i>
            <h1 class="text-2xl font-bold">LibrarySurvey</h1>
        </div>
        
        <nav class="space-y-2">
            <?php 
            // Use $pages array from config.php
            if (isset($pages)) {
                foreach ($pages as $page): 
                    $pageFileName = str_replace('.php', '', $page['file']);
                    $isActive = ($currentPage === $pageFileName);
            ?>
                <a href="<?php echo $page['path']; ?>" 
                   class="sidebar-link <?php echo $isActive ? 'active' : ''; ?>">
                    <i class="<?php echo $page['icon']; ?> mr-3"></i> <?php echo $page['name']; ?>
                </a>
            <?php 
                endforeach;
            }
            ?>
        </nav>
    </div>
    
    <div class="absolute bottom-0 w-64 p-6 bg-indigo-700">
        <a href="login.php?action=logout" class="text-white hover:text-indigo-200 flex items-center gap-2">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>

