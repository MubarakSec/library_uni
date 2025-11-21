<?php
require __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$search = trim($_GET['q'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Fields to select (instead of SELECT *)
$fields = 'id, title, author, category, level, description, year, file_path, created_at';

if ($search !== '') {
    // Search query with pagination
    $stmt = $pdo->prepare("
        SELECT $fields FROM books
        WHERE title LIKE ? OR author LIKE ? OR category LIKE ?
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    $like = "%$search%";
    $stmt->execute([$like, $like, $like, $perPage, $offset]);
    
    // Get total count for pagination
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM books
        WHERE title LIKE ? OR author LIKE ? OR category LIKE ?
    ");
    $countStmt->execute([$like, $like, $like]);
    $total = $countStmt->fetch()['total'];
} else {
    // Get all books with pagination
    $stmt = $pdo->prepare("SELECT $fields FROM books ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$perPage, $offset]);
    
    // Get total count
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM books");
    $total = $countStmt->fetch()['total'];
}

$books = $stmt->fetchAll();

// Return data with pagination info
echo json_encode([
    'success' => true,
    'data' => $books,
    'pagination' => [
        'page' => $page,
        'perPage' => $perPage,
        'total' => $total,
        'totalPages' => ceil($total / $perPage)
    ]
]);
