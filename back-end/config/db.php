<?php
// Load environment variables using our simple loader
require_once __DIR__ . '/env.php';
EnvLoader::load(__DIR__ . '/../../');

// Database configuration from environment
$host = EnvLoader::get('DB_HOST', 'localhost');
$db   = EnvLoader::get('DB_NAME', 'library_uni');
$user = EnvLoader::get('DB_USER', 'root');
$pass = EnvLoader::get('DB_PASS', '');

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, // Security: Use real prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Log error to file instead of displaying
    error_log("Database connection failed: " . $e->getMessage());
    
    // Show generic error to user
    if (EnvLoader::get('APP_ENV', 'production') === 'development') {
        die("Database connection failed: " . $e->getMessage());
    } else {
        die("عذراً، حدث خطأ في الاتصال بقاعدة البيانات. يرجى المحاولة لاحقاً.");
    }
}
