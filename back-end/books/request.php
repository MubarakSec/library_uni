<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../middleware/require-login.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /library_uni/front-end/pages/books.html');
    exit;
}

$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$notes = trim($_POST['notes'] ?? '');

// Validate title
if ($title === '') {
    echo 'حقل العنوان مطلوب.';
    exit;
}

// Check daily request limit (max 5 requests per day)
$userId = current_user_id();
$today = date('Y-m-d');

$stmt = $pdo->prepare('
    SELECT COUNT(*) as count FROM book_requests
    WHERE user_id = ? AND DATE(created_at) = ?
');
$stmt->execute([$userId, $today]);
$requestCount = $stmt->fetch()['count'];

if ($requestCount >= 5) {
    echo 'لقد وصلت للحد الأقصى من الطلبات اليومية (5 طلبات). يرجى المحاولة غداً.';
    exit;
}

// Insert request
$stmt = $pdo->prepare('INSERT INTO book_requests (user_id, title, author, notes) VALUES (?, ?, ?, ?)');
$stmt->execute([
    $userId,
    $title,
    $author ?: null,
    $notes ?: null,
]);

header('Location: /library_uni/front-end/pages/books.html');
exit;
