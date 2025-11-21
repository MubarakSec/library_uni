<?php
/**
 * Database Connection Test
 * Tests if the database configuration is working
 */

echo "=== Library_Uni Database Connection Test ===\n\n";

// Check if .env file exists
if (!file_exists(__DIR__ . '/../.env')) {
    die("❌ ERROR: .env file not found!\n   Please copy .env.example to .env and configure it.\n");
}

echo "✓ .env file found\n";

// Load environment
require_once __DIR__ . '/../back-end/config/env.php';

try {
    EnvLoader::load(__DIR__ . '/../');
    echo "✓ Environment variables loaded\n";
} catch (Exception $e) {
    die("❌ ERROR loading .env: " . $e->getMessage() . "\n");
}

// Test database connection
echo "\n--- Testing Database Connection ---\n";

try {
    require __DIR__ . '/../back-end/config/db.php';
    echo "✓ Database connection successful!\n";
    
    // Check database name
    $stmt = $pdo->query('SELECT DATABASE() as db_name');
    $result = $stmt->fetch();
    echo "✓ Connected to database: " . $result['db_name'] . "\n";
    
    // Check tables exist
    echo "\n--- Checking Tables ---\n";
    $tables = ['users', 'books', 'book_requests', 'book_reviews'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists\n";
            
            // Count records
            $count = $pdo->query("SELECT COUNT(*) as cnt FROM $table")->fetch();
            echo "  └─ Records: " . $count['cnt'] . "\n";
        } else {
            echo "❌ Table '$table' NOT FOUND!\n";
        }
    }
    
    // Check views
    echo "\n--- Checking Views ---\n";
    $views = ['books_with_details', 'pending_requests', 'reviews_with_details'];
    
    foreach ($views as $view) {
        $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_" . $result['db_name'] . " = '$view'");
        if ($stmt->rowCount() > 0) {
            echo "✓ View '$view' exists\n";
        } else {
            echo "⚠ View '$view' not found (optional)\n";
        }
    }
    
    echo "\n✅ All basic tests passed!\n";
    echo "\nYou can now start the PHP server:\n";
    echo "  cd " . dirname(__DIR__) . "\n";
    echo "  php -S localhost:8000\n";
    
} catch (PDOException $e) {
    echo "❌ DATABASE ERROR: " . $e->getMessage() . "\n\n";
    echo "Common fixes:\n";
    echo "1. Check MySQL is running\n";
    echo "2. Verify credentials in .env file\n";
    echo "3. Make sure database 'library_uni' exists\n";
    echo "4. Run: mysql -u root -p < database.sql\n";
}
