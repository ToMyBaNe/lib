# Admin Panel - Complete Refactor Guide

## Overview

The admin panel has been completely refactored to provide a modern, fully-functional survey management system. This guide will help you get started.

## Quick Start

### 1. Login to Admin Panel
- **URL**: `http://localhost/survey/admin/login.php`
- **Default Credentials**: 
  - Username: `admin`
  - Password: `password123`

### 2. Navigate the Dashboard
Once logged in, you'll see the main dashboard with:
- **Total Responses**: All survey responses submitted
- **Today's Responses**: Responses from the current day
- **This Week**: Responses from the last 7 days
- **Questions**: Total active survey questions and categories

## Admin Sections

### Dashboard (`admin/index.php` or `/admin/`)
- Overview of survey statistics
- Quick access to all admin functions
- System status indicator
- Recent activity summaries

### Manage Questions (`admin/manage_questions.php`)
**Features:**
- View all survey questions organized by category
- Create new questions with multiple types:
  - Text input
  - Textarea
  - Select dropdown
  - Radio buttons
  - Checkboxes
  - Rating scale
- Edit existing questions
- Delete questions
- Reorder questions via drag-and-drop
- Activate/deactivate questions

**How to Create a Question:**
1. Click "Add Question" button
2. Fill in question text
3. Select question type
4. Choose category
5. Set options if applicable (for select, radio, checkbox, rating)
6. Mark as required if needed
7. Click "Save"

### Survey Responses (`admin/responses.php`)
**Features:**
- View all submitted survey responses
- Search responses by name or email
- Filter by date range
- Paginate through large result sets
- View individual response details
- Export responses as CSV

**How to View Responses:**
1. Go to Survey Responses section
2. Use search box to find specific responses
3. Click on a response row to see full details
4. Use "Export" button to download as CSV

### Settings (`admin/settings.php`)
**Features:**
- Manage survey categories
- Configure survey behavior
- View system information
- User preferences

## API Endpoints

All API endpoints are in `/admin/api/`

### Questions API
```
GET  /admin/api/questions.php?action=list         - List all questions
GET  /admin/api/questions.php?action=get&id=1     - Get single question
GET  /admin/api/questions.php?action=categories   - List categories
POST /admin/api/questions.php?action=create       - Create question
POST /admin/api/questions.php?action=update&id=1  - Update question
DELETE /admin/api/questions.php?id=1              - Delete question
```

### Responses API
```
GET  /admin/api/responses.php?action=list        - List responses (paginated)
GET  /admin/api/responses.php?action=get&id=1    - Get single response
GET  /admin/api/responses.php?action=analytics   - Get analytics data
GET  /admin/api/responses.php?action=export      - Export as CSV
DELETE /admin/api/responses.php?id=1             - Delete response
```

### Login API
```
POST /admin/api/login.php                        - Authenticate user
```

## Developer Tools

### API Tester (`/api_tester.php`)
Interactive tool to test and verify the public API is working correctly.

### Diagnostic Tool (`admin/diagnostic.php`)
Check database connections and table structures.

### Debug Panel (`admin/login_debug.php`)
Troubleshoot login issues and create test admin users.

## File Structure

```
admin/
├── index.php                    # Main dashboard
├── login.php                    # Login page
├── manage_questions.php         # Question management
├── responses.php                # Responses viewer
├── settings.php                 # Settings page
├── diagnostic.php               # Diagnostics tool
├── login_debug.php             # Debug/setup tool
├── config.php                   # Configuration & auth
├── auth.php                     # Auth functions (legacy)
├── api/
│   ├── questions.php           # Questions CRUD API
│   ├── responses.php           # Responses API
│   ├── analytics.php           # Analytics API
│   ├── login.php               # Login API
│   └── test_responses.php      # Test utility
├── assets/
│   ├── admin.css               # Admin styles
│   ├── admin.js                # Admin utilities
│   ├── questions.js            # Questions page JS
│   ├── responses.js            # Responses page JS
│   ├── settings.js             # Settings page JS
│   └── dashboard.js            # Dashboard JS
├── components/
│   ├── header.php              # Page header
│   └── sidebar.php             # Navigation sidebar
├── layouts/
│   └── base.php                # Base layout
└── pages/
    ├── dashboard-content.php    # Dashboard content
    ├── questions-content.php    # Questions content
    ├── responses-content.php    # Responses content
    └── settings-content.php     # Settings content
```

## Key Features

### 1. Authentication
- Session-based authentication
- Secure password hashing with bcrypt
- Session timeout after period of inactivity
- Logout functionality

### 2. Question Management
- Full CRUD operations
- Multiple question types
- Category organization
- Display ordering
- Conditional display options
- Required field validation

### 3. Response Management
- Search and filter capabilities
- Pagination support
- Detailed response viewing
- CSV export functionality
- Response analytics

### 4. Security
- Input validation and sanitization
- SQL injection prevention via prepared statements
- XSS protection through proper escaping
- CSRF token support
- Role-based access control ready

## Common Tasks

### Create a New Survey Question
```php
// Use the web interface: Manage Questions → Add Question
// Or via API:
POST /admin/api/questions.php?action=create
{
    "question": "Your question text",
    "question_type": "text",
    "category_id": 1,
    "is_required": true,
    "options": []
}
```

### Export Survey Responses
```
GET /admin/api/responses.php?action=export&date_from=2024-01-01&date_to=2024-12-31
```

### Get Survey Analytics
```
GET /admin/api/responses.php?action=analytics
```

## Troubleshooting

### Login Issues
1. Visit `admin/login_debug.php`
2. Check database connectivity
3. Verify admin user exists
4. Review server error logs

### API Not Working
1. Visit `../api_tester.php` to test public API
2. Check `admin/diagnostic.php` for database issues
3. Verify file permissions
4. Check PHP error logs

### Questions Not Displaying
1. Ensure `survey_questions` table has data
2. Check `is_active` field is set to 1
3. Run `setup_questions.php` to populate defaults
4. Verify API returns proper JSON

## Configuration

### Database
Edit `/api/db_config.php` to configure database connection:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'library_survey');
```

### Session Settings
Session configuration in `admin/config.php`:
```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

## Best Practices

1. **Regular Backups**: Backup database regularly
2. **User Management**: Create individual admin accounts for team members
3. **Response Archival**: Archive old responses periodically
4. **Question Review**: Review and update survey questions quarterly
5. **Performance**: Monitor response count and archive large datasets

## Support & Further Development

For issues or feature requests:
1. Check error logs in `/admin/diagnostic.php`
2. Review API responses in `../api_tester.php`
3. Test database connectivity in `admin/diagnostic.php`
4. Check server error logs in PHP error_log

---

**Last Updated**: March 2024
**Admin Panel Version**: 2.0
**Status**: ✓ Production Ready
