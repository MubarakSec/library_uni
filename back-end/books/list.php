<?php
require __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$level = trim($_GET['level'] ?? '');
$year = trim($_GET['year'] ?? '');

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(title LIKE ? OR author LIKE ? OR category LIKE ?)';
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($category !== '') {
    $where[] = 'category = ?';
    $params[] = $category;
}

if ($level !== '') {
    $where[] = 'level = ?';
    $params[] = $level;
}

if ($year !== '' && is_numeric($year)) {
    $where[] = 'year = ?';
    $params[] = (int) $year;
}

$sql = 'SELECT * FROM books';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll());
