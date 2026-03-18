<?php
/**
 * Admin Header/Top Navigation Component
 */
$admin = getCurrentAdmin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle = getPageTitle($currentPage);
?>

<header class="mb-6 flex items-center justify-between rounded-2xl bg-brand-green px-4 py-3 shadow-sm sm:px-6">
    <div class="flex items-center gap-3">
        <button
            type="button"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-200 bg-white/90 text-emerald-800 shadow-sm hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-brand-gold/70 lg:h-8 lg:w-8"
            data-sidebar-toggle
            aria-label="Toggle sidebar"
        >
            <i class="fas fa-bars text-sm"></i>
        </button>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-100">BASC Library Survey</p>
        <h1 class="mt-1 text-xl font-semibold text-white">
            <?php echo htmlspecialchars($pageTitle); ?>
        </h1>
    </div>

    <div class="flex items-center gap-3">
        <div class="hidden text-right sm:block">
            <p class="text-sm font-medium text-emerald-50">
                <?php echo htmlspecialchars($admin['username'] ?? 'Admin'); ?>
            </p>
            <p class="text-xs text-emerald-100/80">Administrator</p>
        </div>
        <div class="relative">
            <span class="absolute -right-0.5 -top-0.5 h-3 w-3 rounded-full border border-emerald-900 bg-brand-gold shadow-sm"></span>
            <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['username'] ?? 'Admin'); ?>&background=16a34a&color=fff&size=40"
                alt=""
                class="h-10 w-10 rounded-full border-2 border-emerald-100/80 shadow-sm"
            >
        </div>
    </div>
</header>
