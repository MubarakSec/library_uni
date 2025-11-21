<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../middleware/require-role.php';

require_any_role(['assistant', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /library_uni/front-end/pages/upload-book.html');
    exit;
}

$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$category = trim($_POST['category'] ?? '');
$level = trim($_POST['level'] ?? '');
$year = trim($_POST['year'] ?? '');
$description = trim($_POST['description'] ?? '');

$errors = [];

// Validate title
if ($title === '') {
    $errors[] = 'عنوان الكتاب مطلوب.';
}

// Validate file upload
if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
    $errors[] = 'ملف الكتاب (PDF) مطلوب.';
}

// Validate year
if ($year !== '' && !is_numeric($year)) {
    $errors[] = 'قيمة السنة غير صالحة.';
}

// File validation
if (!$errors && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $errors[] = 'حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت.';
    }
    
    // Check file type using MIME
    $allowedMime = 'application/pdf';
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if ($mimeType !== $allowedMime) {
        $errors[] = 'يجب أن يكون الملف بصيغة PDF.';
    }
    
    // Double check extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        $errors[] = 'امتداد الملف يجب أن يكون .pdf';
    }
}

if ($errors) {
    echo 'أخطاء في رفع الكتاب: ' . implode(' | ', $errors);
    exit;
}

// Create upload directory if needed
$uploadDir = realpath(__DIR__ . '/../../front-end/uploads/books');
if ($uploadDir === false) {
    $uploadDir = __DIR__ . '/../../front-end/uploads/books';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
        echo 'Upload error: تعذر إنشاء مجلد الرفع.';
        exit;
    }
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'pdf';
$filename = uniqid('book_', true) . '.' . $extension;
$targetPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo 'Upload error: فشل رفع الملف.';
    exit;
}

// Save to database
$filePathForDb = '/library_uni/front-end/uploads/books/' . $filename;

$stmt = $pdo->prepare('INSERT INTO books (title, author, category, level, description, year, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $title,
    $author ?: null,
    $category ?: null,
    $level ?: null,
    $description ?: null,
    $year !== '' ? (int) $year : null,
    $filePathForDb,
    current_user_id(),
]);

header('Location: /library_uni/front-end/pages/books.html');
exit;
