<?php
/**
 * Add or Update Book Review
 * Allows logged-in users to submit a review for a book
 * POST: book_id, rating (1-5), review_text (optional)
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../middleware/require-login.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'طريقة طلب غير صحيحة'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$reviewText = trim($_POST['review_text'] ?? '');
$userId = current_user_id();

// Validate inputs
if ($bookId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'معرف الكتاب مطلوب'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode([
        'success' => false,
        'error' => 'التقييم يجب أن يكون بين 1 و 5'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Check if book exists
$checkBook = $pdo->prepare('SELECT id FROM books WHERE id = ?');
$checkBook->execute([$bookId]);
if (!$checkBook->fetch()) {
    echo json_encode([
        'success' => false,
        'error' => 'الكتاب غير موجود'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Check if user already reviewed this book
    $existing = $pdo->prepare('SELECT id FROM book_reviews WHERE user_id = ? AND book_id = ?');
    $existing->execute([$userId, $bookId]);
    
    if ($existing->fetch()) {
        // Update existing review
        $stmt = $pdo->prepare('
            UPDATE book_reviews 
            SET rating = ?, review_text = ?, updated_at = CURRENT_TIMESTAMP
            WHERE user_id = ? AND book_id = ?
        ');
        $stmt->execute([$rating, $reviewText ?: null, $userId, $bookId]);
        $message = 'تم تحديث تقييمك بنجاح';
    } else {
        // Insert new review
        $stmt = $pdo->prepare('
            INSERT INTO book_reviews (book_id, user_id, rating, review_text) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$bookId, $userId, $rating, $reviewText ?: null]);
        $message = 'تم إضافة تقييمك بنجاح';
    }
    
    // Update book's average rating and review count
    $updateBook = $pdo->prepare('
        UPDATE books 
        SET avg_rating = (SELECT AVG(rating) FROM book_reviews WHERE book_id = ?),
            review_count = (SELECT COUNT(*) FROM book_reviews WHERE book_id = ?)
        WHERE id = ?
    ');
    $updateBook->execute([$bookId, $bookId, $bookId]);
    
    echo json_encode([
        'success' => true,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Review submission error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'حدث خطأ أثناء حفظ التقييم'
    ], JSON_UNESCAPED_UNICODE);
}
