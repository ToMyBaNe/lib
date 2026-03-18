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

// Create survey categories table first (if not exists)
$create_categories_table = "
    CREATE TABLE IF NOT EXISTS survey_categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        category_name VARCHAR(100) NOT NULL,
        description TEXT,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_category (category_name)
    )
";

echo "Creating survey_categories table...\n";
if ($conn->query($create_categories_table)) {
    echo "✓ Table 'survey_categories' created/exists\n";
} else {
    echo "✗ Failed to create table: " . $conn->error . "\n";
}

// Insert default categories
echo "Inserting default categories...\n";
$categories_insert = "
    INSERT IGNORE INTO survey_categories (id, category_name, description, display_order) VALUES
    (1, 'About You', 'Visitor personal information', 1),
    (2, 'Your Visit', 'Details about library visit', 2),
    (3, 'Feedback', 'Library satisfaction ratings and feedback', 3),
    (4, 'Additional Feedback', 'Additional comments and suggestions', 4)
";

if ($conn->query($categories_insert)) {
    echo "✓ Default categories inserted\n";
} else {
    echo "✗ Failed to insert categories: " . $conn->error . "\n";
}

// Create survey questions table
$create_questions_table = "
    CREATE TABLE IF NOT EXISTS survey_questions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        category_id INT,
        question VARCHAR(500) NOT NULL,
        question_type ENUM('text', 'textarea', 'select', 'radio', 'checkbox', 'rating') NOT NULL DEFAULT 'text',
        options JSON,
        is_required BOOLEAN DEFAULT 1,
        display_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        is_locked BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY fk_category (category_id) REFERENCES survey_categories(id),
        INDEX idx_category_active (category_id, is_active),
        INDEX idx_display_order (display_order)
    )
";

echo "Creating survey_questions table...\n";
if ($conn->query($create_questions_table)) {
    echo "✓ Table 'survey_questions' created/exists\n";
} else {
    echo "✗ Failed to create table: " . $conn->error . "\n";
}

// Insert default survey questions
echo "\nInserting default survey questions...\n";

$default_questions = "
    INSERT IGNORE INTO survey_questions (id, category_id, question, question_type, is_required, display_order, options, is_active) VALUES
    (1, 1, 'Full Name', 'text', 1, 1, NULL, 1),
    (2, 1, 'Email Address', 'text', 0, 2, NULL, 1),
    (3, 2, 'How often do you visit the library?', 'select', 1, 3, '[\"Daily\", \"Weekly\", \"Monthly\", \"Occasionally\", \"First time\"]', 1),
    (4, 2, 'What was the primary purpose of your visit?', 'text', 1, 4, NULL, 1),
    (5, 3, 'Overall library satisfaction', 'rating', 1, 5, NULL, 1),
    (6, 3, 'Book availability and collection', 'rating', 1, 6, NULL, 1),
    (7, 3, 'Staff helpfulness and knowledge', 'rating', 1, 7, NULL, 1),
    (8, 3, 'Facilities (cleanliness, comfort, equipment)', 'rating', 1, 8, NULL, 1),
    (9, 3, 'Would you recommend this library to others?', 'select', 1, 9, '[\"Definitely Not\", \"Probably Not\", \"Neutral\", \"Probably Yes\", \"Definitely Yes\"]', 1),
    (10, 4, 'What can we improve?', 'textarea', 0, 10, NULL, 1)
";

if ($conn->query($default_questions)) {
    echo "✓ Default questions inserted\n";
} else {
    echo "✗ Failed to insert questions: " . $conn->error . "\n";
}

echo "\n✓ Survey questions table setup complete!\n";
$conn->close();
?>
