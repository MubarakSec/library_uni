<?php
/**
 * Database Import Script
 * Imports database.sql directly using PHP PDO
 */

echo "=== Library_Uni Database Import ===\n\n";

// Load environment
require_once __DIR__ . '/../back-end/config/env.php';

try {
    EnvLoader::load(__DIR__ . '/../');
    echo "✓ Environment variables loaded\n";
} catch (Exception $e) {
    die("❌ ERROR: " . $e->getMessage() . "\n");
}

$host = EnvLoader::get('DB_HOST', 'localhost');
$user = EnvLoader::get('DB_USER', 'root');
$pass = EnvLoader::get('DB_PASS', '');

echo "\nConnecting to MySQL server...\n";

try {
    // Connect to MySQL server (without specifying database)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "✓ Connected to MySQL server\n\n";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/../database.sql';
    
    if (!file_exists($sqlFile)) {
        die("❌ ERROR: database.sql not found!\n");
    }
    
    echo "Reading database.sql...\n";
    $sql = file_get_contents($sqlFile);
    
    if ($sql === false) {
        die("❌ ERROR: Could not read database.sql\n");
    }
    
    echo "✓ SQL file read successfully (" . strlen($sql) . " bytes)\n\n";
    
    // Execute SQL using multi-statement
    echo "Executing SQL statements...\n";
    echo "This may take a few seconds...\n\n";
    
    try {
        // Enable multi-statement execution
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
        $pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, 1);
        
        // Execute all SQL at once
        $pdo->exec($sql);
        
        echo "✓ SQL executed successfully\n\n";
        
    } catch (PDOException $e) {
        echo "❌ Execution error: " . $e->getMessage() . "\n";
        echo "\nTrying alternative method...\n\n";
        
        // Split and execute one by one
        $statements = explode(';', $sql);
        $executed = 0;
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            try {
                $pdo->exec($statement);
                $executed++;
                
                // Show progress for major statements
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    preg_match('/CREATE TABLE[^`]*`?(\w+)`?/i', $statement, $matches);
                    if (isset($matches[1])) {
                        echo "  ✓ Created table: {$matches[1]}\n";
                    }
                } elseif (stripos($statement, 'CREATE DATABASE') !== false) {
                    echo "  ✓ Created database: library_uni\n";
                } elseif (stripos($statement, 'INSERT INTO') !== false) {
                    preg_match('/INSERT INTO[^`]*`?(\w+)`?/i', $statement, $matches);
                    if (isset($matches[1])) {
                        echo "  ✓ Inserted data into: {$matches[1]}\n";
                    }
                }
            } catch (PDOException $e2) {
                // Ignore "already exists" errors
                if (stripos($e2->getMessage(), 'already exists') === false &&
                    stripos($e2->getMessage(), 'Duplicate') === false) {
                    echo "  ⚠ Warning: " . $e2->getMessage() . "\n";
                }
            }
        }
        
        echo "\nStatements processed: $executed\n";
    }
    
    // Verify database was created
    echo "\n--- Verification ---\n";
    $pdo->exec("USE library_uni");
    echo "✓ Database 'library_uni' is accessible\n";
    
    // Check tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Tables created: " . count($tables) . "\n";
    
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "  - $table: $count rows\n";
    }
    
    echo "\n✅ SUCCESS! Database imported successfully!\n\n";
    echo "Next step: Test the connection\n";
    echo "  php tests\\test-db-connection.php\n\n";
    
} catch (PDOException $e) {
    echo "❌ DATABASE ERROR: " . $e->getMessage() . "\n\n";
    echo "Common fixes:\n";
    echo "1. Check MySQL is running\n";
    echo "2. Verify credentials in .env file\n";
    echo "3. Make sure MySQL user has CREATE DATABASE privileges\n";
}
