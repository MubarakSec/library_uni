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

if ($title === '') {
    echo 'حقل العنوان مطلوب.';
    exit;
}

$stmt = $pdo->prepare('INSERT INTO book_requests (user_id, title, author, notes) VALUES (?, ?, ?, ?)');
$stmt->execute([
    current_user_id(),
    $title,
    $author ?: null,
    $notes ?: null,
]);

header('Location: /library_uni/front-end/pages/books.html');
exit;
