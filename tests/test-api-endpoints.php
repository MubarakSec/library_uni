<?php
/**
 * API Endpoints Test
 * Tests major API endpoints to ensure they work correctly
 */

echo "=== Library_Uni API Endpoints Test ===\n\n";

// Helper function to test API
function testAPI($name, $url, $method = 'GET', $postData = null) {
    echo "Testing: $name\n";
    echo "  URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    if ($method === 'POST' && $postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "  ✓ Status: $httpCode - Response is valid JSON\n";
            if (isset($data['success'])) {
                echo "  ✓ Success: " . ($data['success'] ? 'true' : 'false') . "\n";
            }
            return true;
        } else {
            echo "  ⚠ Status: $httpCode - Response is not JSON\n";
            return false;
        }
    } else {
        echo "  ❌ Status: $httpCode - Request failed\n";
        return false;
    }
}

echo "NOTE: These tests require the PHP server to be running!\n";
echo "Start with: php -S localhost:8000\n\n";

$baseUrl = 'http://localhost:8000/library_uni/back-end';

// Check if server is running
$ch = curl_init($baseUrl . '/books/list.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
$result = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    die("❌ Cannot connect to PHP server!\n   Please start it with: php -S localhost:8000\n\n");
}

echo "✓ PHP server is running\n\n";

// Test endpoints
echo "--- Testing Public Endpoints ---\n";
testAPI("Books List", $baseUrl . '/books/list.php');
testAPI("Books List (paginated)", $baseUrl . '/books/list.php?page=1');
testAPI("Books Search", $baseUrl . '/books/search.php?q=programming');
testAPI("Book Reviews", $baseUrl . '/books/reviews.php?book_id=1');

echo "\n--- Testing Endpoints (Require Login) ---\n";
echo "⚠ Following tests will likely fail without authentication:\n";
testAPI("Add Review", $baseUrl . '/books/add-review.php', 'POST', [
    'book_id' => 1,
    'rating' => 5,
    'review_text' => 'Test review'
]);
testAPI("Admin Dashboard", $baseUrl . '/admin/dashboard.php');

echo "\n✅ Basic API tests complete!\n";
echo "\nTo test authenticated endpoints:\n";
echo "1. Login through the web interface\n";
echo "2. Session will be created automatically\n";
