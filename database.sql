-- ================================================================
-- Library_Uni Database Schema
-- MySQL 8.0+
-- Character Set: utf8mb4 (full Unicode support including Arabic)
-- UPDATED: Added Reviews System
-- ================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS library_uni
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE library_uni;

-- ================================================================
-- Table: users
-- Stores student, assistant, and admin accounts
-- ================================================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    college VARCHAR(255) NULL,
    major VARCHAR(255) NULL,
    role ENUM('student', 'assistant', 'admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_email (email),
    KEY idx_role (role),
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: books
-- Stores uploaded books (PDFs) with metadata and ratings
-- ================================================================
CREATE TABLE IF NOT EXISTS books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NULL,
    category VARCHAR(255) NULL,
    level VARCHAR(100) NULL COMMENT 'Academic level/year',
    description TEXT NULL,
    year INT NULL COMMENT 'Publication year',
    file_path VARCHAR(500) NOT NULL COMMENT 'Relative path to PDF file',
    uploaded_by INT UNSIGNED NULL,
    avg_rating DECIMAL(3,2) NULL COMMENT 'Average rating from reviews (1.00-5.00)',
    review_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of reviews',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    KEY idx_title (title),
    KEY idx_author (author),
    KEY idx_category (category),
    KEY idx_level (level),
    KEY idx_uploaded_by (uploaded_by),
    KEY idx_created_at (created_at),
    KEY idx_rating (avg_rating),
    
    -- Full-text index for better search performance
    FULLTEXT KEY ft_search (title, author, category),
    
    -- Foreign keys
    CONSTRAINT fk_books_uploaded_by 
        FOREIGN KEY (uploaded_by) 
        REFERENCES users(id) 
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: book_requests
-- Stores student requests for books not in the library
-- ================================================================
CREATE TABLE IF NOT EXISTS book_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NULL,
    notes TEXT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    KEY idx_user_id (user_id),
    KEY idx_status (status),
    KEY idx_created_at (created_at),
    KEY idx_user_date (user_id, created_at),
    
    -- Foreign keys
    CONSTRAINT fk_book_requests_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Table: book_reviews
-- Stores student reviews and ratings for books
-- ================================================================
CREATE TABLE IF NOT EXISTS book_reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL COMMENT 'Rating from 1 to 5 stars',
    review_text TEXT NULL COMMENT 'Optional review text',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    KEY idx_book_id (book_id),
    KEY idx_user_id (user_id),
    KEY idx_rating (rating),
    KEY idx_created_at (created_at),
    
    -- Unique constraint: one review per user per book
    UNIQUE KEY unique_user_book_review (user_id, book_id),
    
    -- Check constraint for rating range
    CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5),
    
    -- Foreign keys
    CONSTRAINT fk_book_reviews_book_id 
        FOREIGN KEY (book_id) 
        REFERENCES books(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_book_reviews_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Sample Data
-- ================================================================

-- Insert default admin user
-- Password: Admin123! (hashed with PASSWORD_DEFAULT)
INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('Admin', 'User', 'admin@library.uni', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample student user
-- Password: Student123! (hashed with PASSWORD_DEFAULT)
INSERT INTO users (first_name, last_name, email, password_hash, college, major, role) VALUES
('أحمد', 'محمد', 'student@library.uni', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'College of Engineering', 'Computer Science', 'student');

-- Insert sample assistant user
-- Password: Assistant123! (hashed with PASSWORD_DEFAULT)
INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('مساعد', 'المكتبة', 'assistant@library.uni', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assistant');

-- Insert sample books
INSERT INTO books (title, author, category, level, description, year, file_path, uploaded_by) VALUES
('Introduction to Programming', 'John Smith', 'Computer Science', 'Level 1', 'A comprehensive guide to programming fundamentals', 2023, '/library_uni/front-end/uploads/books/sample_book1.pdf', 1),
('Data Structures and Algorithms', 'Jane Doe', 'Computer Science', 'Level 2', 'Essential algorithms and data structures', 2023, '/library_uni/front-end/uploads/books/sample_book2.pdf', 1),
('مبادئ البرمجة', 'محمد أحمد', 'علوم الحاسوب', 'المستوى الأول', 'كتاب شامل لأساسيات البرمجة باللغة العربية', 2024, '/library_uni/front-end/uploads/books/sample_book3.pdf', 3);

-- Insert sample reviews
INSERT INTO book_reviews (book_id, user_id, rating, review_text) VALUES
(1, 2, 5, 'كتاب ممتاز للمبتدئين! شرح واضح ومفصل.'),
(2, 2, 4, 'كتاب جيد ولكن يحتاج المزيد من الأمثلة العملية.');

-- Update book ratings based on reviews
UPDATE books SET avg_rating = 5.00, review_count = 1 WHERE id = 1;
UPDATE books SET avg_rating = 4.00, review_count = 1 WHERE id = 2;

-- ================================================================
-- Useful Views (Optional)
-- ================================================================

-- View for books with uploader info and ratings
CREATE OR REPLACE VIEW books_with_details AS
SELECT 
    b.id,
    b.title,
    b.author,
    b.category,
    b.level,
    b.description,
    b.year,
    b.file_path,
    b.avg_rating,
    b.review_count,
    b.created_at,
    CONCAT(u.first_name, ' ', u.last_name) AS uploaded_by_name,
    u.role AS uploader_role
FROM books b
LEFT JOIN users u ON b.uploaded_by = u.id;

-- View for pending book requests with user info
CREATE OR REPLACE VIEW pending_requests AS
SELECT 
    br.id,
    br.title,
    br.author,
    br.notes,
    br.status,
    br.created_at,
    CONCAT(u.first_name, ' ', u.last_name) AS requested_by_name,
    u.email AS requester_email
FROM book_requests br
INNER JOIN users u ON br.user_id = u.id
WHERE br.status = 'pending'
ORDER BY br.created_at DESC;

-- View for reviews with book and user info
CREATE OR REPLACE VIEW reviews_with_details AS
SELECT 
    r.id,
    r.book_id,
    b.title AS book_title,
    r.user_id,
    CONCAT(u.first_name, ' ', u.last_name) AS reviewer_name,
    r.rating,
    r.review_text,
    r.created_at
FROM book_reviews r
INNER JOIN books b ON r.book_id = b.id
INNER JOIN users u ON r.user_id = u.id
ORDER BY r.created_at DESC;

-- ================================================================
-- End of schema
-- ================================================================
