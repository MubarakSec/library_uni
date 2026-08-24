<?php
/**
 * User Profile API
 * GET: Returns current user details
 * POST: Updates profile (first_name, last_name, college, major, current_password, new_password)
 */

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../middleware/require-login.php';

header('Content-Type: application/json; charset=utf-8');

$userId = current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('
        SELECT id, first_name, last_name, email, college, major, role, created_at
        FROM users
        WHERE id = ?
    ');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'المستخدم غير موجود'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $user
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verify_csrf_token($csrf)) {
        echo json_encode(['success' => false, 'error' => 'رمز التحقق الأمني (CSRF) غير صالح'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $college = trim($_POST['college'] ?? '');
    $major = trim($_POST['major'] ?? '');
    $currentPass = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';

    if ($first === '' || $last === '') {
        echo json_encode(['success' => false, 'error' => 'الاسم الأول واسم العائلة مطلوبان'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Password change request
    if ($newPass !== '') {
        if (strlen($newPass) < 8) {
            echo json_encode(['success' => false, 'error' => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $currHash = $stmt->fetch()['password_hash'] ?? '';

        if (!password_verify($currentPass, $currHash)) {
            echo json_encode(['success' => false, 'error' => 'كلمة المرور الحالية غير صحيحة'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare('
            UPDATE users
            SET first_name = ?, last_name = ?, college = ?, major = ?, password_hash = ?
            WHERE id = ?
        ');
        $updateStmt->execute([$first, $last, $college ?: null, $major ?: null, $newHash, $userId]);
    } else {
        $updateStmt = $pdo->prepare('
            UPDATE users
            SET first_name = ?, last_name = ?, college = ?, major = ?
            WHERE id = ?
        ');
        $updateStmt->execute([$first, $last, $college ?: null, $major ?: null, $userId]);
    }

    $_SESSION['user_name'] = $first;

    echo json_encode([
        'success' => true,
        'message' => 'تم تحديث البيانات بنجاح'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
