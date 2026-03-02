<!-- Admin Header/Top Navigation -->
<?php
$admin = getCurrentAdmin();
?>

<div class="bg-white shadow-sm p-6 flex justify-between items-center border-b sticky top-0 z-40">
    <div>
        <h1 class="text-3xl font-bold text-gray-900" id="pageTitle">
            <?php echo getPageTitle(basename($_SERVER['PHP_SELF'], '.php')); ?>
        </h1>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-gray-600"><?php echo htmlspecialchars($admin['username']); ?></span>
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['username']); ?>&background=667eea&color=fff" 
             alt="Avatar" class="w-10 h-10 rounded-full">
    </div>
</div>
