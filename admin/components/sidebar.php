<?php
/**
 * Admin Sidebar Navigation Component
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="admin-sidebar">
    <div class="admin-sidebar__brand">
        <a href="./dashboard.php" class="admin-sidebar__brand-link">
            <span class="admin-sidebar__logo"><i class="fas fa-chart-pie"></i></span>
            <span class="admin-sidebar__title">Survey Admin</span>
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
        <a href="./create-user.php" class="admin-sidebar__link admin-sidebar__link--sm <?php echo $currentPage === 'login_debug' ? 'admin-sidebar__link--active' : ''; ?>">
            <i class="fa-solid fa-users admin-sidebar__icon"></i>
            <span>Add More Admins</span>
        </a>
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

