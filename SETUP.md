# Setup Guide

Complete step-by-step guide to set up the Library Survey System.

## Prerequisites

Before you start, make sure you have:
- XAMPP, WAMP, or similar local PHP development server installed
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser (Chrome, Firefox, Safari, Edge)

## Step 1: Extract Files

Extract the survey files to your web server directory:
- **XAMPP users**: `C:\xampp\htdocs\survey\`
- **WAMP users**: `C:\wamp\www\survey\`

## Step 2: Create Database

### Option A: Using phpMyAdmin (Recommended for beginners)

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" in the left sidebar
3. Enter database name: `library_survey`
4. Click "Create"
5. Select the new database
6. Click the "Import" tab
7. Click "Choose File" and select `database/survey.sql`
8. Click "Import"

### Option B: Using Command Line

```bash
mysql -u root -p
CREATE DATABASE library_survey;
USE library_survey;
SOURCE /path/to/database/survey.sql;
```

### Option C: Direct SQL

1. Open phpMyAdmin
2. Click "SQL" tab
3. Copy the contents of `database/survey.sql`
4. Paste into the SQL editor
5. Click "Go"

## Step 3: Create Admin User

### Using phpMyAdmin

1. Open phpMyAdmin
2. Select the `library_survey` database
3. Click the "users" table
4. Click "Insert"
5. Fill in the form:
   - **id**: Leave blank (auto-increment)
   - **username**: `admin`
   - **password**: Click the "MD5" or "BCRYPT" dropdown and enter: `$2y$10$YIjlrDn2.PneOVz75BCKNe.pHJYABT8KqOgkxShsHkiVIZaGIW0dO`
   - **email**: `admin@library.local`
   - Click "Go"

### Using Command Line

```bash
mysql -u root -p library_survey
INSERT INTO users (username, password, email) VALUES ('admin', '$2y$10$YIjlrDn2.PneOVz75BCKNe.pHJYABT8KqOgkxShsHkiVIZaGIW0dO', 'admin@library.local');
```

## Step 4: Configure Database Connection (Optional)

If your database credentials are different:

1. Open `api/db_config.php`
2. Update these lines:
   ```php
   define('DB_HOST', 'localhost');  // Your MySQL host
   define('DB_USER', 'root');       // Your MySQL username
   define('DB_PASS', '');           // Your MySQL password
   define('DB_NAME', 'library_survey');  // Database name
   ```
3. Save the file

## Step 5: Verify Installation

### Start Your Local Server

- **XAMPP**: Start Apache and MySQL from the XAMPP Control Panel
- **WAMP**: Click the WAMP icon in the system tray and click "Start All Services"

### Test the Application

1. **Setup Page**: `http://localhost/survey/`
2. **Public Survey**: `http://localhost/survey/public/`
3. **Admin Login**: `http://localhost/survey/admin/login.php`

## Step 6: Default Credentials

### Admin Login
- **URL**: `http://localhost/survey/admin/login.php`
- **Username**: `admin`
- **Password**: `password123`

**⚠️ Important**: Change the password after first login!

## File Permissions

Ensure proper file permissions:

```bash
# For Linux/Mac:
chmod 755 survey/
chmod 755 survey/api/
chmod 755 survey/admin/
chmod 644 survey/api/db_config.php
```

## Troubleshooting

### Issue: "Database connection failed"

**Solutions**:
1. Verify MySQL is running
2. Check database credentials in `api/db_config.php`
3. Confirm database name is `library_survey`
4. Test connection in phpMyAdmin

### Issue: Admin login not working

**Solutions**:
1. Verify admin user exists in database
2. Check username is exactly `admin`
3. Clear browser cache and cookies
4. Try a different browser

### Issue: Survey form not submitting

**Solutions**:
1. Check browser console for JavaScript errors (F12)
2. Verify all required fields are filled
3. Check that `api/submit_survey.php` is accessible
4. Check PHP error logs

### Issue: Charts not showing on dashboard

**Solutions**:
1. Verify Chart.js CDN is accessible
2. Check browser console for JavaScript errors
3. Ensure analytics API endpoint is working
4. Check that responses exist in database

### Issue: Styling looks broken

**Solutions**:
1. Clear browser cache (Ctrl+Shift+Del)
2. Verify Tailwind CSS CDN is accessible
3. Check browser console for CSS errors
4. Try a different browser

## Database Backup

### Create Backup with phpMyAdmin

1. Open phpMyAdmin
2. Select `library_survey` database
3. Click "Export"
4. Select "SQL" format
5. Click "Go" to download

### Create Backup via Command Line

```bash
mysqldump -u root -p library_survey > backup.sql
```

## Restore Database

```bash
mysql -u root -p library_survey < backup.sql
```

## Next Steps

After successful setup:

1. **Test Public Survey**: Fill out the survey form as a visitor
2. **View In Admin Dashboard**: Log in as admin and see responses
3. **Customize**: Modify colors, questions, and settings as needed
4. **Change Default Password**: Update admin password
5. **Create Additional Admins**: Add more admin users if needed

## Security Recommendations

1. Change the default admin password immediately
2. Use HTTPS in production (update `.htaccess`)
3. Keep PHP and MySQL updated
4. Regularly backup your database
5. Use strong passwords for admin accounts
6. Enable SQL injection protection (already implemented)
7. Consider adding two-factor authentication
8. Restrict file access via `.htaccess`

## Support Files

- **README.md**: Complete documentation
- **SETUP.md**: This file
- **config.example.php**: Configuration template
- **.htaccess**: Apache server configuration
- **database/survey.sql**: Database schema

## Getting Help

1. Check the README.md for comprehensive documentation
2. Review browser console (F12) for JavaScript errors
3. Check PHP error logs
4. Verify database contents in phpMyAdmin
5. Ensure all files are in the correct directories

---

**Setup Complete!** You're ready to use the Library Survey System.

For more information, see README.md
