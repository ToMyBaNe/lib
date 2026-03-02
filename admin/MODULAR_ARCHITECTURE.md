# Admin Panel Modular Architecture

## Overview

The admin panel has been refactored from a monolithic structure to a modular, component-based architecture. This makes the codebase more maintainable and easier to extend.

## Directory Structure

```
admin/
├── config.php                    # Central configuration and helper functions
├── login.php                     # Admin login page
├── dashboard.php                 # Dashboard wrapper (11 lines)
├── manage_questions.php          # Questions management wrapper (12 lines)
├── responses.php                 # Survey responses wrapper (12 lines)
├── settings.php                  # Settings wrapper (12 lines)
├── api/
│   ├── login.php                # Login endpoint
│   └── questions.php            # Questions CRUD API
├── components/
│   ├── sidebar.php              # Reusable navigation sidebar
│   └── header.php               # Reusable page header
├── layouts/
│   └── base.php                 # Master layout template
├── pages/
│   ├── dashboard-content.php    # Dashboard content template
│   ├── questions-content.php    # Questions management content
│   ├── responses-content.php    # Responses listing content
│   └── settings-content.php     # Settings page content
├── assets/
│   ├── admin.css                # Shared admin styling
│   ├── admin.js                 # Shared admin utilities
│   ├── questions.css            # Questions-specific styles
│   ├── questions.js             # Questions-specific JavaScript
│   ├── responses.css            # Responses-specific styles
│   ├── responses.js             # Responses-specific utilities
│   ├── settings.css             # Settings-specific styles
│   └── settings.js              # Settings-specific utilities
└── setup_questions.php          # Database setup script
```

## How It Works

### Page Flow

1. **Page Load** (e.g., `manage_questions.php`)
   ```php
   <?php
   session_start();
   require_once 'config.php';           // Load configuration
   requireAdminAuth();                  // Check authentication
   
   $pageTitle = '...';                  // Set page title
   $contentFile = './pages/...';        // Set content template
   $additionalCss = [...];              // Optional page-specific styles
   $additionalScripts = [...];          // Optional page-specific scripts
   
   require_once './layouts/base.php';   // Load master layout
   ?>
   ```

2. **Master Layout** (`layouts/base.php`)
   - Requires config.php if not already loaded
   - Sets up HTML document structure
   - Includes reusable components:
     - `components/sidebar.php` - Navigation menu
     - `components/header.php` - Page header with title and user info
   - Includes global CSS/JS utilities
   - Includes page-specific CSS/JS
   - Loads the content file specified by `$contentFile`

3. **Components** (`components/`)
   - **sidebar.php**: Dynamic navigation using `$pages` array from config.php
   - **header.php**: Page title and user information
   - Both are automatically included by base.php

4. **Content** (`pages/`)
   - Each page has a corresponding content file
   - Contains only the page-specific HTML
   - No duplicate headers, sidebars, or template code
   - Can include inline scripts specific to that page

5. **Styling** (`assets/`)
   - **admin.css**: Global admin panel styles
     - CSS variables for colors and animations
     - Component styles (buttons, cards, forms, modals, etc.)
     - Responsive design
   - **admin.js**: Global utilities
     - `showToast()`, `showSuccess()`, `showError()` - Toast notifications
     - `apiRequest()` - Fetch wrapper with error handling
     - `escapeHtml()` - XSS prevention
     - `formatDate()` - Date formatting
     - Other helper functions
   - **{page}.css/js**: Page-specific styling and functionality

## Key Files

### config.php
Central configuration file defining:
- `$pages` array - Menu structure
- `requireAdminAuth()` - Authentication check
- `getCurrentAdmin()` - Get current user info
- `getCurrentPageName()` - Get current page name
- `getPageTitle()` - Get page title

### layouts/base.php
Master template that:
- Ensures config.php is loaded
- Sets up HTML document with Tailwind CSS and Font Awesome
- Includes components and content
- Loads page-specific assets

### components/sidebar.php
Dynamic sidebar that:
- Uses `$pages` array from config.php
- Highlights active page based on current URL
- Links to all admin pages
- Includes logout button

### components/header.php
Page header that:
- Displays current page title using `getPageTitle()`
- Shows logged-in username
- Displays user avatar

### assets/admin.css
Comprehensive admin styling:
- CSS variables for consistent theming
- `.sidebar`, `.sidebar-link` - Sidebar styles
- `.stat-card`, `.admin-card` - Card components
- `.btn` classes - Button styles
- `.form-*` classes - Form element styles
- `.toast` - Toast notification styles
- `.admin-table` - Table styles
- Animations and responsive design

### assets/admin.js
Shared utilities:
```javascript
showToast(message, type, duration)  // Type: success, error, warning, info
showSuccess(message)                // Green toast
showError(message)                  // Red toast
showWarning(message)                // Orange toast
apiRequest(url, options)            // Fetch wrapper with error handling
setLoading(element, isLoading)      // Toggle loading state
formatDate(date)                    // Format date ISO -> readable
escapeHtml(text)                    // Escape HTML entities
confirmAction(message)              // Show confirmation dialog
debug(message, data)                // Console debug
```

## HTML Code Sharing Classes

Instead of duplicating HTML, use CSS classes defined in admin.css:

### Buttons
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-danger">Delete</button>
```

### Cards
```html
<div class="stat-card">Statistics Card</div>
<div class="admin-card">Content Card</div>
```

### Forms
```html
<input type="text" class="form-input">
<select class="form-select"></select>
<textarea class="form-input"></textarea>
```

### Tables
```html
<table class="admin-table">
  <thead class="bg-gray-50">...</thead>
  <tbody>...</tbody>
</table>
```

### Modals
```html
<div class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg p-6">...</div>
</div>
```

## Adding a New Page

1. **Create page wrapper** (`admin/new_page.php`):
   ```php
   <?php
   session_start();
   require_once 'config.php';
   requireAdminAuth();
   
   $pageTitle = 'New Page Title';
   $contentFile = './pages/new_page-content.php';
   
   require_once './layouts/base.php';
   ?>
   ```

2. **Create content template** (`admin/pages/new_page-content.php`):
   ```html
   <!-- Your page content here -->
   <h2>Page Content</h2>
   ```

3. **Add to config.php** - Add entry to `$pages` array:
   ```php
   [
       'name' => 'New Page',
       'icon' => 'fas fa-icon-name',
       'file' => 'new_page.php',
       'path' => './new_page.php'
   ],
   ```

4. **Optional**: Create page-specific CSS/JS:
   - `admin/assets/new_page.css`
   - `admin/assets/new_page.js`
   - Reference in page wrapper:
     ```php
     $additionalCss = ['./assets/new_page.css'];
     $additionalScripts = ['./assets/new_page.js'];
     ```

## JavaScript Patterns

### Toast Notifications
```javascript
showSuccess('Action completed!');
showError('Something went wrong');
showWarning('Please be careful');

showToast('Custom message', 'info', 5000);
```

### API Requests
```javascript
async function loadData() {
    try {
        const data = await apiRequest('./api/questions.php?action=list');
        if (!data.success) {
            throw new Error(data.message);
        }
        console.log(data.data);
    } catch (error) {
        showError(error.message);
    }
}
```

### Loading States
```javascript
const button = document.getElementById('submitBtn');
setLoading(button, true);
// ... do something ...
setLoading(button, false);
```

### Confirmation Dialogs
```javascript
if (confirm('Are you sure?')) {
    // Proceed with action
}
```

## Benefits of This Architecture

1. **DRY Principle** - No duplicate HTML, CSS, or common JS
2. **Maintainability** - Each page is 10-12 lines; easy to understand
3. **Consistency** - Shared styling and utilities ensure uniform look
4. **Scalability** - Adding new pages is quick and straightforward
5. **Performance** - Reusable components reduce code duplication
6. **Extensibility** - Page-specific CSS/JS when needed, but optional
7. **Security** - Centralized authentication and XSS prevention

## CSS Variables

Customize the theme by modifying CSS variables in `admin.css`:

```css
:root {
    --color-primary: #4f46e5;      /* Indigo */
    --color-primary-dark: #4338ca; /* Darker indigo */
    --color-success: #10b981;      /* Green */
    --color-danger: #ef4444;       /* Red */
    --color-warning: #f59e0b;      /* Orange */
    --color-info: #3b82f6;         /* Blue */
}
```

## Migration from Old Structure

The old monolithic pages are replaced with minimal wrappers:
- Old: 292 lines of HTML/JS/CSS in one file
- New: 12-line page wrapper + separate content/styling/JS templates

Only the fundamental files needed for each page are loaded, significantly reducing page size and improving maintainability.

## Security

- Session-based authentication via `requireAdminAuth()`
- Input escaping with `escapeHtml()` utility
- CSRF protection possible with upcoming token system
- API endpoints check session validity
- Prepared statements in SQL queries

## Next Steps

- Implement responses API and detail view
- Add analytics/charts functionality
- Create settings persistence system
- Add user management if needed
- Implement batch operations for questions
