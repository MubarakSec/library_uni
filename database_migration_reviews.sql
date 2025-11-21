-- ================================================================
-- Migration Script: Add Reviews System
-- Run this if you already have the database set up
-- ================================================================

USE library_uni;

-- Add rating columns to books table (if they don't exist)
ALTER TABLE books 
ADD COLUMN IF NOT EXISTS avg_rating DECIMAL(3,2) NULL COMMENT 'Average rating from reviews (1.00-5.00)',
ADD COLUMN IF NOT EXISTS review_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of reviews',
ADD INDEX IF NOT EXISTS idx_rating (avg_rating);

-- Create book_reviews table
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
-- End of Migration
-- ================================================================
