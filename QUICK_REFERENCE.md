# Quick Reference Guide

## 📱 URLs

| Component | URL | Purpose |
|-----------|-----|---------|
| Landing Page | `http://localhost/survey/` | Setup information & links |
| Public Survey | `http://localhost/survey/public/` | Submit survey responses |
| Admin Login | `http://localhost/survey/admin/login.php` | Admin authentication |
| Admin Dashboard | `http://localhost/survey/admin/dashboard.php` | View analytics & responses |

## 🔑 Default Credentials

| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `password123` |
| Email | `admin@library.local` |

⚠️ **Change password after first login!**

## 📁 Important Files

| File | Location | Purpose |
|------|----------|---------|
| Database Schema | `database/survey.sql` | Import to create database |
| Configuration | `api/db_config.php` | Database credentials |
| Survey Form | `public/index.php` | Public survey page |
| Admin Dashboard | `admin/dashboard.php` | Admin interface |
| API Endpoints | `api/*.php` | Backend APIs |
| Styles | `assets/styles.css` | Custom CSS |
| Scripts | `assets/*.js` | JavaScript code |

## 🛠️ Configuration

### Database Credentials (api/db_config.php)
```php
DB_HOST = 'localhost'
DB_USER = 'root'
DB_PASS = ''
DB_NAME = 'library_survey'
```

### Modify Survey Questions
Edit `public/index.php` - Search for form input sections

## 📊 Database Tables

### users
- Stores admin user accounts
- Fields: id, username, password, email, created_at

### survey_responses
- Stores all survey submissions
- Fields: id, visitor_name, visitor_email, visit_frequency, purpose, ratings, feedback, created_at

### survey_categories
- Reference data for survey types
- Fields: id, category_name, description, created_at

## 🔌 API Endpoints Summary

### Survey Submission
```
POST /api/submit_survey.php
```
Submit a new survey response

### Analytics
```
GET /api/analytics.php?action=total_responses
GET /api/analytics.php?action=average_ratings
GET /api/analytics.php?action=visit_frequency
GET /api/analytics.php?action=satisfaction_distribution
GET /api/analytics.php?action=daily_submissions
GET /api/analytics.php?action=recommendation_breakdown
GET /api/analytics.php?action=all_responses&limit=10&offset=0
GET /api/analytics.php?action=response_detail&id=1
```

### Admin
```
POST /admin/api/login.php
```
Authenticate admin user

## 🎨 Color Scheme

- **Primary**: Indigo (#667eea)
- **Accent**: Purple (#764ba2)
- **Success**: Green (#22c55e)
- **Warning**: Orange (#f97316)
- **Error**: Red (#ef4444)

## 📦 External Libraries

- **Tailwind CSS 3**: `https://cdn.tailwindcss.com`
- **Font Awesome 6.4.0**: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css`
- **Chart.js 3.9.1**: `https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js`

## ⌚ Timestamps

All records include `created_at` timestamps in MySQL format: `YYYY-MM-DD HH:MM:SS`

## 🔍 Common Tasks

### View All Survey Responses
```
Admin Dashboard > Survey Responses tab
```

### Add New Admin User
```sql
INSERT INTO users (username, password, email) VALUES 
('newadmin', BCRYPT_HASH('password'), 'email@domain.com');
```

### Export Survey Data
Use database export from phpMyAdmin or command line:
```bash
mysqldump -u root -p library_survey > backup.sql
```

### Reset Admin Password
```sql
UPDATE users SET password = '$2y$10$YIjlrDn2.PneOVz75BCKNe.pHJYABT8KqOgkxShsHkiVIZaGIW0dO' 
WHERE username = 'admin';
```

### Clear All Survey Data
```sql
TRUNCATE TABLE survey_responses;
```

## 🚨 Troubleshooting

| Issue | Solution |
|-------|----------|
| Database connection error | Check credentials in `api/db_config.php` |
| Login not working | Verify admin user exists in database |
| Survey not submitting | Check browser console (F12) for JavaScript errors |
| Charts not showing | Verify Chart.js CDN is accessible |
| Styling broken | Clear browser cache (Ctrl+Shift+Del) |

## 📝 Survey Rating Scale

### Main Satisfaction (1-5)
1. Very Poor 😠
2. Poor 😞
3. Average 😐
4. Good 😊
5. Excellent 😄

### Other Ratings (1-5)
1. Poor
2. Fair
3. Good
4. Very Good
5. Excellent

### Recommendation (0-4)
0. Definitely Not
1. Probably Not
2. Neutral
3. Probably Yes
4. Definitely Yes

## 🔐 Security Checklist

- [ ] Change default admin password
- [ ] Verify .htaccess file is in place
- [ ] Use HTTPS in production
- [ ] Regular database backups
- [ ] Check file permissions (755 for folders, 644 for files)
- [ ] Update PHP and MySQL regularly

## 📚 Documentation

- **Full Documentation**: README.md
- **Setup Instructions**: SETUP.md
- **API Testing Guide**: API_TESTING.md
- **Configuration Template**: config.example.php
- **This File**: QUICK_REFERENCE.md

## 💬 Feedback Section

Visitors can leave optional feedback to improve the library. View recent feedback in the admin dashboard.

## 📈 Analytics Metrics

- **Total Responses**: All survey submissions
- **Average Ratings**: Mean of all ratings by category
- **Visit Frequency**: Distribution of how often people visit
- **Satisfaction Distribution**: Breakdown of satisfaction ratings
- **Daily Submissions**: Number of responses per day
- **Recommendation Rate**: Percentage likely to recommend

## 🎯 Next Steps

1. Import database (SETUP.md)
2. Create admin user (SETUP.md)
3. Access public survey and test
4. Log in to admin dashboard
5. View analytics and responses
6. Customize as needed

---

**For detailed information, see README.md**
