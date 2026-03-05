-- Library Survey Database Schema
-- Supports dynamic survey questions and flexible response storage

CREATE DATABASE IF NOT EXISTS library_survey;
USE library_survey;

-- ============================================
-- Admin Users Table
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
);

-- ============================================
-- Survey Categories Table
-- ============================================
CREATE TABLE IF NOT EXISTS survey_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_category (category_name),
    INDEX idx_display_order (display_order)
);

-- ============================================
-- Survey Questions Table (Dynamic)
-- ============================================
CREATE TABLE IF NOT EXISTS survey_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    question VARCHAR(500) NOT NULL,
    question_type ENUM('text', 'textarea', 'select', 'radio', 'checkbox', 'rating') NOT NULL DEFAULT 'text',
    options JSON,
    is_required BOOLEAN DEFAULT 1,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY fk_category (category_id) REFERENCES survey_categories(id),
    INDEX idx_category_active (category_id, is_active),
    INDEX idx_display_order (display_order)
);

-- ============================================
-- Survey Responses Table
-- ============================================
CREATE TABLE IF NOT EXISTS survey_responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    visitor_name VARCHAR(100) NOT NULL,
    visitor_email VARCHAR(100),
    visit_frequency VARCHAR(50),
    purpose VARCHAR(255),
    responses_data JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_response_date (created_at),
    INDEX idx_visitor_email (visitor_email),
    INDEX idx_visit_frequency (visit_frequency)
);

-- ============================================
-- Sample Data - Categories
-- ============================================
INSERT IGNORE INTO survey_categories (id, category_name, description, display_order) VALUES
(1, 'About You', 'Visitor personal information', 1),
(2, 'Your Visit', 'Details about library visit', 2),
(3, 'Feedback', 'Library satisfaction ratings and feedback', 3),
(4, 'Additional Feedback', 'Additional comments and suggestions', 4);

-- ============================================
-- Sample Data - Default Survey Questions
-- ============================================
INSERT IGNORE INTO survey_questions (id, category_id, question, question_type, is_required, display_order, options, is_active) VALUES
(1, 1, 'Full Name', 'text', 1, 1, NULL, 1),
(2, 1, 'Email Address', 'text', 0, 2, NULL, 1),
(3, 2, 'How often do you visit the library?', 'select', 1, 3, '["Daily", "Weekly", "Monthly", "Occasionally", "First time"]', 1),
(4, 2, 'What was the primary purpose of your visit?', 'text', 1, 4, NULL, 1),
(5, 3, 'Overall library satisfaction', 'rating', 1, 5, NULL, 1),
(6, 3, 'Book availability and collection', 'rating', 1, 6, NULL, 1),
(7, 3, 'Staff helpfulness and knowledge', 'rating', 1, 7, NULL, 1),
(8, 3, 'Facilities (cleanliness, comfort, equipment)', 'rating', 1, 8, NULL, 1),
(9, 3, 'Would you recommend this library to others?', 'select', 1, 9, '["Definitely Not", "Probably Not", "Neutral", "Probably Yes", "Definitely Yes"]', 1),
(10, 4, 'What can we improve?', 'textarea', 0, 10, NULL, 1);
