<?php
/**
 * Reusable Admin Layout Template
 */

// Ensure config is loaded
if (!function_exists('requireAdminAuth')) {
    require_once dirname(__FILE__) . '/../config.php';
}

// Verify auth
requireAdminAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Admin' : 'Admin Panel'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="./assets/admin.css">
    <?php if (isset($additionalCss)): ?>
        <?php foreach ($additionalCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php require_once './components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 ml-64 overflow-auto flex flex-col">
            <!-- Header -->
            <?php require_once './components/header.php'; ?>
            
            <!-- Page Content -->
            <div class="flex-1 p-8 overflow-auto">
                <?php include $contentFile; ?>
            </div>
        </main>
    </div>

    <!-- Global Scripts -->
    <script src="./assets/admin.js"></script>
    
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
