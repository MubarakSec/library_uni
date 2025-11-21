<?php
/**
 * Admin Dashboard API
 * Returns statistics and overview for admin panel
 * Requires: admin role
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../middleware/require-role.php';

require_role('admin');

header('Content-Type: application/json; charset=utf-8');

try {
    // Get user statistics
    $userStats = $pdo->query('
        SELECT 
            COUNT(*) as total_users,
            SUM(CASE WHEN role = "student" THEN 1 ELSE 0 END) as students,
            SUM(CASE WHEN role = "assistant" THEN 1 ELSE 0 END) as assistants,
            SUM(CASE WHEN role = "admin" THEN 1 ELSE 0 END) as admins
        FROM users
    ')->fetch();
    
    // Get book statistics
    $bookStats = $pdo->query('
        SELECT 
            COUNT(*) as total_books,
            AVG(avg_rating) as overall_avg_rating,
            SUM(review_count) as total_reviews
        FROM books
    ')->fetch();
    
    // Get request statistics
    $requestStats = $pdo->query('
        SELECT 
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed
        FROM book_requests
    ')->fetch();
    
    // Get most popular books (by reviews)
    $popularBooks = $pdo->query('
        SELECT id, title, author, avg_rating, review_count
        FROM books
        WHERE review_count > 0
        ORDER BY review_count DESC, avg_rating DESC
        LIMIT 5
    ')->fetchAll();
    
    // Get recent activity (latest reviews)
    $recentReviews = $pdo->query('
        SELECT 
            r.id,
            b.title as book_title,
            CONCAT(u.first_name, " ", u.last_name) as reviewer_name,
            r.rating,
            r.created_at
        FROM book_reviews r
        INNER JOIN books b ON r.book_id = b.id
        INNER JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
        LIMIT 10
    ')->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'users' => [
                'total' => (int)$userStats['total_users'],
                'students' => (int)$userStats['students'],
                'assistants' => (int)$userStats['assistants'],
                'admins' => (int)$userStats['admins']
            ],
            'books' => [
                'total' => (int)$bookStats['total_books'],
                'avg_rating' => $bookStats['overall_avg_rating'] ? round((float)$bookStats['overall_avg_rating'], 2) : null,
                'total_reviews' => (int)$bookStats['total_reviews']
            ],
            'requests' => [
                'total' => (int)$requestStats['total_requests'],
                'pending' => (int)$requestStats['pending'],
                'approved' => (int)$requestStats['approved'],
                'rejected' => (int)$requestStats['rejected'],
                'completed' => (int)$requestStats['completed']
            ],
            'popular_books' => $popularBooks,
            'recent_reviews' => $recentReviews
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ أثناء تحميل الإحصائيات'
    ], JSON_UNESCAPED_UNICODE);
}
