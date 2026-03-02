# Library Survey System

A complete survey system for libraries with a public-facing survey form and an admin dashboard for analytics.

## Features

### Public Survey Form
- **User-Friendly Interface**: Built with Tailwind CSS for a modern, responsive experience
- **Multiple Rating Types**: Satisfaction ratings, book availability, staff helpfulness, facilities, and recommendation
- **Feedback Collection**: Optional text area for visitor suggestions
- **Form Validation**: Client-side and server-side validation
- **Icon Support**: Uses Font Awesome icons with Lucide-inspired design
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices

### Admin Dashboard
- **Secure Login**: Username and password authentication
- **Analytics Overview**: 
  - Total responses count
  - Average ratings across all dimensions
  - Visitor satisfaction distribution pie chart
  - Visit frequency bar chart
  - Recommendation breakdown
  - Daily submission trends
- **Detail View**: Click on responses to see full survey details
- **Settings Page**: Update admin password (future implementation)
- **Responsive Layout**: Sidebar navigation with tab-based content

## Project Structure

```
survey/
├── index.php                    # Landing page
├── public/
│   └── index.php               # Public survey form
├── admin/
│   ├── login.php               # Admin login
│   ├── dashboard.php           # Admin dashboard
│   └── api/
│       └── login.php           # Login API
├── api/
│   ├── db_config.php           # Database configuration
│   ├── submit_survey.php       # Survey submission API
│   └── analytics.php           # Analytics data API
├── assets/
│   ├── styles.css              # Custom CSS
│   ├── script.js               # Public form JavaScript
│   └── dashboard.js            # Dashboard JavaScript
└── database/
    └── survey.sql              # Database schema
```

## Installation

### 1. Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP, WAMP, or similar local server

### 2. Database Setup

Import the SQL file into your MySQL database:

```bash
mysql -u root -p library_survey < database/survey.sql
```

Or manually run the SQL from `database/survey.sql` in phpMyAdmin.

### 3. Create Admin User

Run this SQL query to create the default admin account:

```sql
INSERT INTO users (username, password, email) VALUES 
('admin', '$2y$10$YIjlrDn2.PneOVz75BCKNe.pHJYABT8KqOgkxShsHkiVIZaGIW0dO', 'admin@library.local');
```

**Default Credentials:**
- Username: `admin`
- Password: `password123`

### 4. Configure Database (if needed)

Edit `api/db_config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'library_survey');
```

### 5. Access the Application

- **Public Survey**: `http://localhost/survey/public/`
- **Admin Login**: `http://localhost/survey/admin/login.php`
- **Setup Page**: `http://localhost/survey/`

## Usage

### For Visitors
1. Navigate to the public survey form
2. Fill in your name and optional email
3. Select your visit frequency and purpose
4. Rate various aspects of the library (1-5 scale)
5. Optionally provide feedback
6. Submit the survey

### For Admins
1. Log in with your credentials
2. View dashboard with key metrics and charts
3. Click "Survey Responses" to see all submissions
4. Click "View" on any response to see full details
5. Analyze trends using the Analytics tab

## Database Schema

### Users Table
- `id`: Primary key
- `username`: Unique username for admin
- `password`: Hashed password
- `email`: Admin email
- `created_at`: Account creation timestamp

### Survey Responses Table
- `id`: Primary key
- `visitor_name`: Name of the survey taker
- `visitor_email`: Optional email
- `visit_frequency`: How often they visit (daily, weekly, monthly, occasionally, first_time)
- `purpose`: Purpose of their visit
- `satisfaction`: Satisfaction rating (1-5)
- `book_availability`: Book availability rating (1-5)
- `staff_helpfulness`: Staff rating (1-5)
- `facilities_rating`: Facilities rating (1-5)
- `would_recommend`: Would recommend rating (0-4)
- `improvements_feedback`: Optional text feedback
- `created_at`: Submission timestamp

## API Endpoints

### Public API
- `POST /api/submit_survey.php` - Submit survey response

### Analytics API (Admin)
- `GET /api/analytics.php?action=total_responses` - Get total response count
- `GET /api/analytics.php?action=average_ratings` - Get average ratings
- `GET /api/analytics.php?action=visit_frequency` - Get visit frequency distribution
- `GET /api/analytics.php?action=satisfaction_distribution` - Get satisfaction ratings breakdown
- `GET /api/analytics.php?action=daily_submissions` - Get daily submission counts
- `GET /api/analytics.php?action=recommendation_breakdown` - Get recommendation breakdown
- `GET /api/analytics.php?action=all_responses&limit=X&offset=Y` - Get all responses with pagination
- `GET /api/analytics.php?action=response_detail&id=X` - Get specific response details

### Admin API
- `POST /admin/api/login.php` - Admin login

## Technologies Used

- **Frontend**: HTML5, CSS3 (Tailwind CSS), JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Charts**: Chart.js 3.9.1
- **Icons**: Font Awesome 6.4.0
- **UI Framework**: Tailwind CSS 3

## Security Features

- Server-side form validation
- Prepared statements for SQL queries (SQL injection prevention)
- Password hashing (bcrypt)
- Session-based authentication for admin
- CSRF protection ready (can be enhanced)

## Customization

### Change Admin Credentials
Update the SQL insert query with your preferred username and password hash.

### Modify Survey Questions
Edit the form in `public/index.php` to add or remove survey questions.

### Customize Colors
- Tailwind CSS classes are embedded in HTML
- Custom colors in `assets/styles.css`
- Color scheme is primarily indigo/purple with gradients

### Add More Analytics
Update `api/analytics.php` to add new analytics endpoints and create corresponding dashboard sections.

## Troubleshooting

### Database Connection Error
- Check `api/db_config.php` database credentials
- Ensure MySQL server is running
- Verify database name is correct

### Admin Login Not Working
- Ensure user was created in the database
- Check that MySQL password is empty (or update config)
- Try clearing browser cache and cookies

### Charts Not Displaying
- Ensure Chart.js CDN is accessible
- Check browser console for JavaScript errors
- Verify analytics API is returning valid data

### Styling Issues
- Clear browser cache (Ctrl+Shift+Del)
- Check Tailwind CSS CDN is loading
- Verify `assets/styles.css` is being loaded

## Future Enhancements

- Export survey data to Excel/PDF
- Email notifications for new feedback
- Advanced filtering and searching
- Multi-language support
- User roles and permissions
- Survey scheduling
- Custom survey builder
- Data visualization enhancements

## License

This project is open-source and available for modification and distribution.

## Support

For issues or questions, refer to the setup page at `http://localhost/survey/`

---

**Version**: 1.0  
**Last Updated**: February 2026
