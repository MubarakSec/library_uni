<?php
/**
 * Advanced Search API
 * Provides filtered search with sorting options
 * Params: q (search), category, level, min_rating, sort_by, page
 */

require __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

// Get parameters
$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$level = trim($_GET['level'] ?? '');
$minRating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : 0;
$sortBy = $_GET['sort_by'] ?? 'date'; // date, rating, title
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build WHERE clauses
$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = '(title LIKE ? OR author LIKE ? OR category LIKE ?)';
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if ($category !== '') {
    $whereClauses[] = 'category = ?';
    $params[] = $category;
}

if ($level !== '') {
    $whereClauses[] = 'level = ?';
    $params[] = $level;
}

if ($minRating > 0) {
    $whereClauses[] = 'avg_rating >= ?';
    $params[] = $minRating;
}

$whereSQL = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Determine sort order
switch ($sortBy) {
    case 'rating':
        $orderBy = 'avg_rating DESC, review_count DESC';
        break;
    case 'title':
        $orderBy = 'title ASC';
        break;
    case 'date':
    default:
        $orderBy = 'created_at DESC';
        break;
}

// Get books
$fields = 'id, title, author, category, level, description, year, file_path, avg_rating, review_count, created_at';
$sql = "SELECT $fields FROM books $whereSQL ORDER BY $orderBy LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_merge($params, [$perPage, $offset]));
$books = $stmt->fetchAll();

// Get total count
$countSQL = "SELECT COUNT(*) as total FROM books $whereSQL";
$countStmt = $pdo->prepare($countSQL);
$countStmt->execute($params);
$total = $countStmt->fetch()['total'];

// Get available categories and levels for filters
$categories = $pdo->query('SELECT DISTINCT category FROM books WHERE category IS NOT NULL ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$levels = $pdo->query('SELECT DISTINCT level FROM books WHERE level IS NOT NULL ORDER BY level')->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'success' => true,
    'data' => $books,
    'pagination' => [
        'page' => $page,
        'perPage' => $perPage,
        'total' => $total,
        'totalPages' => ceil($total / $perPage)
    ],
    'filters' => [
        'categories' => $categories,
        'levels' => $levels
    ]
], JSON_UNESCAPED_UNICODE);
