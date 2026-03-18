/**
 * Admin Panel - Unified JavaScript Module
 * Provides global utilities, API helpers, and UI functions
 */

class AdminPanel {
    constructor() {
        this.apiBase = '/admin/api';
        this.init();
    }

    init() {
        console.log('✓ Admin Panel Initialized');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Add any global event listeners here
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeComponents();
        });
    }

    initializeComponents() {
        // Initialize tooltips, popovers, or other components
        console.log('✓ Components Initialized');

        // Sidebar toggle (collapsible)
        const toggleBtn = document.querySelector('[data-sidebar-toggle]');
        const sidebar = document.querySelector('.admin-sidebar');
        const main = document.querySelector('.admin-main');

        if (toggleBtn && sidebar && main) {
            toggleBtn.addEventListener('click', () => {
                const isMobile = window.matchMedia('(max-width: 768px)').matches;

                if (isMobile) {
                    sidebar.classList.toggle('open');
                } else {
                    sidebar.classList.toggle('collapsed');
                    main.classList.toggle('collapsed');
                }
            });
        }

        // "Add More Admins" modal
        const addAdminModal = document.getElementById('addAdminModal');
        const openAddAdminBtn = document.querySelector('[data-open-add-admin]');
        const closeAddAdminButtons = document.querySelectorAll('[data-close-add-admin]');

        if (addAdminModal && openAddAdminBtn) {
            const openModal = () => {
                addAdminModal.classList.remove('hidden');
                addAdminModal.classList.add('flex');
            };
            const closeModal = () => {
                addAdminModal.classList.add('hidden');
                addAdminModal.classList.remove('flex');
            };

            openAddAdminBtn.addEventListener('click', openModal);

            closeAddAdminButtons.forEach(btn => {
                btn.addEventListener('click', closeModal);
            });

            addAdminModal.addEventListener('click', (e) => {
                if (e.target === addAdminModal) closeModal();
            });
        }
    }

    /**
     * Make API request with error handling
     */
    async apiRequest(endpoint, options = {}) {
        const url = `${this.apiBase}/${endpoint}`;
        const config = {
            method: options.method || 'GET',
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        if (config.body && typeof config.body === 'object') {
            config.body = JSON.stringify(config.body);
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }

            return { success: true, data };
        } catch (error) {
            console.error('API Error:', error);
            this.showError(error.message);
            return { success: false, message: error.message };
        }
    }

    /**
     * Show success notification
     */
    showSuccess(message, duration = 3000) {
        this.showNotification(message, 'success', duration);
    }

    /**
     * Show error notification
     */
    showError(message, duration = 4000) {
        this.showNotification(message, 'error', duration);
    }

    /**
     * Show warning notification
     */
    showWarning(message, duration = 3500) {
        this.showNotification(message, 'warning', duration);
    }

    /**
     * Generic notification system
     */
    showNotification(message, type = 'info', duration = 3000) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };

        const colors = {
            success: 'from-green-400 to-green-600',
            error: 'from-red-400 to-red-600',
            warning: 'from-yellow-400 to-yellow-600',
            info: 'from-blue-400 to-blue-600'
        };

        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 bg-gradient-to-r ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3 animate-fade-in`;
        toast.innerHTML = `
            <i class="fas fa-${icons[type]}"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    /**
     * Format date to readable string
     */
    formatDate(dateString) {
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(dateString));
    }

    /**
     * Format number with thousand separator
     */
    formatNumber(number) {
        return Number(number).toLocaleString('en-US');
    }

    /**
     * Copy text to clipboard
     */
    copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(() => this.showSuccess('Copied to clipboard!', 2000))
            .catch(() => this.showError('Failed to copy'));
    }

    /**
     * Confirm action with modal
     */
    confirm(message) {
        return window.confirm(message);
    }

    /**
     * Prompt user for input
     */
    prompt(message, defaultValue = '') {
        return window.prompt(message, defaultValue);
    }
}

// Create global admin instance
const adminPanel = new AdminPanel();

// Backward compatibility functions
function showToast(message, type = 'success', duration = 3000) {
    adminPanel.showNotification(message, type, duration);
}

function showSuccess(message) {
    adminPanel.showSuccess(message);
}

function showError(message) {
    adminPanel.showError(message);
}

function showWarning(message) {
    adminPanel.showWarning(message);
}

function formatTime(dateString) {
    adminPanel.formatDate(dateString);
}

// Expose to window for global access
window.AdminPanel = adminPanel;
window.showToast = showToast;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.formatTime = formatTime;

// Expose apiRequest globally for backward compatibility
const apiRequest = adminPanel.apiRequest.bind(adminPanel);
window.apiRequest = apiRequest;

// Hide/show loading state
function setLoading(element, isLoading) {
    if (isLoading) {
        element.classList.add('loading');
        element.disabled = true;
    } else {
        element.classList.remove('loading');
        element.disabled = false;
    }
}

// Format date
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Confirm action
function confirmAction(message = 'Are you sure?') {
    return confirm(message);
}

// Debug log
function debug(message, data = null) {
    console.log(`[Admin] ${message}`, data);
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    debug('Admin panel loaded');
});
