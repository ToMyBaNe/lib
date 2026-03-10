<?php
/**
 * Admin Header/Top Navigation Component
 */
$admin = getCurrentAdmin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle = getPageTitle($currentPage);
?>

<header class="admin-header">
    <h1 class="admin-header__title"><?php echo htmlspecialchars($pageTitle); ?></h1>
    <div class="admin-header__user">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['username'] ?? 'Admin'); ?>&background=64748b&color=fff&size=36" alt="" class="admin-header__avatar">
        <span class="admin-header__name"><?php echo htmlspecialchars($admin['username'] ?? 'Admin'); ?></span>
    </div>
</header>
