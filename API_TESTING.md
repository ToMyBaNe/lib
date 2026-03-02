# API Testing Guide

## Testing the Survey API

### Submit a Survey Response

**Endpoint**: `POST /api/submit_survey.php`

**Using cURL**:
```bash
curl -X POST http://localhost/survey/api/submit_survey.php \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "visitor_name=John Doe" \
  -d "visitor_email=john@example.com" \
  -d "visit_frequency=weekly" \
  -d "purpose=Borrow books" \
  -d "satisfaction=5" \
  -d "book_availability=4" \
  -d "staff_helpfulness=5" \
  -d "facilities_rating=4" \
  -d "would_recommend=4" \
  -d "improvements_feedback=Great library! Please add more computer workstations."
```

**Using Python**:
```python
import requests

data = {
    'visitor_name': 'John Doe',
    'visitor_email': 'john@example.com',
    'visit_frequency': 'weekly',
    'purpose': 'Borrow books',
    'satisfaction': 5,
    'book_availability': 4,
    'staff_helpfulness': 5,
    'facilities_rating': 4,
    'would_recommend': 4,
    'improvements_feedback': 'Great library! Please add more computer workstations.'
}

response = requests.post('http://localhost/survey/api/submit_survey.php', data=data)
print(response.json())
```

**Using JavaScript/Fetch**:
```javascript
const formData = new FormData();
formData.append('visitor_name', 'John Doe');
formData.append('visitor_email', 'john@example.com');
formData.append('visit_frequency', 'weekly');
formData.append('purpose', 'Borrow books');
formData.append('satisfaction', 5);
formData.append('book_availability', 4);
formData.append('staff_helpfulness', 5);
formData.append('facilities_rating', 4);
formData.append('would_recommend', 4);
formData.append('improvements_feedback', 'Great library!');

fetch('http://localhost/survey/api/submit_survey.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

## Testing the Analytics API

### Get Total Responses

```bash
curl http://localhost/survey/api/analytics.php?action=total_responses
```

**Response**:
```json
{
    "success": true,
    "data": {
        "total": "25"
    }
}
```

### Get Average Ratings

```bash
curl http://localhost/survey/api/analytics.php?action=average_ratings
```

**Response**:
```json
{
    "success": true,
    "data": {
        "avg_satisfaction": "4.50",
        "avg_book_availability": "4.20",
        "avg_staff_helpfulness": "4.60",
        "avg_facilities": "4.30",
        "avg_recommendation": "3.80"
    }
}
```

### Get Visit Frequency Distribution

```bash
curl http://localhost/survey/api/analytics.php?action=visit_frequency
```

**Response**:
```json
{
    "success": true,
    "data": [
        {
            "visit_frequency": "weekly",
            "count": "15"
        },
        {
            "visit_frequency": "monthly",
            "count": "8"
        },
        {
            "visit_frequency": "daily",
            "count": "2"
        }
    ]
}
```

### Get Satisfaction Distribution

```bash
curl http://localhost/survey/api/analytics.php?action=satisfaction_distribution
```

**Response**:
```json
{
    "success": true,
    "data": [
        {
            "satisfaction": "1",
            "count": "0"
        },
        {
            "satisfaction": "2",
            "count": "1"
        },
        {
            "satisfaction": "3",
            "count": "5"
        },
        {
            "satisfaction": "4",
            "count": "12"
        },
        {
            "satisfaction": "5",
            "count": "7"
        }
    ]
}
```

### Get Daily Submissions

```bash
curl http://localhost/survey/api/analytics.php?action=daily_submissions
```

**Response**:
```json
{
    "success": true,
    "data": [
        {
            "date": "2026-02-23",
            "count": "5"
        },
        {
            "date": "2026-02-22",
            "count": "3"
        }
    ]
}
```

### Get Recommendation Breakdown

```bash
curl http://localhost/survey/api/analytics.php?action=recommendation_breakdown
```

**Response**:
```json
{
    "success": true,
    "data": [
        {
            "label": "Definitely Not",
            "value": 0,
            "count": 1
        },
        {
            "label": "Probably Not",
            "value": 1,
            "count": 2
        },
        {
            "label": "Neutral",
            "value": 2,
            "count": 3
        },
        {
            "label": "Probably Yes",
            "value": 3,
            "count": 8
        },
        {
            "label": "Definitely Yes",
            "value": 4,
            "count": "11"
        }
    ]
}
```

### Get All Responses (Paginated)

```bash
curl "http://localhost/survey/api/analytics.php?action=all_responses&limit=10&offset=0"
```

**Parameters**:
- `limit`: Number of responses to return (default: 100, max: 1000)
- `offset`: Number of responses to skip for pagination (default: 0)

**Response**:
```json
{
    "success": true,
    "data": [
        {
            "id": "1",
            "visitor_name": "John Doe",
            "visitor_email": "john@example.com",
            "visit_frequency": "weekly",
            "purpose": "Borrow books",
            "satisfaction": "5",
            "book_availability": "4",
            "staff_helpfulness": "5",
            "facilities_rating": "4",
            "would_recommend": "4",
            "improvements_feedback": "Great library!",
            "created_at": "2026-02-23 10:30:00"
        }
    ]
}
```

### Get Single Response Details

```bash
curl "http://localhost/survey/api/analytics.php?action=response_detail&id=1"
```

**Response**:
```json
{
    "success": true,
    "data": {
        "id": "1",
        "visitor_name": "John Doe",
        "visitor_email": "john@example.com",
        "visit_frequency": "weekly",
        "purpose": "Borrow books",
        "satisfaction": "5",
        "book_availability": "4",
        "staff_helpfulness": "5",
        "facilities_rating": "4",
        "would_recommend": "4",
        "improvements_feedback": "Great library! Please add more computer workstations.",
        "created_at": "2026-02-23 10:30:00"
    }
}
```

## Testing the Login API

### Admin Login

**Endpoint**: `POST /admin/api/login.php`

**Using cURL**:
```bash
curl -X POST http://localhost/survey/admin/api/login.php \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=admin" \
  -d "password=password123"
```

**Response** (Success):
```json
{
    "success": true,
    "message": "Login successful",
    "token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6"
}
```

**Response** (Failure):
```json
{
    "success": false,
    "message": "Invalid username or password"
}
```

## Error Responses

### Invalid Request

```json
{
    "success": false,
    "message": "Action parameter required"
}
```

### Validation Error (Survey Submission)

```json
{
    "success": false,
    "message": "Visitor name is required"
}
```

### Database Error

```json
{
    "success": false,
    "message": "Failed to insert survey response: [error details]"
}
```

## Rate Limiting

The API doesn't currently implement rate limiting, but consider adding it in production:

```php
// Example rate limiting implementation
$rate_limit = 100; // requests per minute
$client_ip = $_SERVER['REMOTE_ADDR'];
// Check rate limit before processing...
```

## CORS Headers

The API currently doesn't set CORS headers. For cross-origin requests, add to `api/analytics.php`:

```php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

## Security Notes

1. **SQL Injection**: All queries use prepared statements
2. **XSS Protection**: Ensure frontend validates input
3. **CSRF Protection**: Implement in production
4. **Authentication**: Ensure admin endpoints require authentication
5. **HTTPS**: Use HTTPS in production

## Performance Tips

1. Add database indexes for frequently queried columns
2. Implement caching for analytics queries
3. Use pagination for large response lists
4. Monitor query performance with MySQL EXPLAIN

---

For more information, see README.md and SETUP.md
