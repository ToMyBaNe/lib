-- Library Survey Database Schema

CREATE DATABASE IF NOT EXISTS library_survey;
USE library_survey;

-- Users table for admin authentication
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Survey responses table
CREATE TABLE survey_responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    visitor_name VARCHAR(100) NOT NULL,
    visitor_email VARCHAR(100),
    visit_frequency VARCHAR(50) NOT NULL,
    purpose VARCHAR(255),
    satisfaction INT NOT NULL,
    book_availability INT NOT NULL,
    staff_helpfulness INT NOT NULL,
    facilities_rating INT NOT NULL,
    would_recommend INT NOT NULL,
    improvements_feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Survey categories table for reference data
CREATE TABLE survey_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data for categories
INSERT INTO survey_categories (category_name, description) VALUES
('Frequency', 'How often visitors use the library'),
('Purpose', 'Reasons for visiting the library'),
('Rating', 'Satisfaction ratings');

-- Create indexes for better query performance
CREATE INDEX idx_response_date ON survey_responses(created_at);
CREATE INDEX idx_response_frequency ON survey_responses(visit_frequency);
CREATE INDEX idx_response_email ON survey_responses(visitor_email);
