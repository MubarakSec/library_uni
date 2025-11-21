<?php
/**
 * Book Request Handler
 * Allows logged-in users to request books not in the library
 * Rate limited to 5 requests per day per user
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../middleware/require-login.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /library_uni/front-end/pages/books.html');
    exit;
}

// Get form data
$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$notes = trim($_POST['notes'] ?? '');

// Validate required field: title
if ($title === '') {
    echo '<div style="color: red; padding: 20px; direction: rtl;">حقل العنوان مطلوب.</div>';
    exit;
}

// Check daily request limit (max 5 requests per day per user)
$userId = current_user_id();
$today = date('Y-m-d');

$stmt = $pdo->prepare('
    SELECT COUNT(*) as count FROM book_requests
    WHERE user_id = ? AND DATE(created_at) = ?
');
$stmt->execute([$userId, $today]);
$requestCount = $stmt->fetch()['count'];

if ($requestCount >= 5) {
    echo '<div style="color: red; padding: 20px; direction: rtl;">لقد وصلت للحد الأقصى من الطلبات اليومية (5 طلبات). يرجى المحاولة غداً.</div>';
    exit;
}

// FIX: INSERT matches database.sql schema for book_requests
// Columns: user_id, title, author, notes (status defaults to 'pending')
$stmt = $pdo->prepare('
    INSERT INTO book_requests (user_id, title, author, notes) 
    VALUES (?, ?, ?, ?)
');
$stmt->execute([
    $userId,
    $title,
    $author ?: null,  // NULL if empty
    $notes ?: null,   // NULL if empty
]);

// Redirect back to books page
header('Location: /library_uni/front-end/pages/books.html');
exit;

