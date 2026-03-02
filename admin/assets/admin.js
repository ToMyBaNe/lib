/**
 * Admin Global JavaScript Utilities
 */

// Toast notification system
function showToast(message, type = 'success', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, duration);
}

// Show success toast
function showSuccess(message) {
    showToast(message, 'success', 3000);
}

// Show error toast
function showError(message) {
    showToast(message, 'error', 5000);
}

// Show warning toast
function showWarning(message) {
    showToast(message, 'warning', 4000);
}

// API request helper
async function apiRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON response:', text);
            throw new Error('Invalid server response');
        }
        
        if (!response.ok && !data.success) {
            throw new Error(data.message || `HTTP ${response.status}`);
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

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
