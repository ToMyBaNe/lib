<?php
/**
 * Database migration for survey questions management
 */

echo "=== Survey Questions Table Setup ===\n\n";

$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "✓ Connected to MySQL\n";

if (!$conn->select_db("library_survey")) {
    die("✗ Database not found. Please run setup_database.php first.\n");
}

echo "✓ Database selected\n\n";

// Create survey questions table
$create_questions_table = "
    CREATE TABLE IF NOT EXISTS survey_questions (
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
    )
";

echo "Creating survey_questions table...\n";
if ($conn->query($create_questions_table)) {
    echo "✓ Table 'survey_questions' created/exists\n";
} else {
    echo "✗ Failed to create table: " . $conn->error . "\n";
}

// Insert default survey questions (if table was just created)
echo "\nInserting default survey questions...\n";

$default_questions = [
    "INSERT IGNORE INTO survey_questions (id, question_text, question_type, category, required, display_order, options) VALUES
    (1, 'Full Name', 'text', 'About You', 1, 1, NULL),
    (2, 'Email Address', 'text', 'About You', 0, 2, NULL),
    (3, 'How often do you visit the library?', 'select', 'Your Visit', 1, 3, JSON_OBJECT('options', JSON_ARRAY('Daily', 'Weekly', 'Monthly', 'Occasionally', 'First time'))),
    (4, 'What was the primary purpose of your visit?', 'text', 'Your Visit', 1, 4, NULL),
    (5, 'Overall library satisfaction', 'rating', 'Feedback', 1, 5, JSON_OBJECT('scale', 5)),
    (6, 'Book availability and collection', 'rating', 'Feedback', 1, 6, JSON_OBJECT('scale', 5)),
    (7, 'Staff helpfulness and knowledge', 'rating', 'Feedback', 1, 7, JSON_OBJECT('scale', 5)),
    (8, 'Facilities (cleanliness, comfort, equipment)', 'rating', 'Feedback', 1, 8, JSON_OBJECT('scale', 5)),
    (9, 'Would you recommend this library to others?', 'select', 'Feedback', 1, 9, JSON_OBJECT('options', JSON_ARRAY('Definitely Not', 'Probably Not', 'Neutral', 'Probably Yes', 'Definitely Yes'))),
    (10, 'What can we improve?', 'text', 'Additional Feedback', 0, 10, NULL)"
];

foreach ($default_questions as $sql) {
    if ($conn->query($sql)) {
        echo "✓ Default questions inserted\n";
        break; // Only execute once
    }
}

echo "\n✓ Survey questions table setup complete!\n";
$conn->close();
?>
