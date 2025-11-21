<?php
require __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare('
        SELECT * FROM books
        WHERE title LIKE ? OR author LIKE ? OR category LIKE ?
        ORDER BY created_at DESC
    ');
    $like = "%$search%";
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query('SELECT * FROM books ORDER BY created_at DESC');
}

$books = $stmt->fetchAll();
echo json_encode($books);
