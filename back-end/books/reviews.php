<?php
/**
 * Get Reviews for a Book
 * Returns all reviews for a specific book with reviewer information
 * Usage: GET /back-end/books/reviews.php?book_id=1
 */

require __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$bookId = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;

if ($bookId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'معرف الكتاب مطلوب'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Get reviews with user info using the view
$stmt = $pdo->prepare('
    SELECT 
        r.id,
        r.user_id,
        CONCAT(u.first_name, " ", u.last_name) AS reviewer_name,
        r.rating,
        r.review_text,
        r.created_at
    FROM book_reviews r
    INNER JOIN users u ON r.user_id = u.id
    WHERE r.book_id = ?
    ORDER BY r.created_at DESC
');
$stmt->execute([$bookId]);
$reviews = $stmt->fetchAll();

// Get review statistics
$statsStmt = $pdo->prepare('
    SELECT 
        COUNT(*) as total_reviews,
        AVG(rating) as average_rating,
        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
    FROM book_reviews
    WHERE book_id = ?
');
$statsStmt->execute([$bookId]);
$stats = $statsStmt->fetch();

echo json_encode([
    'success' => true,
    'data' => [
        'reviews' => $reviews,
        'statistics' => [
            'total' => (int)$stats['total_reviews'],
            'average' => $stats['average_rating'] ? round((float)$stats['average_rating'], 2) : null,
            'distribution' => [
                5 => (int)$stats['five_stars'],
                4 => (int)$stats['four_stars'],
                3 => (int)$stats['three_stars'],
                2 => (int)$stats['two_stars'],
                1 => (int)$stats['one_star']
            ]
        ]
    ]
], JSON_UNESCAPED_UNICODE);
