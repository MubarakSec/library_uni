<?php
/**
 * Manage Book Requests API
 * Allows admins to view, approve, or reject book requests
 * GET: list all requests
 * POST: update request status (book_request_id, status)
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../middleware/require-role.php';

require_role('admin');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get all requests with user info
    $status = $_GET['status'] ?? null;
    
    if ($status && in_array($status, ['pending', 'approved', 'rejected', 'completed'])) {
        $stmt = $pdo->prepare('
            SELECT 
                br.id,
                br.title,
                br.author,
                br.notes,
                br.status,
                br.created_at,
                CONCAT(u.first_name, " ", u.last_name) as requested_by,
                u.email as requester_email
            FROM book_requests br
            INNER JOIN users u ON br.user_id = u.id
            WHERE br.status = ?
            ORDER BY br.created_at DESC
        ');
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->query('
            SELECT 
                br.id,
                br.title,
                br.author,
                br.notes,
                br.status,
                br.created_at,
                CONCAT(u.first_name, " ", u.last_name) as requested_by,
                u.email as requester_email
            FROM book_requests br
            INNER JOIN users u ON br.user_id = u.id
            ORDER BY br.created_at DESC
        ');
    }
    
    $requests = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $requests
    ], JSON_UNESCAPED_UNICODE);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update request status
    $requestId = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
    $newStatus = $_POST['status'] ?? '';
    
    if ($requestId <= 0 || !in_array($newStatus, ['pending', 'approved', 'rejected', 'completed'])) {
        echo json_encode([
            'success' => false,
            'error' => 'بيانات غير صحيحة'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $stmt = $pdo->prepare('UPDATE book_requests SET status = ? WHERE id = ?');
    $stmt->execute([$newStatus, $requestId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'تم تحديث حالة الطلب بنجاح'
    ], JSON_UNESCAPED_UNICODE);
}
