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
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'BASC | Library Survey System'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind (CDN) with custom BASC library flat theme -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            green: '#16a34a',
                            gold: '#fbbf24',
                            dark: '#064e3b'
                        }
                    },
                    borderRadius: {
                        'lg': '0.75rem',
                        'xl': '1rem',
                        '2xl': '1.25rem'
                    }
                }
            }
        };
    </script>

    <!-- Flowbite (Tailwind components) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.1/flowbite.min.css">

    <!-- Icons & legacy styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="./assets/admin.css">
    <?php if (isset($additionalCss)): ?>
        <?php foreach ($additionalCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="admin-body">
    <div class="admin-shell">
        <!-- Sidebar -->
        <?php require_once './components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <?php require_once './components/header.php'; ?>
            
            <!-- Page Content -->
            <div class="admin-content">
                <?php include $contentFile; ?>
            </div>
        </main>
    </div>

    <!-- Add Admin Modal -->
    <div id="addAdminModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-xl ring-1 ring-emerald-100">
            <div class="flex items-center justify-between border-b border-emerald-50 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-emerald-950">Add admin user</h2>
                    <p class="text-xs text-emerald-700/80">Create another account that can access this dashboard.</p>
                </div>
                <button type="button" class="text-emerald-700 hover:text-emerald-900" data-close-add-admin>
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form method="POST" action="./create-user.php" class="space-y-4 px-5 py-5">
                <input type="hidden" name="action" value="create_user">
                
                <div>
                    <label class="mb-1 block text-sm font-medium text-emerald-900">Username</label>
                    <input type="text" name="create_username" required
                        class="block w-full rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-950 shadow-sm outline-none ring-0 focus:border-brand-green focus:ring-2 focus:ring-brand-green/30">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-emerald-900">Email</label>
                    <input type="email" name="create_email" required
                        class="block w-full rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-950 shadow-sm outline-none ring-0 focus:border-brand-green focus:ring-2 focus:ring-brand-green/30">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-emerald-900">Password</label>
                    <input type="password" name="create_password" required
                        class="block w-full rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-950 shadow-sm outline-none ring-0 focus:border-brand-green focus:ring-2 focus:ring-brand-green/30">
                </div>

                <div class="mt-2 flex items-center justify-end gap-2 border-t border-emerald-50 pt-4">
                    <button type="button" class="btn btn-secondary" data-close-add-admin>Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-1"></i> Create admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Global Scripts -->
    <script src="./assets/admin.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.1/flowbite.min.js"></script>
    
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
