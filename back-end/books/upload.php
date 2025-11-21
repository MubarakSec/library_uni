<?php
/**
 * Book Upload Handler
 * Allows assistants and admins to upload book PDFs with metadata
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../middleware/require-role.php';

// Only assistants and admins can upload books
require_any_role(['assistant', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /library_uni/front-end/pages/upload-book.html');
    exit;
}

// Retrieve and sanitize form data
$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$category = trim($_POST['category'] ?? '');
$level = trim($_POST['level'] ?? '');
$year = trim($_POST['year'] ?? '');
$description = trim($_POST['description'] ?? '');

$errors = [];

// Validate required field: title
if ($title === '') {
    $errors[] = 'عنوان الكتاب مطلوب.';
}

// Validate file upload
if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
    $errors[] = 'ملف الكتاب (PDF) مطلوب.';
}

// Validate year (must be numeric if provided)
if ($year !== '' && !is_numeric($year)) {
    $errors[] = 'قيمة السنة غير صالحة.';
}

// File validation
if (!$errors && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $maxSize = 10 * 1024 * 1024; // 10MB max file size
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $errors[] = 'حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت.';
    }
    
    // Check MIME type
    $allowedMime = 'application/pdf';
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if ($mimeType !== $allowedMime) {
        $errors[] = 'يجب أن يكون الملف بصيغة PDF.';
    }
    
    // Double-check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        $errors[] = 'امتداد الملف يجب أن يكون .pdf';
    }
}

// Display errors if any
if ($errors) {
    echo '<div style="color: red; padding: 20px; direction: rtl;">';
    echo '<h3>أخطاء في رفع الكتاب:</h3><ul>';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul></div>';
    exit;
}

// Create upload directory if it doesn't exist
$uploadDir = realpath(__DIR__ . '/../../front-end/uploads/books');
if ($uploadDir === false) {
    $uploadDir = __DIR__ . '/../../front-end/uploads/books';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
        echo 'Upload error: تعذر إنشاء مجلد الرفع.';
        exit;
    }
}

// Generate unique filename to avoid conflicts
$extension = 'pdf';
$filename = uniqid('book_', true) . '.' . $extension;
$targetPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

// Move uploaded file to target directory
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo 'Upload error: فشل رفع الملف.';
    exit;
}

// Prepare file path for database (relative to web root)
$filePathForDb = '/library_uni/front-end/uploads/books/' . $filename;

// FIX: INSERT statement matches database.sql schema exactly
// Columns: title, author, category, level, description, year, file_path, uploaded_by
$stmt = $pdo->prepare('
    INSERT INTO books (title, author, category, level, description, year, file_path, uploaded_by) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
');
$stmt->execute([
    $title,
    $author ?: null,        // NULL if empty
    $category ?: null,      // NULL if empty
    $level ?: null,         // NULL if empty
    $description ?: null,   // NULL if empty
    $year !== '' ? (int)$year : null,  // INT or NULL
    $filePathForDb,
    current_user_id(),      // ID of the user who uploaded the book
]);

// Redirect back to books page
header('Location: /library_uni/front-end/pages/books.html');
exit;

