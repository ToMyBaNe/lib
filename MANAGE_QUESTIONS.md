# Survey Questions Management

Complete CRUD system for managing survey questions in the admin panel.

## Getting Started

### Step 1: Set Up Questions Table
1. Open your browser and go to: `http://localhost/survey/setup_questions.php`
2. You should see output confirming:
   - ✓ Connected to MySQL
   - ✓ Database selected
   - ✓ Table 'survey_questions' created/exists
   - ✓ Default questions inserted

### Step 2: Access the Management Panel
1. Go to admin login: `http://localhost/survey/admin/login.php`
2. Log in with your admin credentials
3. Click on **"Manage Questions"** in the sidebar
4. You'll see the full list of survey questions

## Features

### Create Questions
1. Click **"Add Question"** button
2. Fill in the question text
3. Select question type:
   - **Text Input** - Free text answer
   - **Rating Scale** - 1-5 rating
   - **Dropdown/Select** - Single choice from options
   - **Multiple Choice** - Multiple selections
4. Add category (optional)
5. Mark as required or optional
6. Add options (if applicable)
7. Click **"Save Question"**

### Edit Questions
1. Click the **Edit** button (blue pencil icon) on any question
2. Modify the question details
3. Click **"Save Question"**

### Delete Questions
1. Click the **Delete** button (red trash icon) on any question
2. Confirm the deletion
3. Question will be removed from the survey

### View Questions
- All questions are displayed in order
- Shows question type, category, and required status
- Drag handle (≡) shows reorderable questions

## Question Types

### Text Input
- Free-form text answer
- Examples: Name, Email, Purpose of visit

### Rating Scale
- Numbered scale (typically 1-5)
- Examples: Satisfaction rating, Staff helpfulness

### Dropdown/Select
- Predefined options dropdown
- User selects one option
- Examples: Visit frequency, Recommendation

### Multiple Choice
- Checkboxes for multiple selections
- User can select multiple options
- Examples: Resource types used, Services interested in

## Database Structure

```sql
CREATE TABLE survey_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_text VARCHAR(500) NOT NULL,
    question_type ENUM('text', 'rating', 'select', 'checkbox') NOT NULL DEFAULT 'text',
    category VARCHAR(100),
    required BOOLEAN DEFAULT 1,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    options JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## API Endpoints

### List All Questions
```
GET /admin/api/questions.php?action=list
Response: { success: true, data: [...], count: N }
```

### Get Single Question
```
GET /admin/api/questions.php?action=get&id=1
Response: { success: true, data: {...} }
```

### Create Question
```
POST /admin/api/questions.php?action=create
Body: FormData with question details
Response: { success: true, id: N }
```

### Update Question
```
POST /admin/api/questions.php?action=update
Body: FormData with updated question details
Response: { success: true }
```

### Delete Question
```
POST /admin/api/questions.php?action=delete
Body: { action: 'delete', id: 1 }
Response: { success: true }
```

### Reorder Questions
```
POST /admin/api/questions.php?action=reorder
Body: { orders: { 0: 1, 1: 2, 2: 3, ... } }
Response: { success: true }
```

## Default Questions

The system comes with 10 default questions:

1. **Full Name** - Text
2. **Email Address** - Text (Optional)
3. **How often do you visit the library?** - Select
4. **What was the primary purpose of your visit?** - Text
5. **Overall library satisfaction** - Rating
6. **Book availability and collection** - Rating
7. **Staff helpfulness and knowledge** - Rating
8. **Facilities (cleanliness, comfort, equipment)** - Rating
9. **Would you recommend this library to others?** - Select
10. **What can we improve?** - Text (Optional)

## Custom Questions Example

### Adding a New Question

**Question:** "Which of these services do you use?"
- Type: Multiple Choice
- Required: Yes
- Category: Services
- Options:
  - Book borrowing
  - Reference desk
  - Computer/Internet access
  - Study space
  - Event programs

### Adding a Rating Question

**Question:** "How satisfied are you with our opening hours?"
- Type: Rating Scale
- Required: Yes
- Category: Operations
- Scale: 1-5

## Troubleshooting

### No questions appear
- Ensure `setup_questions.php` has been run
- Check that you're logged in as admin
- Verify database user has proper permissions

### Can't save questions
- Check browser console (F12) for errors
- Verify admin session is active
- Ensure database connection is working

### Options not saving
- Only Rating, Select, and Multiple Choice types save options
- Make sure to add at least one option before saving
- Options should be added via the UI form

## Security Notes

- All operations require admin authentication
- SQL queries use prepared statements to prevent injection
- Questions are soft-deleted (marked inactive) not removed
- User sessions are validated on each request

## Next Steps

After creating questions, the public survey form will automatically display them in the order you set!
