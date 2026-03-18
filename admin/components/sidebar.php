<?php
/**
 * Admin Sidebar Navigation Component
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="admin-sidebar bg-white">
    <div class="border-b border-emerald-100 px-4 py-4">
        <a href="./dashboard.php" class="flex items-center gap-3 text-sm font-semibold text-emerald-900">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl text-white">
                <img src="assets/imgs/lib-logo-no-bg.png" alt="">
            </span>
            <span>
                <span class="block text-xs font-medium uppercase tracking-[0.18em] text-emerald-500">BASC</span>
                <span class="block text-sm">LSMS</span>
            </span>
        </a>
    </div>

    <nav class="admin-sidebar__nav">
        <a href="./dashboard.php" class="admin-sidebar__link <?php echo ($currentPage === 'index' || $currentPage === 'dashboard') ? 'admin-sidebar__link--active' : ''; ?>">
            <i class="fas fa-home admin-sidebar__icon"></i>
            <span>Dashboard</span>
        </a>
        <a href="./manage_questions.php" class="admin-sidebar__link <?php echo $currentPage === 'manage_questions' ? 'admin-sidebar__link--active' : ''; ?>">
            <i class="fas fa-question-circle admin-sidebar__icon"></i>
            <span>Questions</span>
        </a>
        <a href="./responses.php" class="admin-sidebar__link <?php echo $currentPage === 'responses' ? 'admin-sidebar__link--active' : ''; ?>">
            <i class="fas fa-inbox admin-sidebar__icon"></i>
            <span>Responses</span>
        </a>
        <a href="./settings.php" class="admin-sidebar__link <?php echo $currentPage === 'settings' ? 'admin-sidebar__link--active' : ''; ?>">
            <i class="fas fa-cog admin-sidebar__icon"></i>
            <span>Settings</span>
        </a>
        <a href="./generate_link.php" class="admin-sidebar__link <?php echo $currentPage === 'generate_link' ? 'admin-sidebar__link--active' : ''; ?>">
            <i class="fa-solid fa-chart-line admin-sidebar__icon"></i>
            <span>Generate Link</span>
        </a>
    </nav>

    <div class="admin-sidebar__tools">
        <span class="admin-sidebar__label">More Options</span>
        <button type="button" data-open-add-admin
            class="admin-sidebar__link admin-sidebar__link--sm w-full text-left">
            <i class="fa-solid fa-users admin-sidebar__icon"></i>
            <span>Add More Admins</span>
        </button>
    </div>

    <div class="admin-sidebar__footer">
        <p class="admin-sidebar__user"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></p>
        <a href="?logout" class="admin-sidebar__logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Log out</span>
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

